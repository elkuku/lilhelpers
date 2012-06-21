<?php
/**
 * User: elkuku
 * Date: 21.06.12
 * Time: 17:44
 */

/**
 * File info class.
 *
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
     * @param string $base Base path
     * @param string $path Path relative to base path
     * @param string $alias Optional alias
     *
     * @return SlkFileinfo
     */
    public static function getInfo($base, $path, $alias = '')
    {
        $alias =($alias) ? $alias : $path;

        $obj = self::getInstance();
        $obj->path = $base.DS.$path;
        $obj->aliasPath = $base.DS.$alias;

        $obj->isDir = is_dir($obj->path);
        $obj->isFile = is_file($obj->path);
        $obj->isLink = is_link($obj->aliasPath);
        $obj->link =($obj->isLink) ? $base.DS.$alias : '';
        $obj->exists =($obj->isDir || $obj->isFile);

        return $obj;
    }//function

    private function getInstance()
    {
        return new SlkFileinfo;
    }//function
}//class
