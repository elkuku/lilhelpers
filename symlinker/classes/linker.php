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
    public static function getList()
    {
        $symLinks = self::getLinks();

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

    /**
     * Get a link list.
     *
     * @static
     * @throws DomainException
     * @return array
     */
    private static function getLinks()
    {
        if(false == file_exists(ROOT_PATH.'/symlinks'))
            throw new DomainException(__METHOD__.' - Symlink file not found in'.ROOT_PATH.'/symlinks');

        $lines = file(ROOT_PATH.'/symlinks');

        $syms = array();
        $base = '';

        foreach($lines as $lNo => $line)
        {
            $line = trim($line);

            //-- Strip blanks and comments
            if(false == $line
                || strpos($line, '#') === 0
            )
                continue;

            if(strpos($line, 'basePath=') === 0)
            {
                $base = trim(str_replace('basePath=', '', $line));

                $syms[$base] = array();

                continue;
            }

            if($base == '')
            {
                echo 'base not set ! - '.$lNo;

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
