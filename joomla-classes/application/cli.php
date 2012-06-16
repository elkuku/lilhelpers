<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jtester
 * Date: 6/16/12
 * Time: 7:32 PM
 * To change this template use File | Settings | File Templates.
 */

class KukuApplicationCli extends JApplicationCli
{
    protected $verbose = true;

    /**
     * Class constructor.
     *
     * @param   mixed  $input       An optional argument to provide dependency injection for the application's
     *                              input object.  If the argument is a JInputCli object that object will become
     *                              the application's input object, otherwise a default input object is created.
     * @param   mixed  $config      An optional argument to provide dependency injection for the application's
     *                              config object.  If the argument is a JRegistry object that object will become
     *                              the application's config object, otherwise a default config object is created.
     * @param   mixed  $dispatcher  An optional argument to provide dependency injection for the application's
     *                              event dispatcher.  If the argument is a JEventDispatcher object that object will become
     *                              the application's event dispatcher, if it is null then the default event dispatcher
     *                              will be created based on the application's loadDispatcher() method.
     *
     * @see     loadDispatcher()
     * @since   11.1
     */
    public function __construct(JInputCli $input = null, JRegistry $config = null, JEventDispatcher $dispatcher = null)
    {
        parent::__construct($input, $config, $dispatcher);

        if($this->input->get('nocolors'))
        {
            define('COLORS', 0);
        }
        else
        {

            //-- Search PEAR's ConsoleColor2
            if(false == class_exists('Console_Color2')) @include 'Console/Color2.php';

            //-- Any color ?
            define('COLORS', class_exists('Console_Color2'));
        }

        $this->verbose = ($this->input->get('q', $this->input->get('quiet'))) ? false : true;
    }

    /**
     * Write a string to standard output.
     *
     * @param string $text The text to display
     * @param bool   $nl   Should a new line be printed.
     * @param string $fg   Foreground color.
     * @param string $bg   Background color.
     * @param string $style
     *
     * @return KukuApplicationCli
     */
    public function output($text = '', $nl = true, $fg = '', $bg = '', $style = '')
    {
        if(false == $this->verbose)
            return $this;

        static $color = null;

        if(is_null($color))
            $color = new Console_Color2;

        if($fg && COLORS) $this->out($color->fgcolor($fg), false);
        if($bg && COLORS) $this->out($color->bgcolor($bg), false);

        if($style && COLORS)
        {
            $cs = $color->getColorCodes();
            //	var_dump($cs);
            $this->out("\033[".$cs['style'][$style].'m', false);
        }

        $this->out($text, $nl);

        if(($fg || $bg || $style) && COLORS) $this->out($color->convert('%n'), false);

        return $this;
    }


}
