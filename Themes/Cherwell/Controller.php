<?php

    namespace Themes\Cherwell {

        class Controller extends \Idno\Common\Theme {

            /**
             * Sets the page owner on the homepage
             */
            function init() {

                \Idno\Core\site()->events()->addListener('page/get',function(\Idno\Core\Event $event) {
                    if ($event->data()['page_class'] == 'Idno\Pages\Homepage') {
                        \Idno\Core\site()->currentPage()->setOwner(\Idno\Entities\User::getOne(['admin' => true]));
                    }
                });

                \Idno\Core\site()->addPageHandler('/admin/cherwell/?','Themes\Cherwell\Pages\Admin');

            }

            /**
             * Retrieve the background image URL
             * @return string
             */
            static function getBackgroundImageURL() {

                if (!empty(\Idno\Core\site()->config()->cherwell['bg_id'])) {
                    return \Idno\Core\site()->config()->getURL() . 'file/' . \Idno\Core\site()->config()->cherwell['bg_id'];
                } else {
                    return \Idno\Core\site()->config()->getURL() . 'Themes/Cherwell/img/cherwell.jpg';
                }

            }

        }

    }