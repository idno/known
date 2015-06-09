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
                    $t = \Idno\Core\site()->template();
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