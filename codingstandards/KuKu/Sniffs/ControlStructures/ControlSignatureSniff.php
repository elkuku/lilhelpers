<?php
/**
 * Verifies that control statements conform to their coding standards.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: ControlSignatureSniff.php 8 2010-11-06 00:40:23Z elkuku $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

if(class_exists('PHP_CodeSniffer_Standards_AbstractPatternSniff', true) === false)
{
    throw new PHP_CodeSniffer_Exception(
    'Class PHP_CodeSniffer_Standards_AbstractPatternSniff not found');
}

/**
 * Verifies that control statements conform to the coding standard.
 *
 * My rule for this is quite simple:
 *
 * <b>--------------------------------------------</b>
 * <b>* Every curly brace deserves it's own line *</b>
 * <b>--------------------------------------------</b>
 *
 * <b class="bad">Bad:</b>
 * stmt($foo) <b class="bad">{</b>
 *    // blah
 * }
 * <b class="good">Good:</b>
 * stmt($foo)
 * <b class="good">{</b>
 *    // blah
 * }
 *
 * This is applied to: <b>try</b>, <b>catch</b>, <b>do</b>, <b>while</b>, <b>for</b>,
 * <b>foreach</b>, <b>if</b>, <b>else</b> and <b>else if</b>.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: 1.2.2
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class KuKu_Sniffs_ControlStructures_ControlSignatureSniff
extends PHP_CodeSniffer_Standards_AbstractPatternSniff
{
    /**
     * Constructs a PEAR_Sniffs_ControlStructures_ControlSignatureSniff.
     */
    public function __construct()
    {
        parent::__construct(true);
    }//function

    /**
     * Returns the patterns that this test wishes to verify.
     *
     * @return array(string)
     */
    protected function getPatterns()
    {
        return array(
        'if(...)EOL...{...}EOL'
        , 'else if(...)EOL...{'
        , 'elseEOL...{'

        , 'tryEOL...{...}EOL'
        , 'catch(...)EOL...{'

        , 'doEOL...{'
        , 'while(...)EOL...{'

        , 'for(...)EOL...{'
        , 'foreach(...)EOL...{'

        , 'switch(...)EOL...{'
        );
    }//function

    /**
     * Process a pattern.
     *
     * Returns if we are inside a "tmpl" folder - workaround :(
     *
     * @param array $patternInfo Information about the pattern used for checking, which includes are
     *               parsed token representation of the pattern.
     * @param PHP_CodeSniffer_File $phpcsFile The PHP_CodeSniffer file where the token occured.
     * @param integer $stackPtr The postion in the tokens stack where the listening token type was found.
     *
     * @return return_type
     */
    protected function processPattern($patternInfo, PHP_CodeSniffer_File $phpcsFile
    , $stackPtr)
    {
        $parts = explode(DIRECTORY_SEPARATOR, $phpcsFile->getFileName());

        if($parts[count($parts) - 2] == 'tmpl')
        {
            return false;
        }

        return parent::processPattern($patternInfo, $phpcsFile, $stackPtr);
    }//function
}//class
