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
 * @version   CVS: $Id: DeprecatedClassesSniff.php 26 2010-11-09 02:10:06Z elkuku $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Removed and deprecated Joomla methods.
 *
 * Discourages the use of functions that are kept in Joomla! for compatibility with older versions.
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
class KuKu_Sniffs_Joomla_DeprecatedClassesSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * A list of forbidden Joomla! methods.
     *
     * @var array(class => method)
     */
    protected $forbidden = array(
    'AAA' => 'BBB'
    );

    /**
     * A list of deprecated Joomla! methods with their alternatives.
     *
     * @var array(
     *         class => array(
     *            method => alternative
     *            , ...
     *            )
     *         , ...
     *         )
     */
    protected $deprecated = array(

    'JFactory' => array(
    'getXMLParser' => 'JFactory::getXML()'
    )

    ,'JToolBarHelper' => array(
    'customX' => 'JToolBarHelper::custom()'
    ,'addNewX' => 'JToolBarHelper::addNew()'
    //...
    )

    , 'JDate' => array(
    'toFormat' => 'JDate::format()'
    )

    , 'JSite' => array(
    'authorize' => 'JSite::authorise()'
    )

    ,'JHtmlList' => array(
    'accesslevel' => "JHtml::_('access.assetgrouplist', 'access', \$selected)")

    , 'JText' => array(
    '_' => 'jgettext()'
    ,'sprintf' => 'jgettext()'
    ,'printf' => 'jgettext()'
    )
    );

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

        //-- Sniff for forbidden functions
        if(array_key_exists($tokens[$stackPtr]['content'], $this->forbidden))
        {
            if($tokens[$stackPtr + 1]['content'] == '::')
            {
                if($tokens[$stackPtr + 2]['content'] == $this->forbidden[$tokens[$stackPtr]['content']])
                {
                    $errorMsg = sprintf('The use of %s::%s() is discouraged;'
                    , $tokens[$stackPtr]['content'], $tokens[$stackPtr + 2]['content']);

                    $phpcsFile->addError($errorMsg, $stackPtr, 'Deprecated');

                    return;
                }
            }
        }

        //-- Sniff for deprecated functions
        if(array_key_exists($tokens[$stackPtr]['content'], $this->deprecated))
        {
            if($tokens[$stackPtr + 1]['content'] == '::')
            {
                foreach($this->deprecated[$tokens[$stackPtr]['content']] as $function => $alternative)
                {
                    if($tokens[$stackPtr + 2]['content'] == $function)
                    {
                        $errorMsg = sprintf('The use of %s::%s() is discouraged;'
                        ."\n".' use %s instead'
                        , $tokens[$stackPtr]['content'], $function, $alternative);

                        $phpcsFile->addWarning($errorMsg, $stackPtr, 'Deprecated');

                        return;
                    }
                }//foreach
            }
        }
    }//function
}//class
