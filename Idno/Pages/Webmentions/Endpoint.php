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

            function getContent() {
                echo ':)';
            }

            function postContent() {

                $source = $this->getInput('source');
                $target = $this->getInput('target');

                // Check that both source and target are non-empty
                if (!empty($source) && !empty($target)) {
                    // TODO check that source exists, parse it for mf2 content,
                    // and ensure that it genuinely mentions this page

                    // Get the page handler for target
                    if ($page = \Idno\Core\site()->getPageHandler($target)) {
                        $page->setInput('source', $source);
                        $page->setInput('target', $target);
                        if ($page->webmentionContent()) {
                            $this->setResponse(202);    // Webmention received a-ok.
                            exit;
                        }
                    }
                }
                $this->setResponse(400);    // Webmention failed.
            }

        }

    }