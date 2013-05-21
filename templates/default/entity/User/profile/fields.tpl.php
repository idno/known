<?php
    if (!empty($vars['user']->profile['url'])) {
?>
        <p>
            <a href="<?=urlencode($vars['user']->profile['url'])?>" rel="me" class="u-url"><?=($vars['user']->profile['url'])?></a>
        </p>
<?php
    }
?>