<?php
/**
 * Squiz_Sniffs_Operators_ValidLogicalOperatorsSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: ValidLogicalOperatorsSniff.php 8 2010-11-06 00:40:23Z elkuku $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Checks to ensure that the logical operators <b class="bad">and</b> and <b class="bad">or</b> are not used.
 * Use the <b class="good">&&</b> and <b class="good">||</b> operators instead.
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
class KuKu_Sniffs_Operators_ValidLogicalOperatorsSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
        T_LOGICAL_AND,
        T_LOGICAL_OR,
        T_LOGICAL_XOR,
        );
    }//function

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The current file being scanned.
     * @param integer                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $replacements = array(
         'and' => '&&',
         'or'  => '||',
         'xor' => '^',
        );

        $operator = strtolower($tokens[$stackPtr]['content']);

        if(isset($replacements[$operator]) === false)
        {
            return;
        }

        $error = sprintf('Logical operator "%s" is prohibited; use "%s" instead'
        , $operator, $replacements[$operator]);

        $phpcsFile->addError($error, $stackPtr, 'NotAllowed');
    }//function
}//class
