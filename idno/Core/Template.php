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
         * @param stirng $text
         * @param string $code Optionally, code to inject into the anchor tag (eg to add classes). '%URL%' is replaced with the URL. Default: blank.
         * @return string
         */
        function parseURLs($text, $code = '') {
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
                        $code = str_replace("%URL%",$url,"'.addslashes($code).'");
                        return "<a href=\"{$url}\" {$code}>{$urltext}</a>{$punc}";
                    '
                ), $text);

            return $r;
        }

        /**
         * Returns a sanitized version of the current page URL
         * @return string
         */
        function getCurrentURL() {
            return \Idno\Core\site()->config()->url . substr($_SERVER['REQUEST_URI'],1);
        }

        /**
         * Returns a version of the current page URL with the specified variable removed from the address line
         * @param string $variable_name
         * @return string
         */
        function getCurrentURLWithoutVar($variable_name) {
            $components = parse_url($this->getCurrentURL());
            parse_str($components['query'],$url_var_array);
            if (!empty($url_var_array[$variable_name])) unset($url_var_array[$variable_name]);
            $components['query'] = http_build_query($url_var_array);
            $url = $components['scheme'] . '://' . $components['host'] . $components['path'];
            if (!empty($components['query'])) $url .= '?' . $components['query'];
            return $url;
        }

        /**
         * Returns a version of the current page URL with the specified variable added to the address line
         * @param string $variable_name
         * @param string $variable_value
         * @return string
         */
        function getCurrentURLWithVar($variable_name, $variable_value) {
            $components = parse_url($this->getCurrentURL());
            parse_str($components['query'],$url_var_array);
            $url_var_array[$variable_name] = $variable_value;
            $components['query'] = http_build_query($url_var_array);
            $url = $components['scheme'] . '://' . $components['host'] . $components['path'];
            if (!empty($components['query'])) $url .= '?' . $components['query'];
            return $url;
        }

    }

}