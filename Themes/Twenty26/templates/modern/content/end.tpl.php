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

        <div class="idno-entry-meta">
            <span class="idno-entry-byline">
                <a href="<?= $owner->getDisplayURL() ?>" class="idno-entry-author"><?= htmlentities(strip_tags($owner->getTitle()), ENT_QUOTES, 'UTF-8') ?></a>
                <?= \Idno\Core\Idno::site()->language()->_('published this') ?>
                <a class="u-url idno-entry-permalink" href="<?= $vars['object']->getDisplayURL() ?>" rel="permalink"><time class="dt-published"
                          datetime="<?= date(DATE_ATOM, $vars['object']->created) ?>"><?= date('d F Y', $vars['object']->created) ?></time></a>
                <?php if ($vars['object']->access != 'PUBLIC') { ?>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:0.875rem;height:0.875rem;display:inline;vertical-align:middle"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                <?php } ?>
            </span>
            <span class="idno-entry-actions">
                <?= $this->draw('content/edit') ?>
                <?= $this->draw('content/end/links') ?>
            </span>
            <?php if (\Idno\Core\Idno::site()->currentPage()->isPermalink() && \Idno\Core\Idno::site()->config()->indieweb_citation) { ?>
                <span class="idno-entry-citation"><?= $vars['object']->getCitation() ?></span>
            <?php } ?>
        </div>
        <div class="idno-entry-interactions">
        <span class="idno-entry-action">
            <?php
            if (!$has_liked) {
                $star = \Idno\Core\Idno::site()->language()->_('Star this!');
                $heart_only = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:1rem;height:1rem;display:inline;vertical-align:middle" title="' . $star . '"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>';
            } else {
                $heart_only = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:1rem;height:1rem;display:inline;vertical-align:middle"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>';
            }
            if ($likes == 1) {
                $star = \Idno\Core\Idno::site()->language()->_('star');
                $heart_text = '1 ' . $star;
            } else {
                $star = \Idno\Core\Idno::site()->language()->_('stars');
                $heart_text = $likes . ' ' . $star;
            }
            $heart = $heart_only . ' ' . $heart_text;
            if (\Idno\Core\Idno::site()->session()->isLoggedOn()) {
                echo \Idno\Core\Idno::site()->actions()->createLink(
                    \Idno\Core\Idno::site()->config()->getDisplayURL() . 'annotation/post',
                    $heart_only,
                    [
                        'type' => 'like',
                        'object' => $vars['object']->getUUID()
                    ],
                    [
                        'method' => 'POST',
                        'class' => 'idno-action-toggle'
                    ]
                );
                ?>
                    <a class="idno-action-count" href="<?= $vars['object']->getDisplayURL() ?>#comments"><?= $heart_text ?></a>
                <?php
            } else {
                ?>
                    <a class="idno-action-count" href="<?= $vars['object']->getDisplayURL() ?>#comments"><?= $heart ?></a>
                <?php
            }
            ?>
            </span>
           <span class="idno-entry-action">
                <a href="<?= $vars['object']->getDisplayURL() ?>#comments">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:1rem;height:1rem;display:inline;vertical-align:middle"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/></svg>
                    <?php
                    if ($replies == 1) {
                        echo '1 ' . \Idno\Core\Idno::site()->language()->_('comment');
                    } else {
                        echo $replies . ' ' . \Idno\Core\Idno::site()->language()->_('comments');
                    }
                    ?>
                </a>
            </span>
            <?php if ($shares = $vars['object']->countAnnotations('share')) { ?>
            <span class="idno-entry-action">
                <a href="<?= $vars['object']->getDisplayURL() ?>#comments">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:1rem;height:1rem;display:inline;vertical-align:middle"><path d="m17 2 4 4-4 4"/><path d="M3 11v-1a4 4 0 0 1 4-4h14"/><path d="m7 22-4-4 4-4"/><path d="M21 13v1a4 4 0 0 1-4 4H3"/></svg>
                    <?= $shares ?>
                </a>
            </span>
            <?php } ?>
            <?php if ($rsvps = $vars['object']->countAnnotations('rsvp')) { ?>
            <span class="idno-entry-action">
                <a href="<?= $vars['object']->getDisplayURL() ?>#comments">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:1rem;height:1rem;display:inline;vertical-align:middle"><path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/></svg>
                    <?= $rsvps ?>
                </a>
            </span>
            <?php } ?>
        </div>
        <?php

        if (\Idno\Core\Idno::site()->currentPage()->isPermalink()) {

            if (!empty($likes) || !empty($replies) || !empty($shares) || !empty($rsvps) || !empty($mentions)) {

                ?>
                <div class="idno-annotations">

                    <a name="comments"></a>
                    <?= $this->draw('content/end/annotations') ?>
                    <?php
                        unset($this->vars['annotations']);
                        unset($this->vars['annotation_permalink']);
                    ?>

                </div>
                <?php

            }

            echo $this->draw('entity/annotations/comment/main');

            echo $this->draw('content/syndication/links');

        } else {

            ?>
            <div class="idno-entry-extra">
                <?= $this->draw('content/syndication/links') ?>
            </div>
            <?php

            if (\Idno\Core\Idno::site()->session()->isLoggedOn()) {
                echo $this->draw('entity/annotations/comment/mini');
            }

        }
}
