<?php
/**
 * Checks the cyclomatic complexity (McCabe) for functions.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: CyclomaticComplexitySniff.php 26 2010-11-09 02:10:06Z elkuku $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Checks the cyclomatic complexity (McCabe) for functions.
 *
 * The cyclomatic complexity (also called McCabe code metrics)
 * indicates the complexity within a function by counting
 * the different paths the function includes.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Johann-Peter Hartmann <hartmann@mayflower.de>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2007 Mayflower GmbH
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: 1.3.0RC1
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class KuKu_Sniffs_Metrics_CyclomaticComplexitySniff implements PHP_CodeSniffer_Sniff
{
    /**
     * A complexity higher than this value will throw a warning.
     *
     * @var int
     */
    public $complexity = 15;

    /**
     * A complexity higer than this value will throw an error.
     *
     * @var int
     */
    public $absoluteComplexity = 25;

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
     * @param integer                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        return;//DISABLED !

        $this->currentFile = $phpcsFile;

        $tokens = $phpcsFile->getTokens();

        // Ignore abstract methods.
        if(isset($tokens[$stackPtr]['scope_opener']) === false)
        {
            return;
        }

        // Detect start and end of this function definition.
        $start = $tokens[$stackPtr]['scope_opener'];
        $end   = $tokens[$stackPtr]['scope_closer'];

        // Predicate nodes for PHP.
        $find = array(
                 'T_CASE',
                 'T_DEFAULT',
                 'T_CATCH',
                 'T_IF',
                 'T_FOR',
                 'T_FOREACH',
                 'T_WHILE',
                 'T_DO',
                 'T_ELSEIF',
        );

        $complexity = 1;

        // Iterate from start to end and count predicate nodes.
        for($i = ($start + 1); $i < $end; $i++)
        {
            if(in_array($tokens[$i]['type'], $find) === true)
            {
                $complexity++;
            }
        }//for

        if($complexity > $this->absoluteComplexity)
        {
            $error = sprintf('Function\'s cyclomatic complexity (%d) exceeds allowed maximum of %d'
            , $complexity, $this->absoluteComplexity);

            $phpcsFile->addError($error, $stackPtr, 'MaxExceeded');
        }
        else if($complexity > $this->complexity)
        {
            $warning = sprintf('Function\'s cyclomatic complexity (%s) exceeds %s;'
            .' consider refactoring the function'
            ,$complexity, $this->complexity);

            $phpcsFile->addWarning($warning, $stackPtr, 'TooHigh');
        }

        return;
    }//function
}//class
