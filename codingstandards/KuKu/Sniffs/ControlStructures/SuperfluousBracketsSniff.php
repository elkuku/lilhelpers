<?php
/**
 * Squiz_Sniffs_Strings_EchoedStringsSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: SuperfluousBracketsSniff.php 8 2010-11-06 00:40:23Z elkuku $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Makes sure that any statement that don't need brackets don't have them.
 *
 * This applies to:
 * <b>echo, include, include_once, require, require_once</b>
 *
 * <b class="bad">include ('Foo/Bar');</b>
 * <b class="good">include 'Foo/Bar';</b>
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: 1.3.0RC1
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class KuKu_Sniffs_ControlStructures_SuperfluousBracketsSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_ECHO
        , T_INCLUDE
        , T_INCLUDE_ONCE
        , T_REQUIRE
        , T_REQUIRE_ONCE);
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

        $firstContent = $phpcsFile->findNext(array(T_WHITESPACE), ($stackPtr + 1), null, true);

        // If the first non-whitespace token is not an opening parenthesis, then we are not concerned.
        if($tokens[$firstContent]['code'] !== T_OPEN_PARENTHESIS)
        {
            return;
        }

        $endOfStatement = $phpcsFile->findNext(array(T_SEMICOLON), $stackPtr, null, false);

        // If the token before the semi-colon is not a closing parenthesis, then we are not concerned.
        if($tokens[($endOfStatement - 1)]['code'] !== T_CLOSE_PARENTHESIS)
        {
            return;
        }

        if(($phpcsFile->findNext(PHP_CodeSniffer_Tokens::$operators, $stackPtr, $endOfStatement, false)) === false)
        {
            // There are no arithmetic operators in this.
            $error = sprintf('The statement %s does not need brackets.'
            , $tokens[$stackPtr]['content']);

            $phpcsFile->addError($error, $stackPtr, 'HasBracket');
        }
    }//function
}//class
