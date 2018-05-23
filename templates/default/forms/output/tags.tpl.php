<?php
if (!empty($vars['tags'])) {
    if (is_array($vars['tags'])) {
        ?>
        <p class="tag-row">
            <i class="fa fa-tag"></i>
            <?php foreach ($vars['tags'] as $tag) { ?>
            <a href="<?= \Idno\Core\Idno::site()->config()->getDisplayURL(); ?>tag/<?= urlencode($tag); ?>" class="p-category" rel="tag"><?= $tag ?></a>
            <?php } ?>
        </p> 
        <?php
    } else {
        ?>
        <i class="fa fa-tag"></i><a href="<?= \Idno\Core\Idno::site()->config()->getDisplayURL(); ?>tag/<?= urlencode($vars['tags']); ?>" class="p-category" rel="tag"><?= $vars['tags'] ?></a>
        <?php
    }
}

unset($this->vars['tags']);