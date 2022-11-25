<?php

namespace IdnoPlugins\Photo {

    class Main extends \Idno\Common\Plugin
    {

        function registerTranslations()
        {

            \Idno\Core\Idno::site()->language()->register(
                new \Idno\Core\GetTextTranslation(
                    'photo', dirname(__FILE__) . '/languages/'
                )
            );
        }

        function registerPages()
        {
            \Idno\Core\Idno::site()->routes()->addRoute('/photo/edit/?', '\IdnoPlugins\Photo\Pages\Edit');
            \Idno\Core\Idno::site()->routes()->addRoute('/photo/edit/:id/?', '\IdnoPlugins\Photo\Pages\Edit');
            \Idno\Core\Idno::site()->routes()->addRoute('/photo/delete/:id/?', '\IdnoPlugins\Photo\Pages\Delete');
        }

        function registerEventHooks()
        {
            \Idno\Core\Idno::site()->events()->addListener('page/get', function (\Idno\Core\Event $event) {
                \Idno\Core\Idno::site()->currentPage()->setAsset("exif-js", \Idno\Core\Idno::site()->config()->getStaticURL() . 'vendor/npm-asset/exif-js/exif.js', 'javascript');
            });
        }

        /**
         * Get the total file usage
         * @param bool $user
         * @return int
         */
        function getFileUsage($user = false)
        {

            $total = 0;

            if (!empty($user)) {
                $search = ['user' => $user];
            } else {
                $search = [];
            }

            if ($photos = Photo::get($search, [], 9999, 0)) {
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

