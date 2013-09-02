<?php
    if (!empty($vars['user']->profile['url']) && is_array($vars['user']->profile['url'])) {
        foreach($vars['user']->profile['url'] as $url) {
            if (!empty($url)) {
                
                $url_display = $url;
                
                // Pick appropriate icon
                $host = parse_url($url, PHP_URL_HOST);
                $host = str_replace('www.','',$host);
                switch($host) {

                    case 'twitter.com':         $icon = 'icon-twitter'; break;
                    case 'github.com':          $icon = 'icon-github'; break;
                    case 'facebook.com':        $icon = 'icon-facebook'; break;
                    case 'plus.google.com':     $icon = 'icon-google-plus'; break;
                    case 'linkedin.com':        $icon = 'icon-linkedin'; break;
                    default:                    $icon = 'icon-globe'; break;

                }

                $scheme = parse_url($url, PHP_URL_SCHEME);
                switch ($scheme) {
                    case 'mailto' : $icon = 'icon-envelope'; $url_display = str_replace('mailto:', '', $url_display); break;
                    case 'sms' : $icon = 'icon-mobile-phone'; $url_display = str_replace('sms:', '', $url_display); break;
                    case 'tel' : $icon = 'icon-phone'; $url_display = str_replace('tel:', '', $url_display); break;
                }
                
?>
        <p class="url-container">
            <i class="<?=$icon?>"></i> <a href="<?=htmlspecialchars($url)?>" rel="me" class="u-url"><?=($url_display)?></a>
        </p>
<?php
            }
        }
    }
?>