<?php

    namespace IdnoPlugins\Media {

        class Main extends \Idno\Common\Plugin {

            function registerPages() {
                \Idno\Core\Idno::site()->addPageHandler('/media/edit/?', '\IdnoPlugins\Media\Pages\Edit');
                \Idno\Core\Idno::site()->addPageHandler('/media/edit/([A-Za-z0-9]+)/?', '\IdnoPlugins\Media\Pages\Edit');
                \Idno\Core\Idno::site()->addPageHandler('/media/delete/([A-Za-z0-9]+)/?', '\IdnoPlugins\Media\Pages\Delete');

                \Idno\Core\Idno::site()->template()->extendTemplate('shell/head','media/shell/head');
                \Idno\Core\Idno::site()->template()->extendTemplate('shell/footer','media/shell/footer');
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

                if ($media = Media::get($search,[],9999,0)) {
                    foreach($media as $post) {
                        /* @var Media $post */
                        if ($attachments = $post->getAttachments()) {
                            foreach($attachments as $attachment) {
                                $total += $attachment['length'];
                            }
                        }
                    }
                }

                return $total;
            }
        }
    }