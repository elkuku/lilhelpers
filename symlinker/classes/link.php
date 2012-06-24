<?php
/**
 * @package    SymLinker
 * @subpackage Classes
 * @author     Nikolai Plath {@link https://github.com/elkuku}
 * @author     Created on 24-Jun-2012
 * @license    GNU/GPL
 */

/**
 * Link base class.
 */
class SlkLink
{
    /**
     * @var SlkFileinfo
     */
    public $source = null;

    /**
     * @var SlkFileinfo
     */
    public $destination = null;

    public $path = '';

    public $alias = '';

    public $createLink = '';
}
