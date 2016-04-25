<?php

    /* @var \Idno\Common\Entity $vars ['object'] */

    $replies = $vars['object']->countAnnotations('reply');
    $likes = $vars['object']->countAnnotations('like');
    $mentions = $vars['object']->countAnnotations('mention');
    $has_liked = false;
    if ($like_annotations = $vars['object']->getAnnotations('like')) {
        foreach ($like_annotations as $like) {
            if (\Idno\Core\Idno::site()->session()->isLoggedOn()) {
                if ($like['owner_url'] == \Idno\Core\Idno::site()->session()->currentUser()->getDisplayURL()) {
                    $has_liked = true;
                }
            }
        }
    }
    $owner = $vars['object']->getOwner();

    if (!empty($owner)) {

        ?>

        <div class="permalink">
            <p>
                <a href="<?= $owner->getDisplayURL() ?>"><?= htmlentities(strip_tags($owner->getTitle()), ENT_QUOTES, 'UTF-8') ?></a>published this
                <a class="u-url url" href="<?= $vars['object']->getDisplayURL() ?>" rel="permalink"><time class="dt-published"
                                                                                                          datetime="<?= date('c', $vars['object']->created) ?>"><?= date('l, F j, Y', $vars['object']->created) ?></time></a>
                <?php

                    if ($vars['object']->access != 'PUBLIC') {
                        ?><i class="fa fa-lock"> </i><?php
                    }

                ?>
                <?= $this->draw('content/edit') ?>
                <?= $this->draw('content/end/links') ?>
                <?php

                    if (\Idno\Core\Idno::site()->currentPage()->isPermalink() && \Idno\Core\Idno::site()->config()->indieweb_citation) {

                        ?>
                        <span class="citation"><?= $vars['object']->getCitation() ?></span>
                        <?php

                    }

                ?>
            </p>
        </div>
        <div class="interactions">
	        <span class="annotate-icon">
            <?php
                if (!$has_liked) {
                    $heart_only = '<i class="fa fa-star-o"></i>';
                } else {
                    $heart_only = '<i class="fa fa-star"></i>';
                }
                if ($likes == 1) {
                    $heart_text = '1 star';
                } else {
                    $heart_text = $likes . ' stars';
                }
                $heart = $heart_only . ' ' . $heart_text;
                if (\Idno\Core\Idno::site()->session()->isLoggedOn()) {
                echo \Idno\Core\Idno::site()->actions()->createLink(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'annotation/post', $heart_only, ['type' => 'like', 'object' => $vars['object']->getUUID()], ['method' => 'POST', 'class' => 'stars']);
            ?>
                <a class="stars" href="<?= $vars['object']->getDisplayURL() ?>#comments"><?= $heart_text ?></a></span>
            <?php
                } else {
            ?>
            <a class="stars" href="<?= $vars['object']->getDisplayURL() ?>#comments"><?= $heart ?></a></span>
        <?php
        }
            ?>
            <span class="annotate-icon"> <a class="comments" href="<?= $vars['object']->getDisplayURL() ?>#comments"><i class="fa fa-comments"></i> <?php

                        //echo $replies;
                        if ($replies == 1) {
                            echo '1 comment';
                        } else {
                            echo $replies . ' comments';
                        }

                    ?></a></span>
            <a class="shares" href="<?= $vars['object']->getDisplayURL() ?>#comments"><?php if ($shares = $vars['object']->countAnnotations('share')) {
                    echo '<i class="fa fa-retweet"></i>' . $shares;
                } ?></a>
            <a class="shares" href="<?= $vars['object']->getDisplayURL() ?>#comments"><?php if ($rsvps = $vars['object']->countAnnotations('rsvp')) {
                    echo '<i class="fa fa-calendar-o"></i>' . $rsvps;
                } ?></a>
        </div>
        <br />
        <?php

        if (\Idno\Core\Idno::site()->currentPage()->isPermalink()) {

            if (!empty($likes) || !empty($replies) || !empty($shares) || !empty($rsvps) || !empty($mentions)) {

                ?>

                <div class="annotations">

                    <a name="comments"></a>
                    <?= $this->draw('content/end/annotations') ?>
                    <?php

                        if ($replies = $vars['object']->getAnnotations('reply')) {
                            echo $this->__(array('annotations' => $replies))->draw('entity/annotations/replies');
                        }
                        if ($likes = $vars['object']->getAnnotations('like')) {
                            echo $this->__(array('annotations' => $likes))->draw('entity/annotations/likes');
                        }
                        if ($shares = $vars['object']->getAnnotations('share')) {
                            echo $this->__(array('annotations' => $shares))->draw('entity/annotations/shares');
                        }
                        if ($rsvps = $vars['object']->getAnnotations('rsvp')) {
                            echo $this->__(array('annotations' => $rsvps))->draw('entity/annotations/rsvps');
                        }
                        if ($mentions = $vars['object']->getAnnotations('mention')) {
                            echo $this->__(array('annotations' => $mentions))->draw('entity/annotations/mentions');
                        }

                        unset($this->vars['annotations']);
                        unset($this->vars['annotation_permalink']);
                    ?>

                </div>

                <?php

            }

            echo $this->draw('content/syndication/links');

        }
    }

?>
