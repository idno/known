<?php

    namespace IdnoPlugins\Photo {

        class Main extends \Idno\Common\Plugin {

            function registerPages() {
                \Idno\Core\Idno::site()->addPageHandler('/photo/edit/?', '\IdnoPlugins\Photo\Pages\Edit');
                \Idno\Core\Idno::site()->addPageHandler('/photo/edit/([A-Za-z0-9]+)/?', '\IdnoPlugins\Photo\Pages\Edit');
                \Idno\Core\Idno::site()->addPageHandler('/photo/delete/([A-Za-z0-9]+)/?', '\IdnoPlugins\Photo\Pages\Delete');
            }

            /**
             * Get the total file usage
             * @param bool $user
             * @return int
             */
            function getFileUsage($user = false) {

                $total = 0;

                if (!empty($user)) {
                    $search = ['user' => $user];
                } else {
                    $search = [];
                }

                if ($photos = Photo::get($search,[],9999,0)) {
                    foreach($photos as $photo) {
                        /* @var Photo $photo */
                        if ($photo instanceof Photo) {
                            if ($attachments = $photo->getAttachments()) {
                                foreach($attachments as $attachment) {
                                    $total += $attachment['length'];
                                }
                            }
                        }
                    }
                }

                return $total;

            }

        }

    }