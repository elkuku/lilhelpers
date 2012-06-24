#!/usr/bin/php
<?php
/**
 * @package    NewVHost
 * @subpackage Base
 * @author     Nikolai Plath {@link https://github.com/elkuku}
 * @author     Created on 27-May-2012
 * @license    GNU/GPL
 */

// We are a valid Joomla entry point.
define('_JEXEC', 1);

// Increase error reporting to that any errors are displayed.
error_reporting(- 1);
ini_set('display_errors', true);

// Setup the base path related constant.
define('JPATH_BASE', dirname(__FILE__));

define('JPATH_SITE', JPATH_BASE);

// Bootstrap the application.
require getenv('JOOMLA_PLATFORM_PATH').'/libraries/import.php';

/**
 * Create new virtual hosts.
 */
class NewVHost extends JApplicationCli
{
    /**
     * Execute the application.
     *
     * @throws Exception
     *
     * @return void
     */
    public function doExecute()
    {
        jimport('joomla.filesystem.folder');
        jimport('joomla.filesystem.file');

        // Print a blank line and new heading.
        $this->out();
        $this->out('New VHost');
        $this->out();

        if(false == isset($this->input->args[0]))
            throw new Exception('Usage: '.$this->input->executable.' target');

        $vhost = $this->input->args[0];

        $webRoot = $this->input->get('webRoot', $this->get('webRoot'));

        $this->out("127.0.0.1\t".$vhost.' >> '.$this->get('pathHosts'));

        $this->out('Include '.$webRoot.'/conf/*.conf >> '.$this->get('pathHttpdConf'));

        $xml = array();
        $xml[] = '';
        $xml[] = '<VirtualHost *:80>';
        $xml[] = '	ServerName '.$vhost;
        $xml[] = '	DocumentRoot '.$webRoot;
        $xml[] = '	ServerAdmin  nobody@'.$vhost;
        $xml[] = '	ServerSignature EMail';
        $xml[] = '</VirtualHost>';
        $xml[] = '';

        JFolder::create($webRoot.'/conf');

        $confPath = $webRoot.'/conf/00local.conf';

        if(false == JFile::exists($confPath))
        {
            $contents = ' ';

            JFile::write($confPath, $contents);
        }
        else
        {
            $contents = JFile::read($confPath);
        }

        $this->out(implode("\n", $xml).' >> '.$confPath);

        $contents .= implode("\n", $xml);

        JFile::write($confPath, $contents);

        $this->out();
        $this->out('Thanks for playing =;)');
        $this->out();
    }
}

try
{
    JApplicationCli::getInstance('NewVHost')->execute();
}
catch(Exception $e)
{
    fwrite(STDOUT, $e->getMessage()."\n");

    exit($e->getCode() ?: 1);
}
