<?php

    namespace IdnoPlugins\Like\Pages {

        use Idno\Common\Page;
        use IdnoPlugins\Like\Like;

        class Callback extends Page {

            function getContent() {

                $this->gatekeeper();
                if ($url = $this->getInput('url')) {

                    $like = new Like();
                    $title = $like->getTitleFromURL($url);

                    if (strlen($title) > 128) {
                        $title = '';    // Don't return overlong titles
                    }

                    $t = \Idno\Core\Idno::site()->template();
                    $t->setTemplateType('json');
                    $t->__([
                        'title' => 'URL to page title callback',
                        'body' => '',
                        'value' => trim($title)
                    ])->drawPage();

                }

            }

            function post() {

                $this->getContent();

            }

        }

    }