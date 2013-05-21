<?php
    if (!empty($vars['user']->profile['url'])) {
?>
        <p>
            <i class="icon-globe"></i> <a href="<?=htmlspecialchars($vars['user']->profile['url'])?>" rel="me" class="u-url"><?=($vars['user']->profile['url'])?></a>
        </p>
<?php
    }
?>