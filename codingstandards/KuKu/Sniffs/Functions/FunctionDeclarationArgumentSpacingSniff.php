<?php
/**
 * Squiz_Sniffs_Functions_FunctionDeclarationArgumentSpacingSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: FunctionDeclarationArgumentSpacingSniff.php 8 2010-11-06 00:40:23Z elkuku $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Checks that arguments in function declarations are spaced correctly.
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
class KuKu_Sniffs_Functions_FunctionDeclarationArgumentSpacingSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_FUNCTION);
    }//function

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param integer                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $functionName = $phpcsFile->findNext(array(T_STRING), $stackPtr);
        $openBracket  = $tokens[$stackPtr]['parenthesis_opener'];
        $closeBracket = $tokens[$stackPtr]['parenthesis_closer'];

        $multiLine = ($tokens[$openBracket]['line'] !== $tokens[$closeBracket]['line']);

        $nextParam = $openBracket;
        $params    = array();

        while(($nextParam = $phpcsFile->findNext(T_VARIABLE, ($nextParam + 1), $closeBracket)) !== false)
        {
            $nextToken = $phpcsFile->findNext(T_WHITESPACE, ($nextParam + 1), ($closeBracket + 1), true);

            if($nextToken === false)
            {
                break;
            }

            $nextCode = $tokens[$nextToken]['code'];

            if($nextCode === T_EQUAL)
            {
                if($tokens[$nextToken - 1]['code'] != T_WHITESPACE)
                {
                    $error = sprintf('Expected 1 space between argument "%s" and equals sign; 0 found'
                    , $tokens[$nextParam]['content']);

                    $phpcsFile->addError($error, $nextToken, 'SpaceAfterDefault');
                }
                else
                {
                    if(strlen($tokens[($nextToken - 1)]['content']) != 1)
                    {
                        $error = sprintf('Expected 1 space between argument "%s" and equals sign; %d found'
                        , $tokens[$nextParam]['content'], strlen($tokens[($nextToken - 1)]['content']));

                        $phpcsFile->addError($error, $nextToken, 'SpaceAfterDefault');
                    }
                }

                if($tokens[$nextToken + 1]['code'] != T_WHITESPACE)
                {
                    $error = sprintf('Expected 1 space between equals sign and default value for argument "%s"; 0 found'
                    , $tokens[$nextParam]['content']);

                    $phpcsFile->addError($error, $nextToken, 'SpaceAfterDefault');
                }
                else
                {
                    if(strlen($tokens[($nextToken + 1)]['content']) != 1)
                    {
                        $error = sprintf('Expected 1 space between equals sign and default for argument "%s"; %d found'
                        , $tokens[$nextParam]['content'], strlen($tokens[($nextToken + 1)]['content']));

                        $phpcsFile->addError($error, $nextToken, 'SpaceAfterDefault');
                    }
                }
            }

            // Find and check the comma (if there is one).
            $nextComma = $phpcsFile->findNext(T_COMMA, ($nextParam + 1), $closeBracket);

            if($nextComma !== false)
            {
                // Comma found.
                if($tokens[($nextComma - 1)]['code'] === T_WHITESPACE)
                {
                    if(strpos($tokens[($nextComma - 2)]['content'], $phpcsFile->eolChar) === false)
                    {
                        //-- If the comma is not on a new line
                        $error = sprintf('Expected 0 spaces between argument "%s" and comma; %s found'
                        , $tokens[$nextParam]['content'], strlen($tokens[($nextComma - 1)]['content']));

                        $phpcsFile->addError($error, $nextToken, 'SpaceBeforeComma');
                    }
                }
            }

            // Take references into account when expecting the
            // location of whitespace.
            if($phpcsFile->isReference(($nextParam - 1)) === true)
            {
                $whitespace = $tokens[($nextParam - 2)];
            }
            else
            {
                $whitespace = $tokens[($nextParam - 1)];
            }

            if(empty($params) === false)
            {
                // This is not the first argument in the function declaration.
                $arg = $tokens[$nextParam]['content'];

                if($whitespace['code'] === T_WHITESPACE)
                {
                    $gap = strlen($whitespace['content']);

                    // Before we throw an error, make sure there is no type hint.
                    $comma     = $phpcsFile->findPrevious(T_COMMA, ($nextParam - 1));
                    $nextToken = $phpcsFile->findNext(T_WHITESPACE, ($comma + 1), null, true);

                    if($phpcsFile->isReference($nextToken) === true)
                    {
                        $nextToken++;
                    }

                    if($nextToken !== $nextParam)
                    {
                        // There was a type hint, so check the spacing between
                        // the hint and the variable as well.
                        $hint = $tokens[$nextToken]['content'];

                        if($gap !== 1)
                        {
                            $error = sprintf('Expected 1 space between type hint and argument "%s"; %s found'
                            , $arg, $gap);

                            $phpcsFile->addError($error, $nextToken, 'SpacingAfterHint');
                        }

                        if($multiLine === false)
                        {
                            if($tokens[($comma + 1)]['code'] !== T_WHITESPACE)
                            {
                                $error = sprintf('Expected 1 space between comma and type hint "%s"; 0 found'
                                , $hint);

                                $phpcsFile->addError($error, $nextToken, 'NoSapceBeforeHint');
                            }
                            else
                            {
                                $gap = strlen($tokens[($comma + 1)]['content']);

                                if($gap !== 1)
                                {
                                    $error = sprintf('Expected 1 space between comma and type hint "%s"; %s found'
                                    , $hint, $gap);

                                    $phpcsFile->addError($error, $nextToken, 'SpacingBeforeHint');
                                }
                            }
                        }
                    }
                    else if($multiLine === false && $gap !== 1)
                    {
                        $error = sprintf('Expected 1 space between comma and argument "%s"; %s found'
                        , $arg, $gap);

                        $phpcsFile->addError($error, $nextToken, 'SpacingBeforeArg');
                    }
                }
                else
                {
                    $error = sprintf('Expected 1 space between comma and argument "%s"; 0 found', $arg);

                    $phpcsFile->addError($error, $nextToken, 'NoSpaceBeforeArg');
                }
            }
            else
            {
                // First argument in function declaration.
                if($whitespace['code'] === T_WHITESPACE)
                {
                    $gap = strlen($whitespace['content']);
                    $arg = $tokens[$nextParam]['content'];

                    // Before we throw an error, make sure there is no type hint.
                    $bracket   = $phpcsFile->findPrevious(T_OPEN_PARENTHESIS, ($nextParam - 1));
                    $nextToken = $phpcsFile->findNext(T_WHITESPACE, ($bracket + 1), null, true);

                    if($phpcsFile->isReference($nextToken) === true)
                    {
                        $nextToken++;
                    }

                    if($nextToken !== $nextParam)
                    {
                        // There was a type hint, so check the spacing between
                        // the hint and the variable as well.
                        $hint = $tokens[$nextToken]['content'];

                        if($gap !== 1)
                        {
                            $error = sprintf('Expected 1 space between type hint and argument "%s"; %s found'
                            , $arg, $gap);

                            $phpcsFile->addError($error, $nextToken, 'SpacingAfterHint');
                        }

                        if($multiLine === false
                        && $tokens[($bracket + 1)]['code'] === T_WHITESPACE
                        )
                        {
                            $error = sprintf('Expected 0 spaces between opening bracket and type hint "%s"; %s found'
                            , $hint, strlen($tokens[($bracket + 1)]['content']));

                            $phpcsFile->addError($error, $nextToken, 'SpacingAfterOpenHint');
                        }
                    }
                    else if($multiLine === false)
                    {
                        $error = sprintf('Expected 0 spaces between opening bracket and argument "%s"; %s found'
                        , $arg, $gap);

                        $phpcsFile->addError($error, $nextToken, 'SpacingAfterOpen');
                    }
                }
            }

            $params[] = $nextParam;
        }//while

        if(empty($params) === true)
        {
            // There are no parameters for this function.
            if(($closeBracket - $openBracket) !== 1)
            {
                $error = sprintf('Expected 0 spaces between brackets of function declaration; %s found'
                , strlen($tokens[($closeBracket - 1)]['content']));

                $phpcsFile->addError($error, $stackPtr, 'SpacingBetween');
            }
        }
        else if($multiLine === false
        && $tokens[($closeBracket - 1)]['code'] === T_WHITESPACE
        )
        {
            $lastParam = array_pop($params);
            $error = sprintf('Expected 0 spaces between argument "%s" and closing bracket; %s found'
            , $tokens[$lastParam]['content'], strlen($tokens[($closeBracket - 1)]['content']));

            $phpcsFile->addError($error, $closeBracket, 'SpacingBeforeClose');
        }
    }//function
}//class
