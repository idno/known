<?php
    if (!empty($vars['user']->profile['url']) && is_array($vars['user']->profile['url'])) {
        foreach($vars['user']->profile['url'] as $url) {
            if (!empty($url)) {
                
                $h_card = 'u-url';
                $url_display = $url;

                // Quick shim for Twitter usernames
                if ($url[0] == '@') {
                    if (preg_match("/\@[a-z0-9_]+/i", $url)) {
                        $url = str_replace('@','',$url);
                        $url = 'https://twitter.com/' . $url;
                    }
                }

                $url = $this->fixURL($url);
                
                // Pick appropriate icon
                $host = parse_url($url, PHP_URL_HOST);
                $host = str_replace('www.','',$host);
                switch($host) {

                    case 'twitter.com':         $icon = 'fa fa-twitter'; break;
                    case 'github.com':          $icon = 'fa fa-github-square'; break;
                    case 'fb.com':
                    case 'facebook.com':        $icon = 'fa fa-facebook'; break;
                    case 'plus.google.com':     $icon = 'fa fa-google-plus'; break;
                    case 'linkedin.com':        $icon = 'fa fa-linkedin'; break;
                    case 'reddit.com':          $icon = 'fa fa-reddit'; break;
                    case 'instagram.com':       $icon = 'fa fa-instagram'; break;
                    case 'pinterest.com':       $icon = 'fa fa-pinterest'; break;
                    case 'soundcloud.com':       $icon = 'fa fa-soundcloud'; break;
                    case 'paypal.me':
                    case 'paypal.com':          $icon = 'fa fa-paypal'; break;
                    case 'flickr.com':          $icon = 'fa fa-flickr'; break;
                    case 'youtube.com':         $icon = 'fa fa-youtube'; break;
                    case 'angel.co':            $icon = 'fa fa-angellist'; break;
                    default:                    $icon = 'fa fa-link'; break;

                }

                $scheme = parse_url($url, PHP_URL_SCHEME);
                switch ($scheme) {
                    case 'mailto' :
                        $icon = 'fa fa-envelope-o'; $url_display = str_replace('mailto:', '', $url_display); $h_card = 'u-email';
                        break;
                    case 'sms' :
                        $icon = 'fa fa-mobile'; $url_display = str_replace('sms:', '', $url_display); $h_card = 'p-tel';
                        break;
                    case 'sip' :
                    case 'tel' :
                        $icon = 'fa fa-phone'; $url_display = str_replace('tel:', '', $url_display); $h_card = 'p-tel';
                        break;
                    case 'skype' :
                        $icon = 'fa fa-skype'; $url_display = str_replace('skype:', '', $url_display); $h_card = 'p-skype';
                        break;
                    case 'bitcoin':
                        $icon = 'fa fa-btc'; $url_display = str_replace('bitcoin:', '', $url_display); $h_card = 'p-bitcoin';
                        break;
                    case 'facetime' :
                        $icon = 'fa fa-video-camera'; $url_display = str_replace('facetime:', '', $url_display); $h_card = 'p-facetime';
                        break;
                }
                
?>
        <p class="url-container">
            <i class="<?=$icon?>"></i> <a href="<?=htmlspecialchars($url)?>" rel="me" class="<?=$h_card; ?>"><?=str_replace('http://','',str_replace('https://','', strip_tags($url_display)))?></a>
        </p>
<?php
            }
        }
    }
?>
