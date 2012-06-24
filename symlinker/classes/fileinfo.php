<?php
/**
 * @package    SymLinker
 * @subpackage Classes
 * @author     Nikolai Plath {@link https://github.com/elkuku}
 * @author     Created on 24-Jun-2012
 * @license    GNU/GPL
 */

/**
 * File info class.
 */
class SlkFileinfo
{
    public $path = '';

    public $isFile = false;

    public $isDir = false;

    public $isLink = false;

    public $aliasPath = '';

    public $link = '';

    public $exists = false;

    /**
     * Get the file info.
     *
     * @param string $base  Base path
     * @param string $path  Path relative to base path
     * @param string $alias Optional alias
     *
     * @return SlkFileinfo
     */
    public static function getInfo($base, $path, $alias = '')
    {
        $alias = ($alias) ? $alias : $path;

        $obj = self::getInstance();
        $obj->path = SlkPath::join($base, $path);
        $obj->aliasPath = SlkPath::join($base, $alias);

        $obj->isDir = is_dir($obj->path);
        $obj->isFile = is_file($obj->path);
        $obj->isLink = is_link($obj->aliasPath);
        $obj->link = ($obj->isLink) ? SlkPath::join($base, $alias) : '';
        $obj->exists = ($obj->isDir || $obj->isFile);

        return $obj;
    }

    /**
     * Get an instance.
     *
     * @static
     * @return SlkFileinfo
     */
    private static function getInstance()
    {
        return new SlkFileinfo;
    }
}
