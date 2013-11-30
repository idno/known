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

        // We'll keep track of extensions to templates here
        public $extensions = array();

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
         * Extension-aware version of the template drawing function
         *
         * @param string $templateName
         * @param bool $returnBlank
         * @return \Bonita\false|string
         */
        function draw($templateName, $returnBlank = true) {
            $result = parent::draw($templateName, $returnBlank);
            if (!empty($this->extensions[$templateName])) {
                foreach($this->extensions[$templateName] as $template) {
                    $result .= parent::draw($template, $returnBlank);
                }
            }
            return $result;
        }

        /**
         * Draw syndication buttons relating to a particular content type
         * @param $content_type
         * @return \Bonita\false|string
         */
        function drawSyndication($content_type) {
            return $this->__(['services' => \Idno\Core\site()->syndication()->getServices($content_type)])->draw('content/syndication');
        }

        /**
         * Draws generic pagination suitable for placing somewhere on a page (offset is drawn from the 'offset' input variable)
         * @param int $count Number of items in total (across all pages)
         * @param int $items_per_page Number of items you're displaying per page
         * @return \Bonita\false|string
         */
        function drawPagination($count, $items_per_page = null) {

            if ($items_per_page == null) $items_per_page = \Idno\Core\site()->config()->items_per_page;
            $page = \Idno\Core\site()->currentPage();
            $offset = (int) $page->getInput('offset');
            if ($offset == 0 && $count < $items_per_page) {
                // Do nothing (maybe later we'll add another behavior)
            } else {
                return $this->__(['count' => $count, 'offset' => $offset, 'items_per_page' => $items_per_page])->draw('shell/pagination');
            }

        }

        /**
         * Extend a template with another template. eg, template "plugin/atemplate"
         * could extend "core/atemplate"; if this is the case, the results of
         * $template->draw('plugin/atemplate') will be automatically appended to
         * the end of the results of $template->draw('core/atemplate').
         *
         * @param string $templateName
         * @param string $extensionTemplateName
         */
        function extendTemplate($templateName, $extensionTemplateName) {
            if (empty($this->extensions[$templateName])) {
                $this->extensions[$templateName] = [];
            }
            $this->extensions[$templateName][] = $extensionTemplateName;
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
         * Link any hashtags in the text
         * @param $text
         * @return string
         */
        function parseHashtags($text) {
            $r = preg_replace_callback('/(?<!=)(?<!["\'])(\#[A-Za-z0-9]+)/i',function($matches) {
                $url = $matches[1];
                return '<a href="'.\Idno\Core\site()->config()->url . 'search/?q=' . urlencode($matches[1]) . '" class="p-category">' . $url . '</a>';
            }, $text);
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