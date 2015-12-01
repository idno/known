<?php

    namespace Themes\Kandinsky {

        class Controller extends \Idno\Common\Theme {

            /**
             * Sets the page owner on the homepage
             */
            function init() {
                \Idno\Core\Idno::site()->events()->addListener('page/get',function(\Idno\Core\Event $event) {
                    if ($event->data()['page_class'] == 'Idno\Pages\Homepage') {
                        if (\Idno\Entities\User::count(['admin' => true]) == 1) {
                            \Idno\Core\Idno::site()->currentPage()->setOwner(\Idno\Entities\User::getOne(['admin' => 1]));
                        }
                    }
                });
            }

        }

    }