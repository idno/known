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

            ?>
            <link href="<?php echo htmlspecialchars($url)?>" rel="me" class="<?php echo $h_card; ?>"/>
            <?php
        }
    }
}
