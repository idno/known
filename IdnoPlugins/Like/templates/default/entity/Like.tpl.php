<?php
    if (\Idno\Core\site()->currentPage()->isPermalink()) {
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
    <h3 class="p-bookmark"><i class="icon-star hint"></i> <a href="<?= $vars['object']->body;?>" rel="bookmark" target="_blank"><?=$this->parseURLs(($body),$rel)?></a></h3>
    <?php

        if (!empty($vars['object']->description)) {
        ?>
            <p><small><?=$this->parseURLs($this->parseHashtags($vars['object']->description),$rel)?></small></p>
        <?php
        }

    ?>
</div>
<?= $this->draw('entity/content/embed'); ?>
