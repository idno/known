<?php
    if (!empty($vars['user']->profile['url']) && is_array($vars['user']->profile['url'])) {
        foreach($vars['user']->profile['url'] as $url) {
?>
        <p class="url-container">
            <i class="icon-globe"></i> <a href="<?=htmlspecialchars($url)?>" rel="me" class="u-url"><?=($url)?></a>
        </p>
<?php
        }
    }
?>