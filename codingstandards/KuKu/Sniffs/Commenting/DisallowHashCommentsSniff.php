<?php
/**
 * This sniff prohibits the use of Perl style hash comments.
 *
 * PHP version 5
 * @version $Id: DisallowHashCommentsSniff.php 26 2010-11-09 02:10:06Z elkuku $
 * @package
 * @subpackage
 * @author		EasyJoomla {@link http://www.easy-joomla.org Easy-Joomla.org}
 * @author		Nikolai Plath {@link http://www.nik-it.de}
 * @author		Created on 09.09.2009
 */

/**
 * This sniff prohibits the use of Perl style hash comments.
 *
 * An example of a hash comment is:
 *
 *  <b class="bad"># This is a hash comment</b>
 *  <b class="good">// This is a standard comment</b>
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Your Name <you@domain.net>
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class KuKu_Sniffs_Commenting_DisallowHashCommentsSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * Returns the token types that this sniff is interested in.
     *
     * @return array (int)
     */
    public function register()
    {
        return array(T_COMMENT);
    }//function

    /**
     * Processes the tokens that this sniff is interested in.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file where the token was found.
     * @param integer $stackPtr  The position in the stack where the token was found.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if($tokens[$stackPtr]['content']{0} === '#'
        && strpos($tokens[$stackPtr]['content'], '##') !== 0
        && strpos($tokens[$stackPtr]['content'], '#_') !== 0)
        {
            $error = 'Hash comments are prohibited'.$tokens[$stackPtr]['content'];
            $phpcsFile->addWarning($error, $stackPtr);
        }
    }//function
}//class
