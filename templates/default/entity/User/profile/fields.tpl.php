<?php

$host_to_icon = [
    "500px.com" => "500px",
    "amazon.com" => "amazon",
    "amazon.co.uk" => "amazon",
    "angel.co" => "angellist",
    "apple.com" => "apple",
    "archive.org" => "archive-org",
    "bandcamp.com" => "bandcamp",
    "behance.net" => "behance",
    "bitbucket.org" => "bitbucket",
    "blogspot.com" => "blogger",
    "chrome.google.com" => "chrome",
    "codepen.io" => "codepen",
    "del.icio.us" => "delicious",
    "deviantart.com" => "deviantart",
    "digg.com" => "digg",
    "discord.com" => "discord",
    "dribbble.com" => "dribbble",
    "dropbox.com" => "dropbox",
    "drupal.org" => "drupal",
    "eercast.com" => "eercast",
    "etsy.com" => "etsy",
    "fb.com" => "facebook",
    "facebook.com" => "facebook",
    "flickr.com" => "flickr",
    "foursquare.com" => "foursquare",
    "freecodecamp.com" => "freecodecamp",
    "getgrav.com" => "grav",
    "github.com" => "github-square",
    "gitlab.com" => "gitlab",
    "google.com" => "google",
    "imdb.com" => "imdb",
    "instagram.com" => "instagram",
    "joomla.org" => "joomla",
    "jsfiddle.net" => "jsfiddle",
    "keybase.io" => "keybase",
    "last.fm" => "lastfm",
    "linkedin.com" => "linkedin",
    "matrix.org" => "matrix-org",
    "matrix.to" => "matrix-org",
    "medium.com" => "medium",
    "meetup.com" => "meetup",
    "mixcloud.com" => "mixcloud",
    "ok.ru" => "odnoklassniki",
    "news.ycombinator.com" => "hacker-news",
    "patreon.com" => "patreon",
    "paypal.com" => "paypal",
    "paypal.me" => "paypal",
    "pinterest.com" => "pinterest",
    "play.google.com" => "android",
    "plus.google.com" => "google-plus",
    "pluspora.com" => "diaspora",
    "producthunt.com" => "product-hunt",
    "qq.com" => "qq",
    "quora.com" => "quora",
    "ravelry.com" => "ravelry",
    "reddit.com" => "reddit",
    "renren.com" => "renren",
    "slideshare.net" => "slideshare",
    "snapchat.com" => "snapchat",
    "soundcloud.com" => "soundcloud",
    "spotify.com" => "spotify",
    "stackexchange.com" => "stack-exchange",
    "stackoverflow.com" => "stack-overflow",
    "steamcommunity.com" => "steam",
    "steampowered.com" => "steam",
    "stumbleupon.com" => "stumbleupon",
    "telegram.org" => "telegram",
    "tripadvisor.com" => "tripadvisor",
    "tripadvisor.co.uk" => "tripadvisor",
    "tumblr.com" => "tumblr",
    "twitch.tv" => "twitch",
    "twitter.com" => "twitter",
    "viadeo.com" => "viadeo",
    "vimeo.com" => "vimeo-square",
    "wikipedia.org" => "wikipedia-w",
    "wordpress.com" => "wordpress",
    "wordpress.org" => "wordpress",
    "xing.com" => "xing",
    "yahoo.com" => "yahoo",
    "yelp.com" => "yelp",
    "yelp.co.uk" => "yelp",
    "youtube.com" => "youtube-play",
    "zotero.com" => "zotero"
];

if (!empty($vars['user']->profile['url']) && is_array($vars['user']->profile['url'])) {
    foreach ($vars['user']->profile['url'] as $url) {
        if (!empty($url)) {
            $h_card = 'u-url';
            $url_display = $url;
            $icon = 'fa fa-link'; // default icon

            // Quick shim for Twitter usernames
            if ($url[0] == '@') {
                if (preg_match("/@[a-z0-9_]+/i", $url)) {
                    $url = str_replace('@', '', $url);
                    $url = 'https://twitter.com/' . $url;
                }
            }

            $url = $this->fixURL($url);

            // Pick appropriate icon
            $host = strtolower(parse_url($url, PHP_URL_HOST));
            $host = str_replace('www.', '', $host);

            // Check if there is an icon for this hostname
            foreach($host_to_icon as $host_value => $host_icon) {
                if (strpos($host, $host_value) > -1) $icon = 'fa fa-' . $host_icon;
            }

            // Map Schemes to Icons.  Keep in sync with fixURL code in Idno/Core/Template.php
            $scheme = parse_url($url, PHP_URL_SCHEME);
            switch ($scheme) {
                case 'mailto' :
                    $icon = 'fa fa-envelope';
                    $url_display = str_replace('mailto:', '', $url_display);
                    $h_card = 'u-email';
                    break;
                case 'sms' :
                    $icon = 'fa fa-mobile';
                    $url_display = str_replace('sms:', '', $url_display);
                    $h_card = 'p-tel';
                    break;
                case 'sip' :
                case 'tel' :
                    $icon = 'fa fa-phone';
                    $url_display = str_replace('tel:', '', $url_display);
                    $h_card = 'p-tel';
                    break;
                case 'spotify' :
                    $icon = 'fa fa-spotify';
                    $url_display = str_replace('spotify:', '', $url_display);
                    $h_card = 'p-skype';
                    break;
                case 'skype' :
                    $icon = 'fa fa-skype';
                    $url_display = str_replace('skype:', '', $url_display);
                    $h_card = 'p-skype';
                    break;
                case 'bitcoin':
                    $icon = 'fa fa-bitcoin';
                    $url_display = str_replace('bitcoin:', '', $url_display);
                    $h_card = 'p-bitcoin';
                    break;
                case 'ethereum':
                    $icon = 'fa fa-ethereum';
                    $url_display = str_replace('ethereum:', '', $url_display);
                    $h_card = 'p-ethereum';
                    break;
                case 'facetime' :
                    $icon = 'fa fa-video';
                    $url_display = str_replace('facetime:', '', $url_display);
                    $h_card = 'p-facetime';
                    break;
                case 'xmpp' :
                    $icon = 'fa fa-xmpp';
                    $url_display = str_replace('facetime:', '', $url_display);
                    $h_card = 'p-facetime';
                    break;
                case 'ssb' :
                    $icon = 'fa fa-ssb';
                    $url_display = str_replace('facetime:', '', $url_display);
                    $h_card = 'p-facetime';
                    break;
            }

            // Remove http / https schemas and any trailing slash
            $url_display = rtrim(str_replace('https://', '', str_replace('http://', '', strip_tags($url_display))),'/');

            switch ($host) {
                case 'angellist.com':
                case 'instagram.com':
                case 'facebook.com':
                case 'flickr.com':
                case 'github.com':
                case 'linkedin.com':
                case 'strava.com':
                case 'twitter.com':
                case 'venmo.com':
                    $url_display = substr(strrchr($url_display, '/'),1);
                    break;
            }

            ?>
            <p class="url-container">
                <i class="<?php echo $icon ?>"></i> <a href="<?php echo htmlspecialchars($url) ?>" rel="me"
                                                       class="<?php echo $h_card; ?>"><?php echo $url_display; ?></a>
            </p>
            <?php
        }
    }
}
