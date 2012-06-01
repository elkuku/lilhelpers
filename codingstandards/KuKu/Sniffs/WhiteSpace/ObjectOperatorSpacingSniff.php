<?php
/**
 * Squiz_Sniffs_WhiteSpace_ObjectOperatorSpacingSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: ObjectOperatorSpacingSniff.php 139 2010-11-23 10:12:50Z elkuku $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Ensure there is no whitespace before an object operator.
 *
 * Example:
 * <b class="bad">$foo ->$bar();</b>
 * <b class="good">$foo->$bar();</b>
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
class KuKu_Sniffs_WhiteSpace_ObjectOperatorSpacingSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = array(
                                   'PHP',
                                   'JS',
    );

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_OBJECT_OPERATOR);
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

        $prevType = $tokens[($stackPtr - 1)]['code'];

        if(in_array($prevType, PHP_CodeSniffer_Tokens::$emptyTokens) === true)
        {
            if(strpos($tokens[$stackPtr - 1]['content'], PHP_EOL) !== false
           || strpos($tokens[$stackPtr - 2]['content'], PHP_EOL) !== false)
            {
                //-- Object operator is on a new line
                return;
            }

            $error = 'Space found before object operator';
            $phpcsFile->addError($error, $stackPtr, 'Before');
        }

        $nextType = $tokens[($stackPtr + 1)]['code'];

        if(in_array($nextType, PHP_CodeSniffer_Tokens::$emptyTokens) === true)
        {
            $error = 'Space found after object operator';
            $phpcsFile->addError($error, $stackPtr, 'After');
        }
    }//function
}//class
