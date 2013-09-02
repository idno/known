<?php /* @var \Idno\Common\Entity $vars['object'] */ ?>
    <div class="permalink">
        <p>
            <a href="<?=$vars['object']->getURL()?>" rel="permalink" ><time class="dt-published" datetime="<?=date('c',$vars['object']->created)?>"><?=date('c',$vars['object']->created)?></time></a>
            <a href="<?=$vars['object']->getURL()?>#comments" ><?php if ($replies = $vars['object']->countAnnotations('reply')) { echo '<i class="icon-comments"></i> ' . $replies; } ?></a>
            <a href="<?=$vars['object']->getURL()?>#comments" ><?php if ($likes = $vars['object']->countAnnotations('like')) { echo '<i class="icon-thumbs-up"></i> ' . $likes; } ?></a>
            <a href="<?=$vars['object']->getURL()?>#comments" ><?php if ($shares = $vars['object']->countAnnotations('share')) { echo '<i class="icon-refresh"></i> ' . $shares; } ?></a>
            <a href="<?=$vars['object']->getURL()?>#comments" ><?php if ($rsvps = $vars['object']->countAnnotations('rsvp')) { echo '<i class="icon-calendar-empty"></i> ' . $rsvps; } ?></a>
            <?=$this->draw('content/end/links')?>
        </p>
    </div>
    <br clear="all" />
<?php

    if (\Idno\Core\site()->currentPage()->isPermalink()) {

        if (!empty($likes) || !empty($replies) || !empty($shares) || !empty($rsvps)) {

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
                    if ($rsvps = $vars['object']->getAnnotations('rsvp')) {
                        echo $this->__(['annotations' => $rsvps])->draw('entity/annotations/rsvps');
                    }

                ?>

            </div>

        <?php

        }

        if ($posse = $vars['object']->getPosseLinks()) {

            ?>
            <div class="posse">
                <a name="posse"></a>
                <p>
                    Also on:
                    <?php

                        foreach($posse as $service => $url) {
                            echo '<a href="'.$url.'" rel="syndication" class="u-syndication '.$service.'">' . $service . '</a> ';
                        }

                    ?>
                </p>
            </div>
        <?php

        } else echo 'noposse';

    }

?>
