<?php
/**
 * Squiz_Sniffs_WhiteSpace_SuperfluousWhitespaceSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: SuperfluousWhitespaceSniff.php 560 2011-10-08 16:07:21Z elkuku $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Checks for whitespace.
 *
 * Checks that.
 * <ul>
 *   <li>No whitespace proceeds the first content of the file.</li>
 *   <li>No whitespace resides after content on any line.</li>
 *   <li>There are not two or more empty lines in functions and classes.</li>
 *   <li>There is a exactly one empty line after the last content of the file.</li>
 * </ul>
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
class KuKu_Sniffs_WhiteSpace_SuperfluousWhitespaceSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = array(
       'PHP',
       'JS',
       'CSS',
    );

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
        T_OPEN_TAG,
        T_CLOSE_TAG,
        T_WHITESPACE,
        T_COMMENT,
        T_CLOSE_CURLY_BRACKET,
        );
    }//function

    /**
     * Processes this sniff, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param integer $stackPtr The position of the current token in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if($stackPtr == $phpcsFile->numTokens - 1)
        {
            //-- We reached the end of the file

            if($tokens[$stackPtr]['code'] == T_WHITESPACE)
            {
                $lastCode = $tokens[$stackPtr - 1]['code'];

                if($lastCode != T_CLOSE_CURLY_BRACKET
                && $lastCode !== T_SEMICOLON)
                {
                    //-- If the second last is not a curly bracket or a semicolon
                    $phpcsFile->addError('Additional whitespace found at end of file', $stackPtr);
                }
            }
            else if(strpos($tokens[$stackPtr]['content'], $phpcsFile->eolChar) === false)
            {
                //-- Files must end with an empty line
                $phpcsFile->addError('Please end your files with an empty line.', $stackPtr);
            }
        }

        if($tokens[$stackPtr]['code'] === T_OPEN_TAG)
        {
            /*
             Check for start of file whitespace.
            */

            if($phpcsFile->tokenizerType !== 'PHP')
            {
                // The first token is always the open tag inserted when tokenizsed
                // and the second token is always the first piece of content in
                // the file. If the second token is whitespace, there was
                // whitespace at the start of the file.
                if($tokens[($stackPtr + 1)]['code'] !== T_WHITESPACE)
                {
                    return;
                }
            }
            else
            {
                // If its the first token, then there is no space.
                if($stackPtr === 0)
                {
                    return;
                }

                for($i = ($stackPtr - 1); $i >= 0; $i--)
                {
                    // If we find something that isn't inline html
                    // then there is something previous in the file.
                    if($tokens[$i]['type'] !== 'T_INLINE_HTML')
                    {
                        return;
                    }

                    // If we have ended up with inline html make sure it isn't just whitespace.
                    $tokenContent = trim($tokens[$i]['content']);

                    if($tokenContent !== '')
                    {
                        return;
                    }
                }//for
            }//end if

            $phpcsFile->addError('Additional whitespace found at start of file', $stackPtr);
        }
        else if($tokens[$stackPtr]['code'] === T_CLOSE_TAG)
        {
            //            #die($phpcsFile->numTokens.'*******AA****'.$stackPtr);
            /*
            Check for end of file whitespace.
            */

            if($phpcsFile->tokenizerType === 'JS')
            {
                // The last token is always the close tag inserted when tokenizsed
                // and the second last token is always the last piece of content in
                // the file. If the second last token is whitespace, there was
                // whitespace at the end of the file.
                if($tokens[($stackPtr - 1)]['code'] !== T_WHITESPACE)
                {
                    return;
                }
            }
            else if($phpcsFile->tokenizerType === 'CSS')
            {
                // The last two tokens are always the close tag and whitespace
                // inserted when tokenizsed and the third last token is always the
                // last piece of content in the file. If the third last token is
                // whitespace, there was whitespace at the end of the file.
                if($tokens[($stackPtr - 3)]['code'] !== T_WHITESPACE)
                {
                    return;
                }

                // Adjust the pointer to give the correct line number for the error.
                $stackPtr -= 2;
            }
            else
            {
                if(isset($tokens[($stackPtr + 1)]) === false)
                {
                    // The close PHP token is the last in the file.
                    return;
                }

                for($i = ($stackPtr + 1); $i < $phpcsFile->numTokens; $i++)
                {
                    // If we find something that isn't inline html then there
                    // is more to the file.
                    if($tokens[$i]['type'] !== 'T_INLINE_HTML')
                    {
                        return;
                    }

                    // If we have ended up with inline html make sure it
                    // isn't just whitespace.
                    $tokenContent = trim($tokens[$i]['content']);

                    if(empty($tokenContent) === false)
                    {
                        return;
                    }
                }//for
            }

            $phpcsFile->addError('Additional whitespace found at end of file', $stackPtr);
        }
        else
        {
            /*
             Check for end of line whitespace.
            */

            if(strpos($tokens[$stackPtr]['content'], $phpcsFile->eolChar) === false)
            {
                return;
            }

            $tokenContent = rtrim($tokens[$stackPtr]['content'], $phpcsFile->eolChar);

            if(empty($tokenContent) === false)
            {
                if(preg_match('|^.*\s+$|', $tokenContent) !== 0)
                {
                    $phpcsFile->addError('Whitespace found at end of line', $stackPtr);
                }
            }

            /*
             Check for multiple blanks lines in a function or class.
            */

            if($phpcsFile->hasCondition($stackPtr, T_FUNCTION) === true
            || $phpcsFile->hasCondition($stackPtr, T_CLASS) === true)
            {
                if($tokens[($stackPtr - 1)]['line'] < $tokens[$stackPtr]['line']
                && $tokens[($stackPtr - 2)]['line'] === $tokens[($stackPtr - 1)]['line'])
                {
                    // This is an empty line and the line before this one is not
                    //  empty, so this could be the start of a multiple empty
                    // line block.
                    $next  = $phpcsFile->findNext(T_WHITESPACE, $stackPtr, null, true);
                    $lines = $tokens[$next]['line'] - $tokens[$stackPtr]['line'];

                    if($lines > 1)
                    {
                        $error = 'Functions and classes must not contain multiple empty lines in a row;'
                        ." found $lines empty lines";
                        $phpcsFile->addError($error, $stackPtr);
                    }
                }
            }
        }//end if
    }//function
}//class
