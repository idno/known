<?php

    $attachments = $vars['object']->getAttachments();
    $multiple = false;
    $num_pics = count($attachments);
    if ($num_pics > 1)
        $multiple = true;
    $cnt = 0;

    $lightBoxEnabled = !!\Idno\Core\Idno::site()->plugins()->get('Lightbox');

    $currentPage = \Idno\Core\Idno::site()->currentPage();
    $isPermalink = (!empty($currentPage) && $currentPage->isPermalink());

    $title = htmlentities(strip_tags($vars['object']->getTitle()), ENT_QUOTES, 'UTF-8');
    $isNotUntitled = ($title !== 'Untitled');

if ($isPermalink) {
    $rel = 'rel="in-reply-to"';
} else {
    $rel = '';
}

    $tags = "";
if (!empty($vars['object']->tags)) {
    $tags = $this->__(['tags' => $vars['object']->tags])->draw('forms/output/tags');

}
if (empty($vars['feed_view']) && $vars['object']->getTitle() && $isNotUntitled) {
    ?>
        <h2 class="photo-title p-name"><a
                href="<?php echo $vars['object']->getDisplayURL(); ?>"><?php echo $title; ?></a>
        </h2>
<?php } ?>

<div class="e-content entry-content <?php if ($multiple) echo "multiple-images"; ?>" data-num-pics="<?php echo $num_pics; ?>">

    <?php
    if (!empty($attachments)) {
        foreach ($attachments as $attachment) {

            if (!$isPermalink) {
                if ($cnt == 5 && $num_pics>$cnt) {
                    ?>
    <div class="photo-view photo-view-more">
        <a href="<?php echo $vars['object']->getDisplayURL(); ?>"><?php echo \Idno\Core\Idno::site()->language()->_('%d more...', [($num_pics-$cnt)]); ?></a>
    </div>
                
                    <?php
                    break;
                } else if ($cnt>5) {
                    break;
                }
            }
            //$mainsrc= \Idno\Core\Idno::site()->config()->getDisplayURL() . 'file/' . $attachment['_id'];
            $mainsrc = $attachment['url'];

            $filename = "";
            if (!empty($attachment['filename']))
                $filename = $attachment['filename'];
            if (!empty($vars['object']->thumbs_large) && !empty($vars['object']->thumbs_large[$filename])) {
                $src = $vars['object']->thumbs_large[$filename]['url'];
                // Old style
            } else if (!empty($vars['object']->thumbnail_large)) {
                $src = $vars['object']->thumbnail_large;
            } else if (!empty($vars['object']->thumbnail)) { // Backwards compatibility
                $src = $vars['object']->thumbnail;
            } else {
                $src = $mainsrc;
            }

            // Patch to correct certain broken URLs caused by https://github.com/idno/known/issues/526
            $src = preg_replace('/^(https?:\/\/\/)/', \Idno\Core\Idno::site()->config()->getDisplayURL(), $src);
            $mainsrc = preg_replace('/^(https?:\/\/\/)/', \Idno\Core\Idno::site()->config()->getDisplayURL(), $mainsrc);

            $src = \Idno\Core\Idno::site()->config()->sanitizeAttachmentURL($src);
            $mainsrc = \Idno\Core\Idno::site()->config()->sanitizeAttachmentURL($mainsrc);
            ?>
            <div class="photo-view">
                <a href="<?php echo ($isPermalink) ? $this->makeDisplayURL($mainsrc) : $vars['object']->getDisplayURL(); ?>" 
                   <?php if ($lightBoxEnabled) { ?>
                   data-toggle="lightbox"
                   data-remote="<?php echo $this->makeDisplayURL($mainsrc) ?>"
                   <?php } ?>
                   data-gallery="<?php echo $vars['object']->_id . $title; ?>"
                   data-original-img="<?php echo $this->makeDisplayURL($mainsrc) ?>"
                   data-title="<?php echo ($isNotUntitled) ? $title : ''; ?>" 
                   data-footer="<?php echo htmlentities(strip_tags($vars['object']->body), ENT_QUOTES, 'UTF-8'); ?>">
                   <img src="<?php echo $this->makeDisplayURL($src) ?>" class="u-photo" alt="<?php echo $title; ?>" />
                </a>
            </div>
            <?php
            $cnt ++;
        }
    } ?>
    <?php echo $this->autop($this->parseHashtags($this->parseURLs($vars['object']->body, $rel))) . $tags ?>

</div>
