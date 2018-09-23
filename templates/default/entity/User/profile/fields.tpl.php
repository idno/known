<?php
if (!empty($vars['user']->profile['url']) && is_array($vars['user']->profile['url'])) {
    foreach($vars['user']->profile['url'] as $url) {
        if (!empty($url)) {

            $h_card = 'u-url';
            $url_display = $url;

            // Quick shim for Twitter usernames
            if ($url[0] == '@') {
                if (preg_match("/\@[a-z0-9_]+/i", $url)) {
                    $url = str_replace('@', '', $url);
                    $url = 'https://twitter.com/' . $url;
                }
            }

            $url = $this->fixURL($url);

            // Pick appropriate icon
            $host = parse_url($url, PHP_URL_HOST);
            $host = str_replace('www.', '', $host);
            switch($host) {

                case 'twitter.com':         $icon = 'fab fa-twitter';
                    break;
                case 'github.com':          $icon = 'fab fa-github-square';
                    break;
                case 'fb.com':
                case 'facebook.com':        $icon = 'fab fa-facebook';
                    break;
                case 'plus.google.com':     $icon = 'fab fa-google-plus';
                    break;
                case 'linkedin.com':        $icon = 'fab fa-linkedin';
                    break;
                case 'reddit.com':          $icon = 'fab fa-reddit';
                    break;
                case 'instagram.com':       $icon = 'fab fa-instagram';
                    break;
                case 'pinterest.com':       $icon = 'fab fa-pinterest';
                    break;
                case 'soundcloud.com':      $icon = 'fab fa-soundcloud';
                    break;
                case 'paypal.me':
                case 'paypal.com':          $icon = 'fab fa-paypal';
                    break;
                case 'flickr.com':          $icon = 'fab fa-flickr';
                    break;
                case 'youtube.com':         $icon = 'fab fa-youtube';
                    break;
                case 'angel.co':            $icon = 'fab fa-angellist';
                    break;
                case 'patreon.com':         $icon = 'fab fa-patreon';
                    break;
                default:                    $icon = 'fa fa-link';
                    break;

            }

            $scheme = parse_url($url, PHP_URL_SCHEME);
            switch ($scheme) {
                case 'mailto' :
                    $icon = 'far fa-envelope'; $url_display = str_replace('mailto:', '', $url_display); $h_card = 'u-email';
                    break;
                case 'sms' :
                    $icon = 'fas fa-mobile-alt'; $url_display = str_replace('sms:', '', $url_display); $h_card = 'p-tel';
                    break;
                case 'sip' :
                case 'tel' :
                    $icon = 'fas fa-phone'; $url_display = str_replace('tel:', '', $url_display); $h_card = 'p-tel';
                    break;
                case 'skype' :
                    $icon = 'fab fa-skype'; $url_display = str_replace('skype:', '', $url_display); $h_card = 'p-skype';
                    break;
                case 'bitcoin':
                    $icon = 'fab fa-bitcoin'; $url_display = str_replace('bitcoin:', '', $url_display); $h_card = 'p-bitcoin';
                    break;
                case 'facetime' :
                    $icon = 'fas fa-video'; $url_display = str_replace('facetime:', '', $url_display); $h_card = 'p-facetime';
                    break;
            }

            ?>
        <p class="url-container">
            <i class="<?php echo $icon?>"></i> <a href="<?php echo htmlspecialchars($url)?>" rel="me" class="<?php echo $h_card; ?>"><?php echo str_replace('http://', '', str_replace('https://', '', strip_tags($url_display)))?></a>
        </p>
            <?php
        }
    }
}
