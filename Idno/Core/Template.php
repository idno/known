<?php

    /**
     * Template management class
     *
     * @package idno
     * @subpackage core
     */

    namespace Idno\Core {

        use Idno\Entities\User;

        class Template extends \Idno\Core\Bonita\Templates
        {

            // We'll keep track of extensions to templates here
            public $extensions = array();

            // We'll keep track of prepended templates here
            public $prepends = array();

            // We'll keep track of replaced templates here
            public $replacements = array();

            // We can also extend templates with HTML or other content
            public $rendered_extensions = array();

            // Keep track of the HTML purifier
            public $purifier = false;


            /**             * On construction, detect the template type
             */
            function __construct($template = false)
            {
                if (!($template instanceof Template)) {
                    $this->detectTemplateType();
                }

                \Idno\Core\Bonita\Main::siteSecret(\Idno\Core\Idno::site()->config()->site_secret);

                $this->purifier = new Purifier();

                return parent::__construct($template);
            }

            /**
             * Extension-aware version of the template drawing function
             *
             * @param string $templateName
             * @param bool $returnBlank Should we return a blank string if the template doesn't exist? (Defaults to true)
             * @param book $replacements Should we honor template replacements? (Defaults to true)
             * @return \Idno\Core\Bonita\false|string
             */
            function draw($templateName, $returnBlank = true, $replacements = true)
            {
                $result = '';
                if (!empty($this->prepends[$templateName])) {
                    foreach ($this->prepends[$templateName] as $template) {
                        $result .= parent::draw($template, $returnBlank);
                    }
                }
                $replaced = false;
                foreach(['*', $this->getTemplateType()] as $templateType) {
                    if (!empty($this->replacements[$templateName][$templateType]) && $replacements == true) {
                        $result .= parent::draw($this->replacements[$templateName][$templateType], $returnBlank);
                        $replaced = true;
                    }
                    if (!empty($this->extensions[$templateName][$templateType])) {
                        foreach ($this->extensions[$templateName][$templateType] as $template) {
                            $result .= parent::draw($template, $returnBlank);
                        }
                    }
                }
                if (!$replaced) {
                    $result .= parent::draw($templateName, $returnBlank);
                }
                if (!empty($this->rendered_extensions[$templateName])) {
                    $result .= $this->rendered_extensions[$templateName];
                }

                if ($templateName == 'shell' && !empty(Idno::site()->config()->filter_shell)) {
                    if (is_array(Idno::site()->config()->filter_shell)) {
                        foreach(Idno::site()->config()->filter_shell as $search => $replace) {
                            $result = str_replace($search, $replace, $result);
                        }
                    }
                }

                if (!empty($result)) return $result;
                if ($returnBlank) return '';

                return false;
            }

            /**
             * Draws the page shell.
             * @param bool $echo
             * @param $shell Optional override of the page shell template to be used
             * @return false|string
             */
            function drawPage($echo = true, $shell = 'shell')
            {

                // Get messages and flush session
                $this->messages = \Idno\Core\Idno::site()->session()->getAndFlushMessages();

                // End session BEFORE we output any data
                session_write_close();

                return parent::drawPage($echo, $shell);

            }

            /**
             * Draw syndication buttons relating to a particular content type
             * @param $content_type
             * @param $posse_links containing Entity::getPosseLinks()
             * @return \Idno\Core\Bonita\false|string
             */
            function drawSyndication($content_type, $posse_links = [])
            {
                return $this->__(array('services'     => \Idno\Core\Idno::site()->syndication()->getServices($content_type),
                                       'content_type' => $content_type,
                                       'posseLinks'   => $posse_links))->draw('content/syndication');
            }

            /**
             * Draws generic pagination suitable for placing somewhere on a page (offset is drawn from the 'offset' input variable)
             * @param int $count Number of items in total (across all pages)
             * @param int $items_per_page Number of items you're displaying per page
             * @param array $vars Additional template variables
             * @return string
             */
            function drawPagination($count, $items_per_page = null, array $vars = [])
            {
                if (empty($vars)) $vars = [];
                if ($items_per_page == null) $items_per_page = \Idno\Core\Idno::site()->config()->items_per_page;
                $page   = \Idno\Core\Idno::site()->currentPage();
                $offset = (int)$page->getInput('offset');
                if ($offset == 0 && $count < $items_per_page) {
                    return '';
                } else {
                    return $this->__(array_merge(array('count' => $count, 'offset' => $offset, 'items_per_page' => $items_per_page), $vars))->draw('shell/pagination');
                }

            }
            
            /**
             * Document a form control and make it easily discoverable by the API.
             * @param type $name Name of the control 
             * @param type $values Array of form value. Common are 'type', 'description', 'id'
             */
            function documentFormControl($name, $values = []) {
                $vars = [];
                if (!empty($this->vars['form-fields'])) {
                    $vars = $this->vars['form-fields'];
                }
                
                if (isset($vars[$name])) {
                    $tmp = [$vars[$name]];
                    $tmp[] = $values;
                    
                    $vars[$name] = $tmp;
                } else {
                    $vars[$name] = $values;
                }
                
                $this->__(['form-fields' => $vars]);
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
            function extendTemplate($templateName, $extensionTemplateName, $to_front = false, $templateType = '*')
            {
                if (empty($this->extensions[$templateName][$templateType])) {
                    $this->extensions[$templateName][$templateType] = array();
                }
                if ($to_front) {
                    array_unshift($this->extensions[$templateName][$templateType], $extensionTemplateName);
                } else {
                    $this->extensions[$templateName][$templateType][] = $extensionTemplateName;
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
             */
            function replaceTemplate($templateName, $replacementTemplateName, $templateType = '*')
            {
                if (empty($this->replacements[$templateName][$templateType])) {
                    $this->replacements[$templateName][$templateType] = array();
                }
                $this->replacements[$templateName][$templateType] = $replacementTemplateName;
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

                return $autop->process($html);
            }

            /**
             * Wrapper for those on UK spelling.
             * @param $html
             * @return mixed
             */
            function sanitise_html($html)
            {
                return $this->sanitize_html($html);
            }

            /**
             * Sanitize HTML in a large block of text, removing XSS and other vulnerabilities.
             * This works by calling the text/filter event, as well as any built-in purifier.
             * @param type $html
             */
            function sanitize_html($html)
            {
                $html = site()->triggerEvent('text/filter', [], $html);

                return $html;
            }

            /**
             * Automatically links URLs embedded in a piece of text
             * @param stirng $text
             * @param string $code Optionally, code to inject into the anchor tag (eg to add classes). '%URL%' is replaced with the URL. Default: blank.
             * @return string
             */
            function parseURLs($text, $code = '')
            {
                $r = preg_replace_callback('/(?<!=)(?<!["\'])((ht|f)tps?:\/\/[^\s<>"\']+)/i', function ($matches) use ($code) {
                    $url  = $matches[1];
                    $punc = '';

                    while ($url) {
                        $last = substr($url, -1, 1);
                        if (strstr('.!?,;:(', $last)
                            // strip ) if there isn't a matching ( earlier in the url
                            || ($last === ')' && !strstr($url, '('))
                        ) {
                            $punc = $last . $punc;
                            $url  = substr($url, 0, -1);
                        } else {
                            break; // found a non-punctuation character
                        }
                    }

                    $result = "<a href=\"$url\"";
                    if (!\Idno\Common\Entity::isLocalUUID($url)) {
                        $result .= " target=\"_blank\" ";
                    }
                    if ($code) {
                        $result .= ' ' . str_replace("%URL%", $url, addslashes($code));
                    }
                    $result .= ">";
                    $result .= preg_replace('/([\/=]+)/', '${1}<wbr />', Template::sampleTextChars($url, 100));
                    $result .= "</a>$punc";

                    return $result;

                }, $text);

                return $r;
            }

            /**
             * Link any hashtags in the text
             * @param $text
             * @return string
             */
            function parseHashtags($text)
            {
                //decode &auml; to ä, but keep < > and & characters
                $text = html_entity_decode(
                    str_replace(
                        ['&amp;', '&lt;', '&gt;'],
                        ['&amp;amp;', '&amp;lt;', '&amp;gt;'],
                        $text
                    )
                );
                $r    = preg_replace_callback('/(?<=^|[\>\s\n])(\#[\p{L}0-9\_]+)/u', function ($matches) {
                    $url = $matches[1];
                    $tag = str_replace('#', '', $matches[1]);

                    if (preg_match('/\#[0-9]{1,3}$/', $matches[1])) {
                        return $matches[1];
                    }

                    if (preg_match('/\#[A-Fa-f0-9]{6}$/', $matches[1])) {
                        return $matches[1];
                    }

                    return '<a href="' . \Idno\Core\Idno::site()->config()->getDisplayURL() . 'tag/' . urlencode($tag) . '" class="p-category" rel="tag">' . $url . '</a>';
                }, $text);

                return $r;
            }

            /**
             * Given HTML text, attempts to return text from the first $paras paragraphs
             * @param $html_text
             * @param int $paras Number of paragraphs to return; defaults to 1
             * @return string
             */
            function sampleParagraph($html_text, $paras = 1)
            {
                $sample = '';
                $dom    = new \DOMDocument;
                $dom->loadHTML($html_text);
                if ($p = $dom->getElementsByTagName('p')) {
                    for ($i = 0; $i < $paras; $i++) {
                        $sample .= $p->item($i)->textContent;
                    }
                }

                return $sample;
            }

            /**
             * Returns a snippet of plain text
             * @param $text
             * @param int $words
             * @return array|string
             */
            function sampleText($text, $words = 32)
            {
                $formatted_text = trim(strip_tags($text));
                $formatted_text = explode(' ', $formatted_text);
                $formatted_text = array_slice($formatted_text, 0, $words);
                $formatted_text = implode(' ', $formatted_text);
                if (strlen($formatted_text) < strlen($text)) $formatted_text .= ' ...';
                return $formatted_text;
            }
            
            /**
             * Return a snippet of plain text based on a number of characters.
             * @param type $text
             * @param type $chars
             */
            function sampleTextChars($text, $chars = 250, $dots = '...') {
                $text = trim(strip_tags($text));
                $length = strlen($text);

                // Short circuit if number of text is less than max chars
                if ($length <= $chars) 
                    return $text;
                
                $formatted_text = substr($text, 0, $chars);
                $space = strrpos($formatted_text, ' ', 0);

                // No space, don't crop
                if ($space === false) 
                    $space = $chars;
                
                $formatted_text = trim(substr($formatted_text, 0, $space));

                if ($length != strlen($formatted_text)) 
                    $formatted_text .= $dots;
                
                return $formatted_text;
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
                    substr($url, 0, 4) == 'sip:' ||
                    substr($url, 0, 4) == 'ssh:' ||
                    substr($url, 0, 8) == 'spotify:' ||
                    substr($url, 0, 8) == 'bitcoin:' ||
                    substr($url, 0, 9) == 'facetime:'
                )
                    ? $url
                    : 'http://' . $url;
            }

            /**
             * Return a schema-less version of the given URL
             *
             * @param $url
             * @param $match_host If set to true (default), only changes the URI if the host matches the site's host
             * @return mixed
             */
            function makeDisplayURL($url, $match_host = true)
            {
                if (Idno::site()->config()->host != parse_url($url, PHP_URL_HOST) && $match_host == true) {
                    return $url;
                }
                $scheme = parse_url($url, PHP_URL_SCHEME);
                if (\Idno\Common\Page::isSSL()) {
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

                $usermatch_regex = '/(?<=^|[\>\s\n\.])(\@[\w0-9\_]+)/i';
                $r = $text;

                if (!empty($in_reply_to)) {

                    // TODO: do this in a more pluggable way

                    // It is only safe to make assumptions on @users if only one reply to is given
                    if (!is_array($in_reply_to) || (is_array($in_reply_to) && count($in_reply_to) == 1)) {

                        if (is_array($in_reply_to))
                            $in_reply_to = $in_reply_to[0];

                        $r = preg_replace_callback($usermatch_regex, function ($matches) use ($in_reply_to) {
                            $url = $matches[1]; 

                            // Find and replace twitter
                            if (strpos($in_reply_to, 'twitter.com') !== false) {
                                return '<a href="https://twitter.com/' . urlencode(ltrim($matches[1], '@')) . '" target="_blank">' . $url . '</a>';
                            // Activate github
                            } else if (strpos($in_reply_to, 'github.com') !== false) {
                                return '<a href="https://github.com/' . urlencode(ltrim($matches[1], '@')) . '" target="_blank">' . $url . '</a>';
                            } else {
                                return \Idno\Core\Idno::site()->triggerEvent("template/parseusers", [
                                    'in_reply_to' => $in_reply_to,
                                    'in_reply_to_domain' => parse_url($in_reply_to, PHP_URL_HOST),
                                    'username' => ltrim($matches[1], '@'),
                                    'match' => $url
                                ], $url);
                            }
                        }, $text);

                    }

                } else {
                    // No in-reply, so we assume a local user
                    $r = preg_replace_callback($usermatch_regex, function ($matches) {
                        $url = $matches[1];

                        $username = ltrim($matches[1], '@'); 

                        if ($user = User::getByHandle($username)) {
                            return '<a href="' . \Idno\Core\Idno::site()->config()->url . 'profile/' . urlencode($username) . '" >' . $url . '</a>';
                        } else {
                            return $url;
                        }

                    }, $text);
                }

                return $r;
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
                $url                 = $components['scheme'] . '://' . $components['host'] . (!empty($components['port']) ? ':' . $components['port'] : '') . $components['path'];
                if (!empty($components['query'])) $url .= '?' . $components['query'];

                return $url;
            }

            /**
             * Returns a sanitized version of the current page URL
             * @return string
             */
            function getCurrentURL()
            {
                $base_url = site()->config()->getDisplayURL();
                $path     = '';
                if ($components = parse_url($base_url)) {
                    if ($components['path'] != '/') {
                        $path = substr($components['path'], 1);
                    }
                }
                $request_uri = substr($_SERVER['REQUEST_URI'], 1);
                if (!empty($path)) {
                    if (substr($request_uri, 0, strlen($path)) == $path) {
                        $request_uri = substr($request_uri, strlen($path));
                    }
                }

                return \Idno\Core\Idno::site()->config()->getDisplayURL() . $request_uri;
            }

            /**
             * Returns a version of the current page URL with the specified variable removed from the address line
             * @param string $variable_name
             * @return string
             */
            function getURLWithoutVar($url, $variable_name)
            {
                if (empty($url)) {
                    $url = $this->getCurrentURL();
                }
                $components = parse_url($url);
                $url_var_array = [];
                if (!empty($components['query'])) parse_str($components['query'], $url_var_array);
                if (!empty($url_var_array[$variable_name])) unset($url_var_array[$variable_name]);
                $components['query'] = http_build_query($url_var_array);
                $url                 = $components['scheme'] . '://' . $components['host'] . (!empty($components['port']) ? ':' . $components['port'] : '') . $components['path'];
                if (!empty($components['query'])) $url .= '?' . $components['query'];

                return $url;
            }

            /**
             * Returns a version of the current page URL with the specified URL variable set to the specified value
             * @param $variable_name
             * @param $value
             * @return string
             */
            function getCurrentURLWithVar($variable_name, $value)
            {
                $components = parse_url($this->getCurrentURL());
                if (isset($components['query'])) {
                    parse_str($components['query'], $url_var_array);
                } else {
                    $url_var_array = [];
                }
                $url_var_array[$variable_name] = $value;
                $components['query']           = http_build_query($url_var_array);
                $url                           = $components['scheme'] . '://' . $components['host'] . (!empty($components['port']) ? ':' . $components['port'] : '') . $components['path'];
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
                $blank_scheme = false;
                if (substr($url, 0, 2) == '//') {
                    $blank_scheme = true;
                    $url          = 'http:' . $url;
                }
                if ($components = parse_url($url)) {
                    if (!empty($components['query'])) {
                        parse_str($components['query'], $url_var_array);
                    } else {
                        $components['query'] = array();
                    }
                    $url_var_array[$variable_name] = $variable_value;
                    $components['query']           = http_build_query($url_var_array);
                    $url                           = $components['scheme'] . '://' . $components['host'] . (!empty($components['port']) ? ':' . $components['port'] : '') . $components['path'];
                    if (!empty($components['query'])) $url .= '?' . $components['query'];
                    if ($blank_scheme) {
                        $url = str_replace($components['scheme'] . ':', '', $url);
                    }
                }

                return $url;
            }
            
            /**
             * Convert a remote image URL into one addressing the local image proxying service.
             * @param url $url
             * @param int Maximum dimensions of proxied image
             * @param string Transformations. Currently only 'square' is supported.
             * @return URL
             */
            public function getProxiedImageUrl($url, $maxsize = null, $transform = null) {
                
                // Local urls, just relay.
                if (\Idno\Common\Entity::isLocalUUID($url))
                    return $url;
                
                // Map to local
                $proxied_url = \Idno\Core\Idno::site()->config()->getDisplayURL() . 'service/web/imageproxy/' . Webservice::base64UrlEncode($url);
                
                if (!empty($maxsize))
                    $proxied_url .= '/' . (int)$maxsize;
                
                if (!empty($transform))
                    $proxied_url .= '/' . $transform;
                
                return $proxied_url;
                
            }
            
            /**
             * Get the modified time of a Known file.
             * Primarily used by cache busting, this method returns when a file was last modified.
             * @param type $file The file, relative to the known path.
             */
            public function getModifiedTS($file) {
                $file = trim($file, '/ ');
                
                $path = \Idno\Core\Idno::site()->config()->getPath();
                
                $ts = filemtime($path . '/' . $file);
                
                return (int)$ts;
            }

            /**
             * Retrieves a set of contextual body classes suitable for including in a shell template
             * @return string
             */
            function getBodyClasses()
            {
                $classes = '';
                $classes .= (str_replace('\\', '_', strtolower(get_class(\Idno\Core\Idno::site()->currentPage()))));
                if ($path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)) {
                    if ($path = explode('/', $path)) {
                        $page_class = '';
                        foreach ($path as $element) {
                            if (!empty($element)) {
                                if (!empty($page_class)) {
                                    $page_class .= '-';
                                }
                                $page_class .= $element;
                                $classes .= ' page-' . $page_class;
                            }
                        }
                    }
                }
                if (\Idno\Core\Idno::site()->session()->isLoggedIn()) {
                    $classes .= ' logged-in';
                } else {
                    $classes .= ' logged-out';
                }

                return $classes;
            }

            /**
             * Returns a version of this template with variable defaults set up for the shell
             * @param $vars
             * @return \Idno\Core\Bonita\Templates
             */
            function formatShellVariables($vars)
            {
                // Get instance of current page for use further down the page
                if ($vars['currentPage'] = \Idno\Core\Idno::site()->currentPage()) {
                    $vars['pageOwner'] = $vars['currentPage']->getOwner();
                }

                if (!empty($currentPage)) {
                    $vars['hidenav'] = \Idno\Core\Idno::site()->embedded();
                }

                $vars['description'] = isset($vars['description']) ? $vars['description'] : '';

                if (empty($vars['title']) && !empty($vars['description'])) {
                    $vars['title'] = implode(' ', array_slice(explode(' ', strip_tags($vars['description'])), 0, 10));
                }

                // Use appropriate language
                $vars['lang'] = 'en';
                if (!empty(\Idno\Core\Idno::site()->config()->lang)) {
                    $vars['lang'] = \Idno\Core\Idno::site()->config()->lang;
                }

                if (empty($vars['title'])) $vars['title'] = '';
                if (empty($vars['body'])) $vars['body'] = '';

                return $this->__($vars);
            }

            /**
             * Sets the template type based on various environmental factors
             */
            function autodetectTemplateType()
            {
                if ($page = site()->currentPage()) {
                    $template = $page->getInput('_t');
                    if (!empty($template)) {
                        site()->template()->setTemplateType($template);
                    } else if ($page->isAcceptedContentType('application/json')) {
                        site()->template()->setTemplateType('json');
                    } else {
                        site()->template()->setTemplateType('default');
                    }
                }
            }

        }

    }
