<?php

/**
 * Template management class
 *
 * @package idno
 * @subpackage core
 */

namespace Idno\Core {

    class Template extends \Bonita\Templates
    {

        /**
         * On construction, detect the template type
         */
        function __construct($template = false)
        {
            if (!($template instanceof Template)) {
                $this->detectTemplateType();
            }
            return parent::__construct($template);
        }

        /**
         * Automatically adds paragraph tags (etc) to a given piece of unformatted or semi-formatted text.
         * @param $html
         * @return \false|string
         */
        function autop($html) {
            require_once dirname(dirname(dirname(__FILE__))) . '/external/MrClay_AutoP/AutoP.php';
            $autop = new \MrClay_AutoP();
            return $autop->process($html);
        }

        /**
         * Automatically links URLs embedded in a piece of text
         * @param $text
         * @return string
         */
        function parse_urls($text) {
            $r = preg_replace_callback('/(?<!=)(?<!["\'])((ht|f)tps?:\/\/[^\s\r\n\t<>"\'\(\)]+)/i',
                create_function(
                    '$matches',
                    '
                        $url = $matches[1];
                        $punc = \'\';
                        $last = substr($url, -1, 1);
                        if (in_array($last, array(".", "!", ","))) {
                            $punc = $last;
                            $url = rtrim($url, ".!,");
                        }
                        $urltext = str_replace("/", "/<wbr />", $url);
                        return "<a href=\"{$url}\" >{$urltext}</a>{$punc}";
                    '
                ), $text);

            return $r;
        }
    }

}