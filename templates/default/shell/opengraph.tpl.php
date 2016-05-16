<?php

    $currentPage = \Idno\Core\Idno::site()->currentPage();
    $pageOwner = $currentPage->getOwner();

    if (empty($vars['title'])) $vars['title'] = '';

    if (!empty($vars['object'])) {
        $objectIcon = $vars['object']->getIcon();
    } else {
        $objectIcon = false;
    }

    if (\Idno\Core\Idno::site()->session()->isLoggedIn()) {

        ?>
        <!-- <link rel="manifest" href="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>chrome/manifest.json"> -->
        <?php
        if (Idno\Core\site()->isSecure()) {
            ?>
            <!-- <script>
                window.addEventListener('load', function () {
                    if ('serviceWorker' in navigator) {
                        navigator.serviceWorker.register('<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>chrome/service-worker.js', {scope: '/'})
                            .then(function (r) {
                                console.log('Registered service worker');
                            })
                            .catch(function (whut) {
                                console.error('Could not register service worker');
                                console.error(whut);
                            });
                    }
                });
            </script> -->
            <?php
        }

    }

    $opengraph = array(
        'og:type'      => 'website',
        'og:title'     => htmlspecialchars(strip_tags($vars['title'])),
        'og:site_name' => htmlspecialchars(strip_tags(\Idno\Core\Idno::site()->config()->title)),
        'og:image'     => $currentPage->getIcon()
    );

    if ($currentPage->isPermalink()) {

        $opengraph['og:url'] = $currentPage->currentUrl();

        if (!empty($vars['object'])) {
            $owner  = $vars['object']->getOwner();
            $object = $vars['object'];

            $opengraph['og:title']       = htmlspecialchars(strip_tags($vars['object']->getTitle()));
            $opengraph['og:description'] = htmlspecialchars($vars['object']->getShortDescription());
            $opengraph['og:type']        = 'article'; //htmlspecialchars($vars['object']->getActivityStreamsObjectType());
            $opengraph['og:image']       = $objectIcon; //$owner->getIcon(); //Icon, for now set to being the author profile pic

            if ($icon = $vars['object']->getIcon()) {
                if ($icon_file = \Idno\Entities\File::getByURL($icon)) {
                    if (!empty($icon_file->metadata['width'])) {
                        $opengraph['og:image:width'] = $icon_file->metadata['width'];
                    }
                    if (!empty($icon_file->metadata['height'])) {
                        $opengraph['og:image:height'] = $icon_file->metadata['height'];
                    }
                }
            }

            if ($url = $vars['object']->getDisplayURL()) {
                $opengraph['og:url'] = $vars['object']->getDisplayURL();
            }
        }

    }

    foreach ($opengraph as $key => $value) {
        echo "<meta property=\"$key\" content=\"$value\" />\n";
    }

    if ($pageOwner && $currentPage->isPermalink()) {
        $has_twitter_account = false;
        if (!empty($pageOwner->profile['url'])) {
            foreach ($pageOwner->profile['url'] as $profile_url) {
                if (!empty($profile_url) && $profile_url[0] == '@') {
                    if (preg_match("/\@[a-z0-9_]+/i", $profile_url)) {
                        $has_twitter_account = true;
                        $twitter_account     = $profile_url;
                        break;
                    }
                }
                if (str_replace('www.', '', parse_url($profile_url, PHP_URL_HOST)) == 'twitter.com') {
                    if (preg_match("/https?:\/\/(www\.)?twitter\.com\/(#!\/)?@?([^\/]*)/", $profile_url, $matches)) {
                        if (!empty($matches[3])) {
                            $has_twitter_account = true;
                            $twitter_account     = $matches[3];
                            break;
                        }
                    }
                }
            }
        }

        if ($has_twitter_account) {

            $twitter_account = str_replace('@', '', $twitter_account);

            ?>

            <!-- Twitter card -->
            <meta name="twitter:card" content="summary"/>
            <meta name="twitter:site" content="@<?= $twitter_account ?>"/>
            <meta name="twitter:title" content="<?= htmlspecialchars($vars['title']) ?>"/>
            <meta name="twitter:description" content="<?= htmlspecialchars($vars['description']) ?>"/>

            <?php

            if (!empty($objectIcon)) {
                echo '<meta name="twitter:image" content="' . $objectIcon . '"/>' . "\n";
            }

        }
    }

?>