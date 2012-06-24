<?php
/**
 * @package    SymLinker
 * @subpackage Classes
 * @author     Nikolai Plath {@link https://github.com/elkuku}
 * @author     Created on 24-Jun-2012
 * @license    GNU/GPL
 */

/**
 * A path utility class.
 */
class SlkPath
{
    /**
     * Joins directories with the system directory separator.
     *
     * @return string
     */
    public static function join()
    {
        return implode(DIRECTORY_SEPARATOR, func_get_args());
    }
}
