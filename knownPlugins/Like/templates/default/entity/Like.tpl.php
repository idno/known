<?php
    if (\known\Core\site()->currentPage()->isPermalink()) {
        $rel = 'rel="like" class="u-like"';
    } else {
        $rel = '';
    }

    if (!empty($vars['object']->pageTitle)) {
        $body = $vars['object']->pageTitle;
    } else {
        $body = $vars['object']->body;
    }

?>
<div class="">
    <p class="p-name"><i class="icon-star hint"></i> <a href="<?= $vars['object']->body;?>" rel="bookmark" target="_blank"><?=$this->parseURLs(($body),$rel)?></a></p>
    <?php

        if (!empty($vars['object']->description)) {
        ?>
            <p><small><?=$this->parseURLs($this->parseHashtags($vars['object']->description),$rel)?></small></p>
        <?php
        }

    ?>
</div>
<?= $this->draw('entity/content/embed'); ?>
