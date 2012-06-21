<?php
/**
 * @version SVN: $Id: symlinker.php 557 2011-10-08 16:04:57Z elkuku $
 * @package    SymLinker
 * @subpackage Base
 * @author     Nikolai Plath {@link http://easy-joomla.org}
 * @author     Created on 26-Oct-2010
 * @license    GNU/GPL
 */

define('DS', DIRECTORY_SEPARATOR);

define('BR', '<br />');

define('ROOT_PATH', dirname($_SERVER['SCRIPT_FILENAME']));

include 'classes/loader.php';

if(isset($_GET['tpl_created']))
{
    echo '*** Template created - please modify your paths ! ***'.BR;
    echo ROOT_PATH.DS.'symlinks'.BR;
}

if(false == file_exists('symlinks'))
{
    try
    {
        SlkLinker::createSymLinkTemplate();

        header('Location: http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?tpl_created');

        return;
    }
    catch(Exception $e)
    {
        exit($e->getMessage());
    }//try
}

$symlinkList = SlkLinker::getLinks();

$task = @ $_GET['task'];
$sym_base = @ $_GET['sym_base'];
$sym_path = @ $_GET['sym_path'];
$alias = @ $_GET['alias'];

$alias =($alias) ? $alias : $sym_path;

$src = SlkFileinfo::getInfo($sym_base, $sym_path, $alias);
$dest = SlkFileinfo::getInfo(ROOT_PATH, $sym_path, $alias);

switch($task)
{
    case 'create':
        if(true == $src->exists
            && false == $dest->isLink)
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
}//switch

?>
<html>
<head>
    <title>SymLinker</title>
    <style>
        b.src { color: blue; }
        b.dest { color : maroon; }
        body { background-image:
            -moz-radial-gradient(50% 50% 360deg,circle cover,
            #949494, #C9C9C9, #C7C7C7 75%,#888999 100%); }
        a:link,a:visited { color: blue; font-weight: bold; text-decoration: none; display: block; padding: 0.3em;}
        a:hover { background-color: #ffc; }
        a.create { color: green; }
        a.remove { color: red; }
        a.kuku { display: inline; color: black; }
        tr.row:hover { background-color: #eee; }
        td.cell { text-align: center; }
        th.cell { text-align: left; }
    </style>
</head>
<body>
<h1>SymLinker</h1>

<p><b class="dest">ROOT: <?php echo ROOT_PATH; ?></b></p>
<table width="100%" border="1">
    <tr>
        <th class="cell">Path =&gt;</th>
        <th class="cell">=&gt; Alias</th>
        <th>Exists</th>
        <th>Linked</th>
        <th>Action</th>
    </tr>
    <?php

    foreach($symlinkList as $symBase => $paths)
    {
        ?>
        <tr>
            <td colspan="5"><b class="src">SOURCE: <?php echo $symBase; ?></b></td>
        </tr>
        <?php

        foreach($paths as $path => $alias)
        {
            $src = SlkFileinfo::getInfo($symBase, $path, $alias);
            $dest = SlkFileinfo::getInfo(ROOT_PATH, $path, $alias);

            if('' == $alias)
                $alias = $path;

            $link = '&sym_base='.$symBase
                . '&sym_path='.$path
                . '&alias='.$alias;

            $srcExists =($src->exists) ? 'yes' : '<b style="color: red;">NO</b>';
            $destIsLink =($dest->isLink) ? 'yes' : 'no';

            if(true == $dest->isLink)
            {
                $action = '<a class="remove" href="symlinker.php?task=remove'.$link.'">Remove</a>';
            }
            else if(true == $src->exists)
            {
                $action = '<a class="create" href="symlinker.php?task=create'.$link.'">Create</a>';
            }
            else
            {
                $action = '---';
            }

            ?>
            <tr class="row">
                <td><b class="src">SOURCE</b>/<?php echo $path; ?></td>
                <td>=&gt; <b class="dest">ROOT</b>/<?php echo $alias; ?></td>
                <td class="cell">
                    <?php echo $srcExists; ?>
                </td>
                <td class="cell">
                    <?php echo $destIsLink; ?>
                </td>
                <td class="cell">
                    <?php echo $action; ?>
                </td>
            </tr>
            <?php
        }//foreach
    }//foreach

    ?>
</table>
<p style="text-align:center;"><a style="color: green;" href="symlinker.php">Reload</a></p>

<p><small>Just in case: This is @license GPL &bull; <a class="kuku" href="https://github.com/elkuku">El KuKu</a> <tt>=;)</tt></small></p>

</body>
</html>

<?php
