<?php
/**
 * KuKu_Sniffs_Functions_FunctionSignatureSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Nikolai Plath
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   SVN: $Id: ClosingWithCommentSniff.php 558 2011-10-08 16:06:10Z elkuku $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Closing structures with a comment.
 *
 * Checks that the closing brace of a structure is marked with the comment "//structure".
 *
 * <b class="bad">Bad:</b>
 * class foo()
 * {
 *     // blah
 * }
 *
 * <b class="good">Good:</b>
 * class foo()
 * {
 *     // blah
 * }<b class="good">//class</b>
 *
 * This rule applies for the structures:
 * <b>class, function, for, foreach, while, switch, try</b>
 * Exception:
 * <b>if</b><small> (...still)</small>
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
class KuKu_Sniffs_ControlStructures_ClosingWithCommentSniff implements PHP_CodeSniffer_Sniff
{
    protected $closers = array(
    T_CLASS => '//class'
    , T_FUNCTION => '//function'
    , T_FOR => '//for'
    , T_FOREACH => '//foreach'
    , T_SWITCH => '//switch'
    , T_CATCH => '//try'
    , T_WHILE => '//while'
    , T_IF => ''
    );

    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
        T_CLASS
        , T_FUNCTION

        , T_FOR
        , T_FOREACH
        , T_SWITCH
        , T_CATCH
        , T_WHILE
        , T_IF
        );
    }//function

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @disabled
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param integer $stackPtr The position of the current token in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if(isset($tokens[$stackPtr]['scope_opener']) === false)
        {
            return;
        }

	    //-- Disabled
	    return;

        $excpectedCloser = $this->closers[$tokens[$stackPtr]['code']];

        $closingBrace = $tokens[$stackPtr]['scope_closer'];

        if( ! isset($tokens[$closingBrace + 1]))
        {
            $error = sprintf('Please close a %s with the comment "%s".'
            , $tokens[$stackPtr]['content'], $excpectedCloser);

            $phpcsFile->addError($error, $closingBrace, 'FunctionClosing');

            return;
        }

        $closer = trim($tokens[$closingBrace + 1]['content']);

        if($closer != $excpectedCloser)
        {
            $error = sprintf('Please close a %s with the comment "%s"; found: "%s"'
            , $tokens[$stackPtr]['content'], $excpectedCloser, $closer);

            $phpcsFile->addError($error, $closingBrace, 'FunctionClosing');
        }
    }//function
}//class
