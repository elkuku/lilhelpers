<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jtester
 * Date: 6/16/12
 * Time: 8:28 PM
 * To change this template use File | Settings | File Templates.
 */

spl_autoload_register('symlinkerLibLoader', true, true);

function symlinkerLibLoader($name)
{
    if(0 !== strpos($name, 'Slk'))
        return;

    $parts = preg_split('/(?<=[a-z])(?=[A-Z])/x', substr($name, 3));

    $path = __DIR__.'/'.strtolower(implode('/', $parts)).'.php';

    if(file_exists($path))
    {
        include $path;

        return;
    }

}
