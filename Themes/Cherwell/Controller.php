<?php

    namespace Themes\Cherwell {

        use Idno\Entities\User;

        class Controller extends \Idno\Common\Theme {

            /**
             * Sets the page owner on the homepage
             */
            function init() {

                \Idno\Core\Idno::site()->events()->addListener('page/get',function(\Idno\Core\Event $event) {
                    if ($event->data()['page_class'] == 'Idno\Pages\Homepage') {
                        if (!empty(\Idno\Core\Idno::site()->config()->cherwell['profile_user'])) {
                            if ($profile_user = User::getByHandle(\Idno\Core\Idno::site()->config()->cherwell['profile_user'])) {
                                \Idno\Core\Idno::site()->currentPage()->setOwner($profile_user);
                            }
                        }
                        if (empty($profile_user)) {
                            if (\Idno\Entities\User::count(['admin' => true]) == 1) {
                                \Idno\Core\Idno::site()->currentPage()->setOwner(\Idno\Entities\User::getOne(['admin' => true]));
                            }
                        }
                    }
                });

                \Idno\Core\Idno::site()->addPageHandler('/admin/cherwell/?','Themes\Cherwell\Pages\Admin');

            }

            /**
             * Retrieve the background image URL
             * @return string
             */
            static function getBackgroundImageURL() {

                if (!empty(\Idno\Core\Idno::site()->config()->cherwell['bg_id'])) {
                    return \Idno\Core\Idno::site()->config()->getDisplayURL() . 'file/' . \Idno\Core\Idno::site()->config()->cherwell['bg_id'];
                } else {
                    return \Idno\Core\Idno::site()->config()->getDisplayURL() . 'Themes/Cherwell/img/cherwell.jpg';
                }

            }

        }

    }