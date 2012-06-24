<?php
/**
 * @package    SymLinker
 * @subpackage Classes
 * @author     Nikolai Plath {@link https://github.com/elkuku}
 * @author     Created on 24-Jun-2012
 * @license    GNU/GPL
 */

spl_autoload_register('symlinkerLibLoader', true, true);

function symlinkerLibLoader($name)
{
    if(0 !== strpos($name, 'Slk'))
        return;

    $parts = preg_split('/(?<=[a-z])(?=[A-Z])/x', substr($name, 3));

    $path = __DIR__.'/'.strtolower(implode('/', $parts)).'.php';

    if(file_exists($path))
        include $path;
}
