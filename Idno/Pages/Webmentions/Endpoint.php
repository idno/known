<?php

    /**
     * Webmentions endpoint
     */

namespace Idno\Pages\Webmentions {

    /**
     * Class to serve the webmention endpoint
     */
    class Endpoint extends \Idno\Common\Page
    {

        function getContent()
        {
            $t = \Idno\Core\Idno::site()->template();
            $content = $t->__(
                [
                'title' => \Idno\Core\Idno::site()->language()->_('Webmention endpoint'),
                'body' => $t->draw('pages/webmention')
                ]
            )->drawPage(false);
            \Idno\Core\Idno::site()->response()->setContent($content);
        }

        function post()
        {

            parse_str(trim(file_get_contents("php://input")), $vars);

            // Check that both source and target are non-empty
            if (!empty($vars['source']) && !empty($vars['target']) && $vars['source'] != $vars['target']) {

                $source = $vars['source'];
                $target = $vars['target'];

                \Idno\Core\Idno::site()->logging()->debug("received webmention from $source to $target");

                // Remove anchors from target URL, but save them to '#' input so we can still reference them later
                if (strpos($target, '#')) {
                    list($target, $fragment) = explode('#', $target, 2);
                    if (!empty($fragment)) {
                        $this->setInput('#', $fragment);
                    }
                }

                // If the target is a bare domain with no path, add /
                $route = $target;
                if (!parse_url($route, PHP_URL_PATH)) {
                    $route .= '/';
                }

                // Get the page handler for target
                if ($page = \Idno\Core\Idno::site()->routes()->getRoute($route)) {
                    // First of all, make sure the target page isn't the source page. Let's not webmention ourselves!
                    $webmention_ok = true;
                    if (\Idno\Common\Entity::isLocalUUID($source)) {
                        if ($source_page = \Idno\Core\Idno::site()->routes()->getRoute($source)) {
                            if ($source_page == $page) {
                                $webmention_ok = false;
                            }
                        }
                    }

                    // Check that source exists, parse it for mf2 content,
                    // and ensure that it genuinely mentions this page
                    if ($webmention_ok) {
                        \Idno\Core\Idno::site()->logging()->debug("webmention is ok with target page " . get_class($page));
                        if ($source_response = \Idno\Core\Webservice::get($source)) {
                            if (substr_count($source_response['content'], $target) || $source_response['response'] == 410) {
                                $source_mf2 = \Idno\Core\Webmention::parseContent($source_response['content'], $source);
                                // Set source and target information as input variables
                                $page->setPermalink();
                                if ($page->webmentionContent($source, $target, $source_response, $source_mf2)) {
                                    $this->setResponse(202); // Webmention received a-ok.
                                    exit;
                                } else {
                                    $error      = 'source_not_supported';
                                    $error_text = 'Could not interpret source as a comment.';
                                }
                            } else {
                                $error      = 'no_link_found';
                                $error_text = 'The source URI does not contain a link to the target URI.';
                                \Idno\Core\Idno::site()->logging()->warning('No link from ' . $source . ' to ' . $target);
                            }
                        } else {
                            $error      = 'source_not_found';
                            $error_text = 'The source content for ' . $source . ' could not be obtained.';
                            \Idno\Core\Idno::site()->logging()->warning('No content from '.$source);
                        }
                    } else {
                        $error      = 'target_not_supported';
                        $error_text = 'A page can\'t webmention itself.';
                    }
                } else {
                    $error      = 'target_not_found';
                    $error_text = 'The target page ' . $target . ' does not exist.';
                    \Idno\Core\Idno::site()->logging()->error('Could not find handler for ' . $target);
                }
            }
            $this->setResponse(400); // Webmention failed.
            if (empty($error)) {
                $error      = 'unknown_error';
                $error_text = 'Not all the required webmention variables were set.';
            }
            \Idno\Core\Idno::site()->response()->setJsonContent(json_encode(array('error' => $error, 'error_text' => $error_text)));
        }

    }

}

