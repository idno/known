<?php

    $item = $vars['object'];
    /* @var \Idno\Entities\Reader\FeedItem $item */


    if (!empty($item)) {

        echo \Idno\Core\Webmention::getActionTypeFromHTML($item->getBody());

        ?>

        <div class="permalink">
            <p>
                <a href="<?= $item->getAuthorURL() ?>"><?= $item->getAuthorName() ?></a>published this
                <a class="u-url url" href="<?= $item->getURL() ?>" rel="permalink">
                    <time class="dt-published"
                          datetime="<?= date('c', $item->created) ?>"><?= date('c', $item->created) ?></time>
                </a>
            </p>
        </div>
        <div class="interactions">
            <?php
                if (!$has_liked) {
                    $heart_only = '<i class="icon-star-empty"></i>';
                } else {
                    $heart_only = '<i class="icon-star"></i>';
                }
                if ($likes == 1) {
                    $heart_text = '1 star';
                } else {
                    $heart_text = $likes . ' stars';
                }
                $heart = $heart_only . ' ' . $heart_text;
                if (\Idno\Core\site()->session()->isLoggedOn()) {
                    echo \Idno\Core\site()->actions()->createLink(\Idno\Core\site()->config()->getURL() . 'annotation/post', $heart_only, array('type' => 'like', 'object' => $vars['object']->getUUID()), array('method' => 'POST', 'class' => 'stars'));
                    ?>
                    <a class="stars" href="<?= $vars['object']->getURL() ?>#comments"><?= $heart_text ?></a>
                <?php
                } else {
                    ?>
                    <a class="stars" href="<?= $vars['object']->getURL() ?>#comments"><?= $heart ?></a>
                <?php
                }
            ?>
            <a class="comments" href="<?= $vars['object']->getURL() ?>#comments"><i class="icon-comments"></i> <?php

                    //echo $replies;
                    if ($replies == 1) {
                        echo '1 comment';
                    } else {
                        echo $replies . ' comments';
                    }

                ?></a>
            <a class="shares" href="<?= $vars['object']->getURL() ?>#comments"><?php if ($shares = $vars['object']->countAnnotations('share')) {
                    echo '<i class="icon-refresh"></i> ' . $shares;
                } ?></a>
            <a class="shares" href="<?= $vars['object']->getURL() ?>#comments"><?php if ($rsvps = $vars['object']->countAnnotations('rsvp')) {
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
    }

?>
