<?php

if (!empty($vars['annotations']) && is_array($vars['annotations'])) {
    uasort(
        $vars['annotations'], function ($a, $b) {
            return ($a['time'] < $b['time']) ? -1 : 1;
        }
    );
    foreach ($vars['annotations'] as $locallink => $annotation) {

        $permalink = $annotation['permalink'] ? $annotation['permalink'] : $locallink;
        ?>
            <div class="idno-annotation row u-comment h-cite">
                <div class="idno-annotation-image col-md-1 hidden-sm">
                    <p>
                        <a href="<?php echo strip_tags($annotation['owner_url']) ?>" rel="nofollow" class="icon-container"><img
                                    src="<?php echo \Idno\Core\Idno::site()->config()->sanitizeAttachmentURL(strip_tags($annotation['owner_image'])) ?>"/></a>
                    </p>
                </div>
                <div class="idno-annotation-content col-md-9">
                    <div class="p-summary e-content"><?php echo $this->autop($this->parseURLs(strip_tags($annotation['content']), 'rel="nofollow"')); ?></div>
                    <p>
                        <small>
                    <span class="p-author h-card">
                        <a class="u-photo" rel="nofollow" href="<?php echo strip_tags($annotation['owner_image']) ?>"></a>
                        <a class="p-name u-url"
                           href="<?php echo htmlspecialchars(strip_tags($annotation['owner_url'])) ?>" rel="nofollow"><?php echo htmlentities($annotation['owner_name'], ENT_QUOTES, 'UTF-8') ?></a></span>,
                            <a href="<?php echo $permalink ?>" rel="nofollow"><?php echo date('M d Y', $annotation['time']); ?></a>
                            on <a href="<?php echo $permalink ?>" rel="nofollow" class="u-url"><?php echo parse_url($permalink, PHP_URL_HOST) ?></a>
                        </small>
                    </p>
                </div>
            <?php
                $this->annotation_permalink = $locallink;

            if ($vars['object']->canEditAnnotation($annotation)) {
                echo $this->draw('content/annotation/edit');
            }
            ?>
            </div>
            <?php

    }
}
