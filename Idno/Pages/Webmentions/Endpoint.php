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
                $t = \Idno\Core\site()->template();
                $t->__(['title' => 'Webmention endpoint', 'body' => $t->draw('pages/webmention')])->drawPage();
            }

            function post()
            {

                parse_str(trim(file_get_contents("php://input")), $vars);

                // Check that both source and target are non-empty
                if (!empty($vars['source']) && !empty($vars['target']) && $vars['source'] != $vars['target']) {

                    // Sanitize source and target
                    $source = urldecode($vars['source']);
                    $target = urldecode($vars['target']);

                    // Remove anchors from target URL, but save them to '#' input so we can still reference them later
                    if (strpos($target, '#')) {
                        list($target, $fragment) = explode('#', $target, 2);
                        if (!empty($fragment)) {
                            $this->setInput('#', $fragment);
                        }
                    }

                    // Get the page handler for target
                    if ($page = \Idno\Core\site()->getPageHandler($target)) {
                        // First of all, make sure the target page isn't the source page. Let's not webmention ourselves!
                        $webmention_ok = true;
                        if (\Idno\Common\Entity::isLocalUUID($source)) {
                            if ($source_page = \Idno\Core\site()->getPageHandler($source)) {
                                if ($source_page == $page) {
                                    $webmention_ok = false;
                                }
                            }
                        }
                        // Check that source exists, parse it for mf2 content,
                        // and ensure that it genuinely mentions this page
                        if ($webmention_ok) {
                            if ($source_content = \Idno\Core\Webservice::get($source)) {
                                if (substr_count($source_content['content'], $target) || $source_content['response'] == 410) {
                                    $source_mf2 = \Idno\Core\Webmention::parseContent($source_content['content'], $source);
                                    // Set source and target information as input variables
                                    $page->setPermalink();
                                    if ($page->webmentionContent($source, $target, $source_content, $source_mf2)) {
                                        $this->setResponse(202); // Webmention received a-ok.
                                        exit;
                                    } else {
                                        $error      = 'target_not_supported';
                                        $error_text = 'This is not webmentionable.';
                                    }
                                } else {
                                    $error      = 'no_link_found';
                                    $error_text = 'The source URI does not contain a link to the target URI.';
                                    \Idno\Core\site()->logging->log('No link from ' . $source . ' to ' . $target, LOGLEVEL_ERROR);
                                }
                            } else {
                                $error      = 'source_not_found';
                                $error_text = 'The source content could not be obtained.';
                                \Idno\Core\site()->logging->log('No content from ' . $source, LOGLEVEL_ERROR);
                            }
                        } else {
                            $error      = 'target_not_supported';
                            $error_text = 'A page can\'t webmention itself.';
                        }
                    } else {
                        $error      = 'target_not_found';
                        $error_text = 'The target page does not exist.';
                    }
                }
                $this->setResponse(400); // Webmention failed.
                echo json_encode(['error' => $error, 'error_text' => $error_text]);
            }

        }

    }