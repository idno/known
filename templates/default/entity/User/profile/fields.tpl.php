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

                    case 'twitter.com':         $icon = 'icon-twttr'; break;
                    case 'github.com':          $icon = 'icon-github'; break;
                    case 'fb.com':
                    case 'facebook.com':        $icon = 'icon-kbf'; break;
                    case 'plus.google.com':     $icon = 'icon-gpl'; break;
                    case 'linkedin.com':        $icon = 'icon-li'; break;
                    default:                    $icon = 'icon-globe'; break;

                }

                $scheme = parse_url($url, PHP_URL_SCHEME);
                switch ($scheme) {
                    case 'mailto' : $icon = 'icon-mail'; $url_display = str_replace('mailto:', '', $url_display); $h_card = 'u-email'; break;
                    case 'sms' : $icon = 'icon-mobile'; $url_display = str_replace('sms:', '', $url_display); $h_card = 'p-tel'; break;
                    case 'tel' : $icon = 'icon-phone'; $url_display = str_replace('tel:', '', $url_display); $h_card = 'p-tel'; break;
                    case 'facetime' : $icon = 'icon-videocam'; $url_display = str_replace('facetime:', '', $url_display); $h_card = 'p-facetime'; break;
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