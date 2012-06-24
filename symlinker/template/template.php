<?php
/**
 * @package    SymLinker
 * @subpackage Template
 * @author     Nikolai Plath {@link https://github.com/elkuku}
 * @author     Created on 24-Jun-2012
 * @license    GNU/GPL
 */

$symlinkList = SlkLinker::getList();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>SymLinker</title>
    <style type="text/css">
        <?php include 'assets/css/symlinker.css'; ?>
    </style>
</head>
<body>
<h1>SymLinker</h1>

<p><b class="dest">ROOT: <?php echo ROOT_PATH; ?></b></p>

<table>
    <tr>
        <th class="cell">Source Path &rArr;</th>
        <th class="cell">&rArr; Destination / Alias</th>
        <th>Exists</th>
        <th>Linked</th>
        <th>Action</th>
    </tr>
    <?php

    foreach($symlinkList as $symBase => $symLinks)
    {
        ?>
        <tr>
            <td colspan="5"><b class="src">SRC: <?php echo $symBase; ?></b></td>
        </tr>
        <?php
        /* @var SlkLink $symLink */
        foreach($symLinks as $symLink)
        {
            if(true == $symLink->destination->isLink) :
                $action = '<a class="remove" href="symlinker.php?task=remove'.$symLink->createLink.'">Remove</a>';
            elseif(true == $symLink->source->exists) :
                $action = '<a class="create" href="symlinker.php?task=create'.$symLink->createLink.'">Create</a>';
            else :
                $action = '---';
            endif;

            ?>
            <tr class="row">
                <td><b class="src">SRC</b>/<?php echo $symLink->path; ?></td>
                <td>&rArr; <b class="dest">ROOT</b>/<?php echo $symLink->alias; ?></td>
                <td class="cell">
                    <?php echo ($symLink->source->exists) ? 'yes' : '<b style="color: red;">NO</b>'; ?>
                </td>
                <td class="cell">
                    <?php echo ($symLink->destination->isLink) ? 'yes' : 'no'; ?>
                </td>
                <td class="cell">
                    <?php echo $action; ?>
                </td>
            </tr>
            <?php
        }
    }

    ?>
</table>
<p style="text-align:center;"><a style="color: green;" href="symlinker.php">Reload</a></p>

<p>
    <small>Just in case: This is @licensed GPL and made 2009 by; <a class="kuku" href="https://github.com/elkuku">El
        KuKu</a> <tt>=;)</tt></small>
</p>
</body>
</html>
