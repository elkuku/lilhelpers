<?php
/**
 * Generic_Sniffs_NamingConventions_ConstructorNameSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Leif Wickland <lwickland@rightnow.com>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: ConstructorNameSniff.php 8 2010-11-06 00:40:23Z elkuku $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

if(class_exists('PHP_CodeSniffer_Standards_AbstractScopeSniff', true) === false)
{
    $error = 'Class PHP_CodeSniffer_Standards_AbstractScopeSniff not found';
    throw new PHP_CodeSniffer_Exception($error);
}

/**
 * Checks for a PHP5 constructor name.
 *
 * PHP 5 constructor syntax:
 * <b class="good">function __construct()</b>.
 *
 * PHP 4 constructor syntax:
 * <b class="bad">function ClassName()</b>.
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Leif Wickland <lwickland@rightnow.com>
 * @license  http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version  Release: 1.3.0RC1
 * @link     http://pear.php.net/package/PHP_CodeSniffer
 */
class KuKu_Sniffs_Classes_ConstructorNameSniff
extends PHP_CodeSniffer_Standards_AbstractScopeSniff
{
    /**
     * Constructs the test with the tokens it wishes to listen for.
     */
    public function __construct()
    {
        parent::__construct(array(T_CLASS, T_INTERFACE), array(T_FUNCTION), true);
    }//function

    /**
     * Processes this test when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The current file being scanned.
     * @param integer                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     * @param integer                  $currScope A pointer to the start of the scope.
     *
     * @return void
     */
    protected function processTokenWithinScope(PHP_CodeSniffer_File $phpcsFile,
    $stackPtr, $currScope
    )
    {
        $className  = $phpcsFile->getDeclarationName($currScope);
        $methodName = $phpcsFile->getDeclarationName($stackPtr);

        if(strcasecmp($methodName, $className) === 0)
        {
            $error = 'PHP4 style constructors are not allowed; use "__construct()" instead';
            $phpcsFile->addError($error, $stackPtr, 'OldStyle');
        }
        else if(strcasecmp($methodName, '__construct') !== 0)
        {
            // Not a constructor.
            return;
        }

        $tokens = $phpcsFile->getTokens();

        $parentClassName = $phpcsFile->findExtendedClassName($currScope);

        if($parentClassName === false)
        {
            return;
        }

        $endFunctionIndex = $tokens[$stackPtr]['scope_closer'];
        $startIndex       = $stackPtr;

        while($doubleColonIndex = $phpcsFile->findNext(array(T_DOUBLE_COLON)
        , $startIndex, $endFunctionIndex))
        {
            if($tokens[($doubleColonIndex + 1)]['code'] === T_STRING
            && $tokens[($doubleColonIndex + 1)]['content'] === $parentClassName
            )
            {
                $error = 'PHP4 style calls to parent constructors are not allowed;'
                .' use "parent::__construct()" instead';

                $phpcsFile->addError($error, ($doubleColonIndex + 1), 'OldStyleCall');
            }

            $startIndex = ($doubleColonIndex + 1);
        }//while
    }//function
}//class
