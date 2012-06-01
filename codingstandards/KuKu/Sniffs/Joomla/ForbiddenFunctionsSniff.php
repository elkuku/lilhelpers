<?php
/**
 * Generic_Sniffs_PHP_ForbiddenFunctionsSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: ForbiddenFunctionsSniff.php 8 2010-11-06 00:40:23Z elkuku $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Discourages the use of functions that are kept in Joomla! for compatibility with older versions.
 *
 * Example:
 * <b class="bad">mosgetparam()</b>
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
class KuKu_Sniffs_Joomla_ForbiddenFunctionsSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * A list of forbidden functions with their alternatives.
     *
     * The value is NULL if no alternative exists. IE, the
     * function should just not be used.
     *
     * @var array(string => string|null)
     */
    protected $forbiddenFunctions = array(
    /* Deprecated Joomla functions */
    'jtext::' => 'jgettext'
    /* Removed Joomla functions */
    ,'mosgetparam' => null

    /* Deprecated PHP functions */
    ,'sizeof' => 'count'
    ,'delete' => 'unset');

    /**
     * If true, an error will be thrown; otherwise a warning.
     *
     * @var bool
     */
    public $error = true;

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_STRING);

    if(false)
    {
echo JText::a('a');
    }
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

        $ignore = array(
        T_DOUBLE_COLON,
        T_OBJECT_OPERATOR,
        T_FUNCTION,
        T_CONST,
        );

        $prevToken = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);

        if(in_array($tokens[$prevToken]['code'], $ignore) === true)
        {
            // Not a call to a PHP function.
            return;
        }

        $function = strtolower($tokens[$stackPtr]['content']);

        if( ! array_key_exists($function, $this->forbiddenFunctions))
        {
            return;
        }

        $error = sprintf('The use of function %s() is ', $function);

        if($this->forbiddenFunctions[$function] !== null)
        {
            //-- Deprecated
            $error .= 'discouraged; use %s() instead';
            $errorMsg = sprintf($error, $function, $this->forbiddenFunctions[$function]);

            $phpcsFile->addWarning($errorMsg, $stackPtr, 'Deprecated');
        }
        else
        {
            //-- Forbidden
            $error .= 'forbidden';
            $errorMsg = sprintf($error, $function);

            $phpcsFile->addError($errorMsg, $stackPtr, 'Forbidden');
        }
    }//function
}//class
