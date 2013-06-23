<?php /* @var \Idno\Common\Entity $vars['object'] */ ?>
<div class="permalink">
    <p>
        <a href="<?=$vars['object']->getURL()?>" rel="permalink" ><time class="dt-published" datetime="<?=date('c',$vars['object']->created)?>"><?=$vars['object']->getRelativePublishDate()?></time></a>
        <a href="<?=$vars['object']->getURL()?>#comments" ><?php if ($replies = $vars['object']->countAnnotations('reply')) { echo '<i class="icon-comments"></i> ' . $replies; } ?></a>
        <a href="<?=$vars['object']->getURL()?>#comments" ><?php if ($likes = $vars['object']->countAnnotations('like')) { echo '<i class="icon-thumbs-up"></i> ' . $likes; } ?></a>
        <a href="<?=$vars['object']->getURL()?>#comments" ><?php if ($shares = $vars['object']->countAnnotations('share')) { echo '<i class="icon-refresh"></i> ' . $shares; } ?></a>
        <?=$this->draw('content/end/links')?>
    </p>
</div>
<br clear="all" />
<?php

    if (\Idno\Core\site()->currentPage()->isPermalink()) {

        if (!empty($likes) || !empty($replies) || !empty($shares)) {

?>

            <div class="annotations">

                <a name="comments"></a>
                <?=$this->draw('content/end/comments')?>
                <?php

                    if ($replies = $vars['object']->getAnnotations('reply')) {
                        echo $this->__(['annotations' => $replies])->draw('entity/annotations/replies');
                    }
                    if ($likes = $vars['object']->getAnnotations('like')) {
                        echo $this->__(['annotations' => $likes])->draw('entity/annotations/likes');
                    }
                    if ($shares = $vars['object']->getAnnotations('share')) {
                        echo $this->__(['annotations' => $shares])->draw('entity/annotations/shares');
                    }

                ?>

            </div>

<?php

        }

    }

?>