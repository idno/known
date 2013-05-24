<?php
    if (!empty($vars['user']->profile['url']) && is_array($vars['user']->profile['url'])) {
        foreach($vars['user']->profile['url'] as $url) {

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

?>
        <p class="url-container">
            <i class="<?=$icon?>"></i> <a href="<?=htmlspecialchars($url)?>" rel="me" class="u-url"><?=($url)?></a>
        </p>
<?php
        }
    }
?>