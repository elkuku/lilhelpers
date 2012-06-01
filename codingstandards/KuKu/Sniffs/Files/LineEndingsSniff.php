<?php
/**
 * Squiz_Sniffs_Files_LineEndingsSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: LineEndingsSniff.php 437 2011-07-12 15:13:40Z elkuku $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

if(class_exists('Generic_Sniffs_Files_LineEndingsSniff', true) === false)
{
    throw new PHP_CodeSniffer_Exception('Class Generic_Sniffs_Files_LineEndingsSniff not found');
}

/**
 * Checks for UNIX style line endings.
 *
 * Good:
 * <b class="good">\n</b>
 *
 * Any other nonUnix OS line endings are bad:
 * <b class="bad">\r\n</b> etc.
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
class KuKu_Sniffs_Files_LineEndingsSniff extends Generic_Sniffs_Files_LineEndingsSniff
{
    /**
     * The valid EOL character.
     *
     * @var string
     */
    public $eolChar = '\n';
}//class
