<?php
/**
 * User: elkuku
 * Date: 21.06.12
 * Time: 17:49
 */

class SlkLinker
{
    /**
     * @static
     * @return array
     */
    public static function getLinks()
    {
        $lines = file('symlinks');

        $syms = array();
        $base = '';

        foreach($lines as $lNo => $line)
        {
            $line = trim($line);

            //-- Strip blanks and comments
            if(false == $line
                || strpos($line, '#') === 0)
                continue;

            if(strpos($line, 'basePath=') === 0)
            {
                $base = trim(str_replace('basePath=', '', $line));

                $syms[$base] = array();

                continue;
            }

            if($base == '')
            {
                echo sprintf('%s - Base not set in config line: %d<br />', __METHOD__, $lNo);

                continue;
            }

            $parts = explode(' ', $line);

            $s = trim($parts[0]);
            $alias =(isset($parts[1])) ? trim($parts[1]) : '';

            $syms[$base][$parts[0]] = $alias;
        }//foreach

        return $syms;
    }//function

    /**
     * Creates a template for a new smlink file.
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
    }//function


}
