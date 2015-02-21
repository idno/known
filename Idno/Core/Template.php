<?php

    /**
     * Template management class
     *
     * @package idno
     * @subpackage core
     */

    namespace Idno\Core {

        use Idno\Entities\User;

        class Template extends \Bonita\Templates
        {

            // We'll keep track of extensions to templates here
            public $extensions = array();

            // We'll keep track of prepended templates here
            public $prepends = array();

            // We'll keep track of replaced templates here
            public $replacements = array();

            // We can also extend templates with HTML or other content
            public $rendered_extensions = array();

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
             * @param bool $returnBlank Should we return a blank string if the template doesn't exist? (Defaults to true)
             * @param book $replacements Should we honor template replacements? (Defaults to true)
             * @return \Bonita\false|string
             */
            function draw($templateName, $returnBlank = true, $replacements = true)
            {
                $result = '';
                if (!empty($this->prepends[$templateName])) {
                    foreach ($this->prepends[$templateName] as $template) {
                        $result .= parent::draw($template, $returnBlank);
                    }
                }
                if (!empty($this->replacements[$templateName]) && $replacements == true) {
                    $result .= parent::draw($this->replacements[$templateName], $returnBlank);
                } else {
                    $result .= parent::draw($templateName, $returnBlank);
                }
                if (!empty($this->extensions[$templateName])) {
                    foreach ($this->extensions[$templateName] as $template) {
                        $result .= parent::draw($template, $returnBlank);
                    }
                }
                if (!empty($this->rendered_extensions[$templateName])) {
                    $result .= $this->rendered_extensions[$templateName];
                }

                return $result;
            }

            /**
             * Draws the page shell.
             * @param bool $echo
             * @return false|string
             */
            function drawPage($echo = true)
            {

                // Get messages and flush session
                $this->messages = site()->session()->getAndFlushMessages();

                // End session BEFORE we output any data
                session_write_close();

                return parent::drawPage($echo);

            }

            /**
             * Draw syndication buttons relating to a particular content type
             * @param $content_type
             * @return \Bonita\false|string
             */
            function drawSyndication($content_type)
            {
                return $this->__(array('services' => \Idno\Core\site()->syndication()->getServices($content_type), 'content_type' => $content_type))->draw('content/syndication');
            }

            /**
             * Draws generic pagination suitable for placing somewhere on a page (offset is drawn from the 'offset' input variable)
             * @param int $count Number of items in total (across all pages)
             * @param int $items_per_page Number of items you're displaying per page
             * @return string
             */
            function drawPagination($count, $items_per_page = null)
            {

                if ($items_per_page == null) $items_per_page = \Idno\Core\site()->config()->items_per_page;
                $page   = \Idno\Core\site()->currentPage();
                $offset = (int)$page->getInput('offset');
                if ($offset == 0 && $count < $items_per_page) {
                    return '';
                } else {
                    return $this->__(array('count' => $count, 'offset' => $offset, 'items_per_page' => $items_per_page))->draw('shell/pagination');
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
             * @param bool $to_front If set, this will add the template to the beginning of the template queue
             */
            function extendTemplate($templateName, $extensionTemplateName, $to_front = false)
            {
                if (empty($this->extensions[$templateName])) {
                    $this->extensions[$templateName] = array();
                }
                if ($to_front) {
                    array_unshift($this->extensions[$templateName], $extensionTemplateName);
                } else {
                    $this->extensions[$templateName][] = $extensionTemplateName;
                }
            }

            /**
             * Prepend a template with another template. eg, template "plugin/atemplate"
             * could extend "core/atemplate"; if this is the case, the results of
             * $template->draw('plugin/atemplate') will be automatically prepended to
             * the end of the results of $template->draw('core/atemplate').
             *
             * @param string $templateName
             * @param string $prependTemplateName
             * @param bool $to_front If set, this will add the template to the beginning of the template queue
             */
            function prependTemplate($templateName, $prependTemplateName, $to_front = false)
            {
                if (empty($this->prepends[$templateName])) {
                    $this->prepends[$templateName] = array();
                }
                if ($to_front) {
                    array_unshift($this->prepends[$templateName], $prependTemplateName);
                } else {
                    $this->prepends[$templateName][] = $prependTemplateName;
                }
            }

            /**
             * Replace a core template with another template. eg, template "plugin/atemplate"
             * could replace "core/atemplate"; if this is the case, the results of
             * $template->draw('plugin/atemplate') will be displayed instead of
             * $template->draw('core/atemplate'). Usually this isn't required - Known replaces
             * templates automatically if you create one in your plugin with the same name -
             * but this function enables conditional replacements.
             *
             * @param string $templateName
             * @param string $extensionTemplateName
             * @param bool $to_front If set, this will add the template to the beginning of the template queue
             */
            function replaceTemplate($templateName, $replacementTemplateName)
            {
                if (empty($this->replacements[$templateName])) {
                    $this->replacements[$templateName] = array();
                }
                $this->replacements[$templateName] = $replacementTemplateName;
            }

            /**
             * Extends a given template with pre-rendered content. All pre-rendered content will be drawn after
             * template-driven content.
             * @param $templateName
             * @param $content
             */
            function extendTemplateWithContent($templateName, $content)
            {
                if (empty($this->rendered_extensions[$templateName])) {
                    $this->rendered_extensions[$templateName] = $content;
                } else {
                    $this->rendered_extensions[$templateName] .= $content;
                }
            }

            /**
             * Automatically adds paragraph tags (etc) to a given piece of unformatted or semi-formatted text.
             * @param $html
             * @return \false|string
             */
            function autop($html)
            {
                $html = site()->triggerEvent('text/format', [], $html);
                require_once dirname(dirname(dirname(__FILE__))) . '/external/MrClay_AutoP/AutoP.php';
                $autop = new \MrClay_AutoP();
                
                return $this->sanitize_html($autop->process($html));
            }
            
            /**
             * Sanitize HTML in a large block of text, removing XSS and other vulnerabilities.
             * This works by calling the text/filter event, note that currently there is no native implementation.
             * @param type $html
             */
            function sanitize_html($html) {
                return site()->triggerEvent('text/filter', [], $html);
            }

            /**
             * Wrapper for those on UK spelling.
             * @param $html
             * @return mixed
             */
            function sanitise_html($html) {
                return $this->sanitize_html($html);
            }

            /**
             * Automatically links URLs embedded in a piece of text
             * @param stirng $text
             * @param string $code Optionally, code to inject into the anchor tag (eg to add classes). '%URL%' is replaced with the URL. Default: blank.
             * @return string
             */
            function parseURLs($text, $code = '')
            {
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
                            $code = str_replace("%URL%",$url,"' . addslashes($code) . '");
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
            function parseHashtags($text)
            {
                $r = preg_replace_callback('/(?<=^|[\>\s\n])(\#[\w0-9]+)/iu', function($matches) {
                    $url = $matches[1];
                    $tag = str_replace('#','',$matches[1]);

                    if (preg_match('/\#[A-Fa-f0-9]{6}/', $matches[1])) {
                        return $matches[1];
                    }

                    return '<a href="' . \Idno\Core\site()->config()->getDisplayURL() . 'tag/' . urlencode($tag) . '" class="p-category" rel="tag">' . $url . '</a>';
                }, $text);

                return $r;
            }

            /**
             * Given a URL, fixes it to have a prefix if it needs one
             * @param $url
             * @return string
             */
            function fixURL($url)
            {
                return (
                    substr($url, 0, 7) == 'http://' ||
                    substr($url, 0, 8) == 'https://' ||
                    substr($url, 0, 1) == '@' ||
                    substr($url, 0, 7) == 'mailto:' ||
                    substr($url, 0, 4) == 'tel:' ||
                    substr($url, 0, 4) == 'sms:' ||
                    substr($url, 0, 6) == 'skype:' ||
                    substr($url, 0, 5) == 'xmpp:' ||
                    substr($url, 0, 5) == 'facetime:'
                )
                    ? $url
                    : 'http://' . $url;
            }

            /**
             * Return a schema-less version of the given URL
             *
             * @param $url
             * @return mixed
             */
            function makeDisplayURL($url)
            {
                $scheme = parse_url($url, PHP_URL_SCHEME);
                if (site()->isSecure()) {
                    $newuri = 'https:';
                } else {
                    $newuri = 'http:';
                }
                return str_replace($scheme . ':', $newuri, $url);
            }

            /**
             * Change @user links into active users.
             * @param type $text The text to parse
             * @param type $in_reply_to If specified, the function will make a (hopefully) sensible guess as to where the user is located
             */
            function parseUsers($text, $in_reply_to = null)
            {

                $r = $text;

                if (!empty($in_reply_to)) {

                    // TODO: do this in a more pluggable way

                    // It is only safe to make assumptions on @users if only one reply to is given
                    if (!is_array($in_reply_to) || (is_array($in_reply_to) && count($in_reply_to) == 1)) {

                        if (is_array($in_reply_to))
                            $in_reply_to = $in_reply_to[0];

                        $r = preg_replace_callback('/(?<=^|[\>\s\n])(\@[\w0-9\_]+)/i', function ($matches) use ($in_reply_to) {
                            $url = $matches[1];

                            // Find and replace twitter
                            if (strpos($in_reply_to, 'twitter.com') !== false) {
                                return '<a href="https://twitter.com/' . urlencode(ltrim($matches[1], '@')) . '" >' . $url . '</a>';
                            } else {
                                return $url;
                            }
                        }, $text);

                    }

                } else {
                    // No in-reply, so we assume a local user
                    $r = preg_replace_callback('/(?<=^|[\>\s\n])(\@[A-Za-z0-9\_]+)/i', function ($matches) {
                        $url = $matches[1];

                        $username = ltrim($matches[1], '@');

                        if ($user = User::getByHandle($username)) {
                            return '<a href="' . \Idno\Core\site()->config()->url . 'profile/' . urlencode($username) . '" >' . $url . '</a>';
                        } else {
                            return $url;
                        }

                    }, $text);
                }

                return $r;
            }

            /**
             * Returns a sanitized version of the current page URL
             * @return string
             */
            function getCurrentURL()
            {
                return \Idno\Core\site()->config()->url . substr($_SERVER['REQUEST_URI'], 1);
            }

            /**
             * Returns a version of the current page URL with the specified variable removed from the address line
             * @param string $variable_name
             * @return string
             */
            function getCurrentURLWithoutVar($variable_name)
            {
                $components = parse_url($this->getCurrentURL());
                parse_str($components['query'], $url_var_array);
                if (!empty($url_var_array[$variable_name])) unset($url_var_array[$variable_name]);
                $components['query'] = http_build_query($url_var_array);
                $url                 = $components['scheme'] . '://' . $components['host'] . $components['path'];
                if (!empty($components['query'])) $url .= '?' . $components['query'];

                return $url;
            }

            /**
             * Returns a version of the current page URL with the specified variable added to the address line
             * @param string $variable_name
             * @param string $variable_value
             * @return string
             */
            function getURLWithVar($variable_name, $variable_value, $url = '')
            {
                if (empty($url)) {
                    $url = $this->getCurrentURL();
                }
                if ($components = parse_url($url)) {
                    if (!empty($components['query'])) {
                        parse_str($components['query'], $url_var_array);
                    } else {
                        $components['query'] = array();
                    }
                    $url_var_array[$variable_name] = $variable_value;
                    $components['query']           = http_build_query($url_var_array);
                    $url                           = $components['scheme'] . '://' . $components['host'] . $components['path'];
                    if (!empty($components['query'])) $url .= '?' . $components['query'];
                }

                return $url;
            }

        }

    }