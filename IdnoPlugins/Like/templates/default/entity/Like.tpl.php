<?php

    $class = '';
    $icon = '';

    if (!empty($vars['object']->likeof)) {
        $class = "u-like-of";
        $icon = '<i class="fa fa-star-o"></i> ';
    }
    elseif (!empty($vars['object']->repostof)) {
        $class = "u-repost-of";
        $icon = '<i class="fa fa-retweet"></i> ';
    }
    else {
        $class = "u-bookmark-of";
    }

    if (!empty($vars['object']->pageTitle)) {
        $body = $vars['object']->pageTitle;
    } else {
        $body = $vars['object']->body;
    }

?>
<div class="known-bookmark">
    <?php

        if (empty($vars['feed_view'])) {

            ?>
            <h2 class="idno-bookmark"><?=$icon?><a href="<?= $vars['object']->body; ?>" class="<?= $class ?> p-name"
                target="_blank"><?= $this->parseURLs(htmlentities(strip_tags($body))) ?></a>
            </h2>
        <?php

        }

        if (!empty($vars['object']->description)) {
            echo '<div class="e-content">';
                echo $this->__(['value' => $vars['object']->description, 'object' => $vars['object']])->draw('forms/output/richtext');
            echo '</div>';
        
        }
        
        
        
        if (!empty($vars['object']->tags)) {
        
            echo $this->__(['tags' => $vars['object']->tags])->draw('forms/output/tags');
        }

    ?>
</div>
<?= $this->draw('entity/content/embed'); ?>
