<?php

    namespace IdnoPlugins\Like\Pages {

        use Idno\Common\Page;
        use IdnoPlugins\Like\Like;

        class Callback extends Page {

            function getContent() {

                $this->gatekeeper();
                if ($url = $this->getInput('url')) {

                    \Idno\Core\Idno::site()->logging()->debug("Attempting to pull title from $url");
                    
                    $like = new Like();
                    $title = $like->getTitleFromURL($url);
                    
                    \Idno\Core\Idno::site()->logging()->debug("Title has been pulled: $title");

                    if (strlen($title) > 128) {
                        $title = '';    // Don't return overlong titles
                    }

                    $t = \Idno\Core\Idno::site()->template();
                    $t->setTemplateType('json');
                    $t->__([
                        'title' => \Idno\Core\Idno::site()->language()->_('URL to page title callback'),
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