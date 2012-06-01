<?php
/**
 * This file is part of the CodeAnalysis addon for PHP_CodeSniffer.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2007-2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD License
 * @version   CVS: $Id: UselessOverridingMethodSniff.php 8 2010-11-06 00:40:23Z elkuku $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Detects unnecessary overriden methods that simply call their parent.
 *
 * This rule is based on the PMD rule catalog. The Useless Overriding Method
 * sniff detects the use of methods that only call their parent classes's method
 * with the same name and arguments. These methods are not required.
 *
 * class FooBar
 * {
 *   <b class="bad">public function __construct($a, $b)</b>
 *   {
 *      <b class="bad">parent::__construct($a, $b);</b>
 *   }</b>
 * }
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2007-2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD License
 * @version   Release: 1.3.0RC1
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class KuKu_Sniffs_Classes_UselessOverridingMethodSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * @return array(integer)
     */
    public function register()
    {
        return array(T_FUNCTION);
    }//function

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param integer                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $token  = $tokens[$stackPtr];

        // Skip function without body.
        if(isset($token['scope_opener']) === false)
        {
            return;
        }

        // Get function name.
        $methodName = $phpcsFile->getDeclarationName($stackPtr);

        // Get all parameters from method signature.
        $signature = array();

        foreach($phpcsFile->getMethodParameters($stackPtr) as $param)
        {
            $signature[] = $param['name'];
        }//foreach

        $next = ++$token['scope_opener'];
        $end  = --$token['scope_closer'];

        for(; $next <= $end; ++$next)
        {
            $code = $tokens[$next]['code'];

            if(in_array($code, PHP_CodeSniffer_Tokens::$emptyTokens) === true)
            {
                continue;
            }
            else if($code === T_RETURN)
            {
                continue;
            }

            break;
        }//for

        // Any token except 'parent' indicates correct code.
        if($tokens[$next]['code'] !== T_PARENT)
        {
            return;
        }

        // Find next non empty token index, should be double colon.
        $next = $phpcsFile->findNext(PHP_CodeSniffer_Tokens::$emptyTokens, ($next + 1), null, true);

        // Skip for invalid code.
        if($next === false || $tokens[$next]['code'] !== T_DOUBLE_COLON)
        {
            return;
        }

        // Find next non empty token index, should be the function name.
        $next = $phpcsFile->findNext(PHP_CodeSniffer_Tokens::$emptyTokens, ($next + 1), null, true);

        // Skip for invalid code or other method.
        if($next === false || $tokens[$next]['content'] !== $methodName)
        {
            return;
        }

        // Find next non empty token index, should be the open parenthesis.
        $next = $phpcsFile->findNext(PHP_CodeSniffer_Tokens::$emptyTokens, ($next + 1), null, true);

        // Skip for invalid code.
        if($next === false || $tokens[$next]['code'] !== T_OPEN_PARENTHESIS)
        {
            return;
        }

        $validParameterTypes = array(
                                T_VARIABLE,
                                T_LNUMBER,
                                T_CONSTANT_ENCAPSED_STRING,
                               );

        $parameters       = array('');
        $parenthesisCount = 1;
        $count            = count($tokens);

        for(++$next; $next < $count; ++$next)
        {
            $code = $tokens[$next]['code'];

            if($code === T_OPEN_PARENTHESIS)
            {
                ++$parenthesisCount;
            }
            else if($code === T_CLOSE_PARENTHESIS)
            {
                --$parenthesisCount;
            }
            else if($parenthesisCount === 1 && $code === T_COMMA)
            {
                $parameters[] = '';
            }
            else if(in_array($code, PHP_CodeSniffer_Tokens::$emptyTokens) === false)
            {
                $parameters[(count($parameters) - 1)] .= $tokens[$next]['content'];
            }

            if($parenthesisCount === 0)
            {
                break;
            }
        }//for

        $next = $phpcsFile->findNext(PHP_CodeSniffer_Tokens::$emptyTokens, ($next + 1), null, true);

        if($next === false || $tokens[$next]['code'] !== T_SEMICOLON)
        {
            return;
        }

        // Check rest of the scope.
        for(++$next; $next <= $end; ++$next)
        {
            $code = $tokens[$next]['code'];
            // Skip for any other content.
            if(in_array($code, PHP_CodeSniffer_Tokens::$emptyTokens) === false)
            {
                return;
            }
        }//for

        $parameters = array_map('trim', $parameters);
        $parameters = array_filter($parameters);

        if(count($parameters) === count($signature) && $parameters === $signature)
        {
            $phpcsFile->addError('Useless method overriding detected', $stackPtr, 'Found');
        }
    }//function
}//class
