<?php

    namespace IdnoPlugins\Convoy\Pages {

        class Syndication extends \Idno\Common\Page {

            function getContent() {

                $this->gatekeeper();

                if (\Idno\Core\Idno::site()->hub()) {

                    $result = \Idno\Core\Idno::site()->hub()->makeCall('hub/user/syndication', [
                        'content_type' => $this->getInput('content_type')
                    ]);

                }

                if (!empty($result['content'])) {
                    $content = $result['content'];
                } else {
                    $content = '';
                }

                echo \Idno\Core\Idno::site()->template()->__(['content' => $content])->draw('content/syndication/embed');

            }

            function postContent() {
                $this->getContent();
            }

        }

    }