<?php

    namespace Themes\Green {

        class Controller extends \Idno\Common\Theme {

            /**
             * Sets the page owner on the homepage
             */
            function init() {
                \Idno\Core\site()->events()->addListener('page/get',function(\Idno\Core\Event $event) {
                    if ($event->data()['page_class'] == 'Idno\Pages\Homepage') {
                        \Idno\Core\site()->currentPage()->setOwner(\Idno\Entities\User::getOne(['admin' => 1]));
                    }
                });
            }

        }

    }