<?php

    /* @var \Idno\Common\Entity $vars ['object'] */

    $replies = $vars['object']->countAnnotations('reply');
    $likes = $vars['object']->countAnnotations('like');
    $has_liked = false;
    if ($like_annotations = $vars['object']->getAnnotations('like')) {
        foreach ($like_annotations as $like) {
            if (\Idno\Core\site()->session()->isLoggedOn()) {
                if ($like['owner_url'] == \Idno\Core\site()->session()->currentUser()->getURL()) {
                    $has_liked = true;
                }
            }
        }
    }
    $owner = $vars['object']->getOwner();

?>
<div class="permalink">
    <p>
    	<a href="<?=$owner->getURL()?>"><?=$owner->getTitle()?></a>published this 
        <a class="u-url url" href="<?= $vars['object']->getURL() ?>" rel="permalink">
            <time class="dt-published"
                  datetime="<?= date('c', $vars['object']->created) ?>"><?= date('c', $vars['object']->created) ?></time></a>
        <?= $this->draw('content/edit') ?>
        <?= $this->draw('content/end/links') ?>
        <?php

            if (\Idno\Core\site()->currentPage()->isPermalink() && \Idno\Core\site()->config()->indieweb_citation) {

                ?>
                <span class="citation"><?= $vars['object']->getCitation() ?></span>
            <?php

            }

        ?>
    </p>
</div>
<div class="interactions">
    <?php
        if (!$has_liked) {
            $heart = '<i class="icon-star-empty"></i>';
        } else {
            $heart = '<i class="icon-star"></i>';
        }
        if ($likes == 1) {
            $heart .= ' 1 star';
        } else {
            $heart .= ' ' . $likes . ' stars';
        }
        if (\Idno\Core\site()->session()->isLoggedOn()) {
            echo \Idno\Core\site()->actions()->createLink(\Idno\Core\site()->config()->getURL() . 'annotation/post', $heart, ['type' => 'like', 'object' => $vars['object']->getUUID()], ['method' => 'POST']);
        } else {
            ?>
            <a href="<?= $vars['object']->getURL() ?>#comments"><?= $heart ?></a>
        <?php
        }
    ?>
    <a href="<?= $vars['object']->getURL() ?>#comments"><i class="icon-comments"></i> <?php

            //echo $replies;
            if ($replies == 1) {
                echo '1 comment';
            } else {
                echo $replies . ' comments';
            }

        ?></a>
    <a href="<?= $vars['object']->getURL() ?>#comments"><?php if ($shares = $vars['object']->countAnnotations('share')) {
            echo '<i class="icon-refresh"></i> ' . $shares;
        } ?></a>
    <a href="<?= $vars['object']->getURL() ?>#comments"><?php if ($rsvps = $vars['object']->countAnnotations('rsvp')) {
            echo '<i class="icon-calendar-empty"></i> ' . $rsvps;
        } ?></a>
</div>
<br clear="all"/>
<?php

    if (\Idno\Core\site()->currentPage()->isPermalink()) {

        if (!empty($likes) || !empty($replies) || !empty($shares) || !empty($rsvps)) {

            ?>

            <div class="annotations">

                <a name="comments"></a>
                <?= $this->draw('content/end/annotations') ?>
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

        echo $this->draw('entity/annotations/comment/main');

        if ($posse = $vars['object']->getPosseLinks()) {

            ?>
            <div class="posse">
                <a name="posse"></a>

                <p>
                    Also on:
                    <?php

                        foreach ($posse as $service => $url) {
                            echo '<a href="' . $url . '" rel="syndication" class="u-syndication ' . $service . '">' . $service . '</a> ';
                        }

                    ?>
                </p>
            </div>
        <?php

        }

    } else {

        if (\Idno\Core\site()->session()->isLoggedOn()) {
            echo $this->draw('entity/annotations/comment/mini');
        }

    }

?>
