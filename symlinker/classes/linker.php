<?php
/**
 * @package    SymLinker
 * @subpackage Classes
 * @author     Nikolai Plath {@link https://github.com/elkuku}
 * @author     Created on 24-Jun-2012
 * @license    GNU/GPL
 */

/**
 * Symlinker class.
 */
class SlkLinker
{
    /**
     * Get the symlink list.
     *
     * @return array
     */
    public static function getList()
    {
        $symLinks = self::parseFiles();

        $list = array();

        foreach($symLinks as $symBase => $linkList)
        {
            if('' == $symBase)
                continue;

            $links = array();

            foreach($linkList as $path => $alias)
            {
                $link = new SlkLink;

                $link->source = SlkFileinfo::getInfo($symBase, $path, $alias);
                $link->destination = SlkFileinfo::getInfo(ROOT_PATH, $path, $alias);
                $link->path = $path;
                $link->alias = ($alias) ? : $path;

                $link->createLink = '&sym_base='.$symBase
                    .'&sym_path='.$path
                    .'&alias='.$link->alias;

                $links[] = $link;
            }

            $list[$symBase] = $links;
        }

        return $list;
    }

    private static function parseFiles()
    {
        $list = array();

        foreach(new DirectoryIterator(ROOT_PATH) as $fileInfo)
        {
            if($fileInfo->isDot())
                continue;

            if($fileInfo->isDir())
                continue;

            $fileName = $fileInfo->getFilename();

            $ext = substr($fileName, strrpos($fileName, '.') + 1);

            if('slk' != $ext)
                continue;

            $list = array_merge($list, self::parseFile($fileInfo->getPathname()));
        }


        return $list;
    }

    /**
     * Get a link list.
     *
     * @static
     *
     * @param string $path
     *
     * @throws DomainException
     * @return array
     */
    private static function parseFile($path)
    {
        if(false == file_exists($path))
            throw new DomainException(__METHOD__.' - Symlink file not found in: '.$path);

        $lines = file($path);

        $syms = array();
        $base = '';

        foreach($lines as $lNo => $line)
        {
            $line = trim($line);

            //-- Strip blanks and comments
            if(false == $line
                || 0 === strpos($line, '#')
            )
                continue;

            if(0 === strpos($line, 'basePath='))
            {
                $base = trim(str_replace('basePath=', '', $line));

                $syms[$base] = array();

                continue;
            }

            if('' == $base)
            {
                echo sprintf('%s - Base not set in config file: "%s" line: %d<br />'
                    , __METHOD__, $path, $lNo);

                continue;
            }

            $parts = explode(' ', $line);

            $alias = (isset($parts[1])) ? trim($parts[1]) : '';

            $syms[$base][$parts[0]] = $alias;
        }

        return $syms;
    }

    /**
     * Creates a template for a new symlink file.
     *
     * @throws Exception
     * @return string
     */
    public static function createSymLinkTemplate()
    {
        $template = <<<EOL
# This file contains the paths to your projects and their symbolic links

# Project Foo
# The variable basePath contains the path to your working copy
basePath=

# Every symlink goes on it's own line
# path/from/basePath [alias/path]
EOL;

        $handle = fopen('symlinks', 'w');

        if(false == $handle)
            throw new Exception('Can not create the template file');

        if(false == fwrite($handle, $template))
            throw new Exception('Can not write the template file');

        return true;
    }

}
