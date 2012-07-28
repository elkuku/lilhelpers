<?php
/**
 * @package    SymLinker
 * @subpackage Base
 * @author     Nikolai Plath {@link https://github.com/elkuku}
 * @author     Created on 26-Oct-2010
 * @license    GNU/GPL
 */

define('SLK_EXEC', 1);

define('BR', '<br />');

define('ROOT_PATH', dirname($_SERVER['SCRIPT_FILENAME']));

include 'classes/loader.php';

$task = @ $_GET['task'];
$sym_base = @ $_GET['sym_base'];
$sym_path = @ $_GET['sym_path'];
$alias = @ $_GET['alias'];

$alias = ($alias) ? $alias : $sym_path;

$src = SlkFileinfo::getInfo($sym_base, $sym_path, $alias);
$dest = SlkFileinfo::getInfo(ROOT_PATH, $sym_path, $alias);

switch($task)
{
    case 'create':
        if(true == $src->exists
            && false == $dest->isLink
        )
        {
            echo 'symlinking '.$src->path.BR;
            echo '---------> '.$dest->path.BR;

            exec('mkdir -p "'.dirname($dest->path).'"');

            //-- Create a symbolic link
            if(false == symlink($src->path, $dest->aliasPath))
            {
                echo('<h2 style="color: red;">Symlink could not be created :(</h2>');
            }
        }
        else
        {
            echo 'Supplied source path does not exist :('.BR;
            var_dump($src, $dest);
        }
        break;

    case 'remove' :
        if(true == $dest->isLink)
        {
            echo 'Removing: '.$dest->aliasPath;

            //-- Remove a symbolic link
            if(false == unlink($dest->aliasPath))
            {
                exit('Symlink could not be deleted :(');
            }
        }
        else
        {
            echo 'Supplied path is not a symbolic link :('.BR;
        }
        break;

    default:
        echo ($task) ? 'unknown task ??' : '';
        break;
}

include 'template/template.php';
