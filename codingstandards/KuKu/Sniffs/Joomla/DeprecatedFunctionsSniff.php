<?php
/**
 * Generic_Sniffs_PHP_DeprecatedFunctionsSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: DeprecatedFunctionsSniff.php 8 2010-11-06 00:40:23Z elkuku $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Discourages the use of deprecated functions that are kept in Joomla! for compatibility with older versions.
 *
 * Example:
 * <b class="warn">JFactory::getXMLParser()</b>
 * <b class="warn">JText</b> coming soon =;)
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: 1.3.0RC1
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class KuKu_Sniffs_Joomla_DeprecatedFunctionsSniff// extends KuKu_Sniffs_Joomla_ForbiddenFunctionsSniff
{
    /**
     * A list of forbidden functions with their alternatives.
     *
     * The value is NULL if no alternative exists. IE, the
     * function should just not be used.
     *
     * @var array(string => string|null)
     */
    protected $forbiddenFunctions = array();

    /**
     * Constructor.
     *
     * Uses the Reflection API to get a list of deprecated functions.
     */
    public function __construct()
    {
        if(version_compare(PHP_VERSION, '5.2.13') >= 0)
        {
            //-- Due to a bug in PHP < 5.2.13 we cannot use
            //-- ReflectionFunction->isDeprecated()
            $functions = get_defined_functions();

            foreach($functions['internal'] as $functionName)
            {
                $function = new ReflectionFunction($functionName);

                if($function->isDeprecated() === true)
                {
                    $this->forbiddenFunctions[$functionName] = NULL;
                }
            }//foreach
        }
    }//function
}//class
