<?php
/**
 * Squiz_Sniffs_WhiteSpace_LanguageConstructSpacingSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: ParenthesisSpacingSniff.php 8 2010-11-06 00:40:23Z elkuku $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Squiz_Sniffs_WhiteSpace_LanguageConstructSpacingSniff.
 *
 * Ensures all language constructs (without brackets) contain a
 * single space between themselves and their content.
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
class KuKu_Sniffs_WhiteSpace_ParenthesisSpacingSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = array('PHP');

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
        T_OPEN_PARENTHESIS
        , T_CLOSE_PARENTHESIS
        );
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

        //        var_dump($tokens[$stackPtr]);
        //        die( '***********************');
        switch($tokens[$stackPtr]['code'])
        {
            case T_OPEN_PARENTHESIS:
                if($tokens[($stackPtr + 1)]['code'] === T_WHITESPACE)
                {
                    if($tokens[$stackPtr + 2]['code'] == T_BOOLEAN_NOT)
                    {
                        //-- a space befor a boolean not is ok
                        return;
                    }

                    if(strpos($tokens[($stackPtr + 1)]['content']
                    , $phpcsFile->eolChar) === false)
                    {
                        //-- If the space does not contain a new line character
                        $error = 'Opening braces should not be followed by a space';
                        $phpcsFile->addError($error, $stackPtr, 'IncorrectSpacing');
                    }
                }
                else if($tokens[($stackPtr + 1)]['code'] === T_BOOLEAN_NOT)
                {
                    $error = 'A boolean not must be preceded by a space';
                    $phpcsFile->addError($error, $stackPtr, 'IncorrectSpacing');
                }
                break;
            case T_CLOSE_PARENTHESIS:
                if($tokens[($stackPtr - 1)]['code'] === T_WHITESPACE)
                {
                    if(strpos($tokens[($stackPtr - 1)]['content']
                    , $phpcsFile->eolChar) === false
                    && strpos($tokens[($stackPtr - 2)]['content']
                    , $phpcsFile->eolChar) === false)
                    {
//                        var_dump($tokens[($stackPtr - 1)]);
                        //-- If the space does not contain a new line character
                        $error = 'Closing braces should not be perceded by a space';
                        $phpcsFile->addError($error, $stackPtr, 'IncorrectSpacing');
                    }
                }
        }//switch
    }//function
}//class
