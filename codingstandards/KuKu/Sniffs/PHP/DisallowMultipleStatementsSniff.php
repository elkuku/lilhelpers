<?php
/**
 * Generic_Sniffs_Formatting_DisallowMultipleStatementsSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: DisallowMultipleStatementsSniff.php 8 2010-11-06 00:40:23Z elkuku $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Ensures each statement is on a line by itself.
 *
 * Example:
 * <b class="bad">echo 'Foo'; echo 'Bar';</b>
 * <b class="good">echo 'Foo';</b>
 * <b class="good">echo 'Bar';</b>
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: 1.3.0RC1
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class KuKu_Sniffs_PHP_DisallowMultipleStatementsSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_SEMICOLON);
    }//function

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param integer $stackPtr The position of the current token in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $prev = $phpcsFile->findPrevious(T_SEMICOLON, ($stackPtr - 1));

        if($prev === false)
        {
            return;
        }

        // Ignore multiple statements in a FOR condition.
        if(isset($tokens[$stackPtr]['nested_parenthesis']) === true)
        {
            foreach($tokens[$stackPtr]['nested_parenthesis'] as $bracket)
            {
                if(isset($tokens[$bracket]['parenthesis_owner']) === false)
                {
                    // Probably a closure sitting inside a function call.
                    continue;
                }

                $owner = $tokens[$bracket]['parenthesis_owner'];

                if($tokens[$owner]['code'] === T_FOR)
                {
                    return;
                }
            }//foreach
        }

        if($tokens[$prev]['line'] === $tokens[$stackPtr]['line'])
        {
            $parts = explode(DIRECTORY_SEPARATOR, $phpcsFile->getFileName());

            if($parts[count($parts) - 2] != 'tmpl')
            {
                //-- Multiple statements are allowed in html tmplate files which are in a folder "tmpl"
                $error = 'Each PHP statement must be on a line by itself';
                $phpcsFile->addError($error, $stackPtr, 'SameLine');
            }

            return;
        }
    }//function
}//class
