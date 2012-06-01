<?php
/**
 * Generic_Sniffs_Functions_FunctionCallArgumentSpacingSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: FunctionCallArgumentSpacingSniff.php 8 2010-11-06 00:40:23Z elkuku $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Checks that calls to methods and functions are spaced correctly.
 *
 * Example:
 * <b class="bad">foo($bar , $baz);</b>
 * <b class="good">foo($bar, $baz);</b>
 * <b class="good">foo($bar
 *    , $baz);</b>
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
class KuKu_Sniffs_Functions_FunctionCallArgumentSpacingSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_STRING);
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

        // Skip tokens that are the names of functions or classes
        // within their definitions. For example:
        // function myFunction...
        // "myFunction" is T_STRING but we should skip because it is not a
        // function or method *call*.
        $functionName    = $stackPtr;
        $functionKeyword = $phpcsFile->findPrevious(PHP_CodeSniffer_Tokens::$emptyTokens, ($stackPtr - 1), null, true);

        if($tokens[$functionKeyword]['code'] === T_FUNCTION || $tokens[$functionKeyword]['code'] === T_CLASS)
        {
            return;
        }

        // If the next non-whitespace token after the function or method call
        // is not an opening parenthesis then it cant really be a *call*.
        $openBracket = $phpcsFile->findNext(PHP_CodeSniffer_Tokens::$emptyTokens, ($functionName + 1), null, true);

        if($tokens[$openBracket]['code'] !== T_OPEN_PARENTHESIS)
        {
            return;
        }

        $closeBracket = $tokens[$openBracket]['parenthesis_closer'];

        $nextSeperator = $openBracket;

        while(($nextSeperator = $phpcsFile->findNext(array(T_COMMA, T_VARIABLE)
        , ($nextSeperator + 1), $closeBracket)) !== false)
        {
            // Make sure the comma or variable belongs directly to this function call,
            // and is not inside a nested function call or array.
            $brackets    = $tokens[$nextSeperator]['nested_parenthesis'];
            $lastBracket = array_pop($brackets);

            if($lastBracket !== $closeBracket)
            {
                continue;
            }

            if($tokens[$nextSeperator]['code'] === T_COMMA)
            {
                if($tokens[($nextSeperator - 1)]['code'] === T_WHITESPACE)
                {
//                    var_dump($tokens[($nextSeperator - 1)]);
//                    var_dump($tokens[($nextSeperator - 2)]);

                    if(strpos($tokens[($nextSeperator - 1)]['content'], $phpcsFile->eolChar) !== false
                  || strpos($tokens[($nextSeperator - 2)]['content'], $phpcsFile->eolChar) !== false)
                    {
                        //-- The comma is on a new line
                        continue;
                    }

                    $error = 'Space found before comma in function call';
                    $phpcsFile->addError($error, $stackPtr, 'SpaceBeforeComma');
                }

                if($tokens[($nextSeperator + 1)]['code'] !== T_WHITESPACE)
                {
                    $error = 'No space found after comma in function call';
                    $phpcsFile->addError($error, $stackPtr, 'NoSpaceAfterComma');
                }
                else
                {
                    // If there is a newline in the space, then the must be formatting
                    // each argument on a newline, which is valid, so ignore it.
                    if(strpos($tokens[($nextSeperator + 1)]['content'], $phpcsFile->eolChar) === false)
                    {
                        $space = strlen($tokens[($nextSeperator + 1)]['content']);

                        if($space > 1)
                        {
                            $error = sprintf('Expected 1 space after comma in function call; %s found'
                            , $space);

                            $phpcsFile->addError($error, $stackPtr, 'TooMuchSpaceAfterComma');
                        }
                    }
                }
            }
            else
            {
                // Token is a variable.
                $nextToken = $phpcsFile->findNext(PHP_CodeSniffer_Tokens::$emptyTokens
                , ($nextSeperator + 1), $closeBracket, true);

                if($nextToken !== false)
                {
                    if($tokens[$nextToken]['code'] === T_EQUAL)
                    {
                        if(($tokens[($nextToken - 1)]['code']) !== T_WHITESPACE)
                        {
                            $error = 'Expected 1 space before = sign of default value';
                            $phpcsFile->addError($error, $stackPtr, 'NoSpaceBeforeEquals');
                        }

                        if($tokens[($nextToken + 1)]['code'] !== T_WHITESPACE)
                        {
                            $error = 'Expected 1 space after = sign of default value';
                            $phpcsFile->addError($error, $stackPtr, 'NoSpaceAfterEquals');
                        }
                    }
                }
            }//end if
        }//while
    }//function
}//class
