<?php
/**
 * Generic_Sniffs_PHP_NoSilencedErrorsSniff
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Andy Brockhurst <abrock@yahoo-inc.com>
 * @license  http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version  CVS: $Id: NoSilencedErrorsSniff.php 8 2010-11-06 00:40:23Z elkuku $
 * @link     http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Throws an error or warning when any code prefixed with an asperand is encountered.
 *
 * Example:
 * if(<b class="bad">@</b>in_array($array, $needle))
 * {
 *     doSomething();
 * }
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Andy Brockhurst <abrock@yahoo-inc.com>
 * @license  http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version  Release: 1.2.2
 * @link     http://pear.php.net/package/PHP_CodeSniffer
 */
class KuKu_Sniffs_PHP_NoSilencedErrorsSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * If true, an error will be thrown; otherwise a warning.
     *
     * @var bool
     */
    protected $error = false;

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_ASPERAND);
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
        $error = 'Silencing errors is ';

        if($this->error === true)
        {
            $error .= 'forbidden';
            $phpcsFile->addError($error, $stackPtr);
        }
        else
        {
            $error .= 'discouraged';
            $phpcsFile->addWarning($error, $stackPtr);
        }
    }//function
}//class
