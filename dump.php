<?php
class KuKuDump
{

    /**
     * Simple highlight for SQL queries.
     *
     * @param   string  $sql  The query to highlight
     * @param   bool    $styles
     *
     * @return string
     */
    public static function query($sql, $styles = false)
    {
        $newlineKeywords = '#\b(FROM|LEFT|INNER|OUTER|WHERE|SET|VALUES|ORDER|GROUP|HAVING|LIMIT|ON|AND|CASE)\b#i';

        $sql = htmlspecialchars($sql, ENT_QUOTES);

        $sql = preg_replace($newlineKeywords, '<br />&#160;&#160;\\0', $sql);

        $regex = array(

            // Tables are identified by the prefix
            '/(=)/'
            => $styles ? '<b class="dbgOperator">$1</b>' : '<b style="color: orange;">$1</b>',

            // All uppercase words have a special meaning
            '/(?<!\w|>)([A-Z_]{2,})(?!\w)/x'
            => $styles ? '<span class="dbgCommand">$1</span>' : '<b style="color: green;">$1</b>',

            // Tables are identified by the prefix
            '/('.JFactory::getDbo()->getPrefix().'[a-z_0-9]+)/'
            => $styles ? '<span class="dbgTable">$1</span>' : '<b style="color: lime;">$1</b>',

            // Tables are identified by the prefix
            '/(#__[a-z_0-9]+)/'
            => $styles ? '<span class="dbgTable">$1</span>' : '<b style="color: lime;">$1</b>',

        );

        $sql = preg_replace(array_keys($regex), array_values($regex), $sql);

        $sql = str_replace('*', '<b style="color: red;">*</b>', $sql);

        $style = 'background-color: #000; color: #fff; padding: 0.2em; font-family: monospace; font-size: 12px; border-radius: 5px;';

        return $styles ? $sql : '<div style="'.$style.'">'.$sql.'</div>';
    }

}
