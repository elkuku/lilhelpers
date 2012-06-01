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
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   CVS: $Id: EmptyStatementSniff.php 8 2010-11-06 00:40:23Z elkuku $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * This sniff detects empty statements.
 *
 * This sniff implements the common algorithm for empty statement body detection.
 * A body is considered as empty if it is completely empty or it only contains
 * whitespace characters and/or comments.
 *
 * stmt(condition)
 * {
 *     <b class="warn">// foo</b>
 * }
 *
 * catch(condition)
 * {
 *     <b class="bad">// foo</b>
 * }
 *
 * Statements covered by this sniff are:
 * <b class="bad">Errors:</b>
 * <b>catch</b>
 * <b class="warn">Warnings:</b>
 * <b>if</b>, <b>else</b>, <b>elsif</b>, <b>do</b>, <b>for</b>, <b>foreach</b>, <b>switch</b>, <b>try</b> and <b>while</b>.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2007-2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: 1.2.2
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class KuKu_Sniffs_CodeAnalysis_EmptyStatementSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * List of block tokens that this sniff covers.
     *
     * The key of this hash identifies the required token while the boolean
     * value says mark an error or mark a warning.
     *
     * @var array
     */
    protected $checkedTokens = array(
    T_CATCH   => true,
    T_DO      => false,
    T_ELSE    => false,
    T_ELSEIF  => false,
    T_FOR     => false,
    T_FOREACH => false,
    T_IF      => false,
    T_SWITCH  => false,
    T_TRY     => false,
    T_WHILE   => false,
    );

    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * @return array(integer)
     */
    public function register()
    {
        return array_keys($this->checkedTokens);
    }//function

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param integer $stackPtr  The position of the current token in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $token  = $tokens[$stackPtr];

        // Skip for-statements without body.
        if(isset($token['scope_opener']) === false)
        {
            return;
        }

        $next = ++$token['scope_opener'];
        $end  = --$token['scope_closer'];

        $emptyBody = true;

        for(; $next <= $end; ++$next)
        {
            if(in_array($tokens[$next]['code'], PHP_CodeSniffer_Tokens::$emptyTokens) === false)
            {
                $emptyBody = false;
                break;
            }
        }//for

        if($emptyBody === true)
        {
            // Get token identifier.
            $name  = $phpcsFile->getTokensAsString($stackPtr, 1);
            $error = sprintf('Empty %s statement detected', strtoupper($name));

            if($this->checkedTokens[$token['code']] === true)
            {
                $phpcsFile->addError($error, $stackPtr);
            }
            else
            {
                $phpcsFile->addWarning($error, $stackPtr);
            }
        }
    }//function
}//class
