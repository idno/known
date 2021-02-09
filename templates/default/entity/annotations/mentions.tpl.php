<?php

if (!empty($vars['annotations']) && is_array($vars['annotations'])) {
    uasort(
        $vars['annotations'], function ($a, $b) {
            return ($a['time'] < $b['time']) ? -1 : 1;
        }
    );
    foreach($vars['annotations'] as $locallink => $annotation) {

        $permalink = $annotation['permalink'] ? $annotation['permalink'] : $locallink;
        ?>
            <div class="idno-annotation row">
                <div class="idno-annotation-image col-md-1 hidden-sm">
                    <p>
                        <a href="<?php echo htmlspecialchars($annotation['owner_url'])?>" rel="nofollow" class="icon-container"><img src="<?php echo \Idno\Core\Idno::site()->config()->sanitizeAttachmentURL($annotation['owner_image'])?>" /></a>
                    </p>
                </div>
                <div class="idno-annotation-content col-md-6">
                    <p><?php echo \Idno\Core\Idno::site()->language()->_('Mentioned in'); ?>: <a href="<?php echo $permalink?>" rel="nofollow"><?php

                    if (!empty($annotation['title'])) {
                        echo htmlspecialchars($annotation['title']);
                    } else {
                        echo htmlspecialchars($permalink);
                    }

                    ?></a></p>
                    <p><small><a href="<?php echo htmlspecialchars($annotation['owner_url'])?>" rel="nofollow"><?php echo htmlentities($annotation['owner_name'], ENT_QUOTES, 'UTF-8')?></a>,
                            <a href="<?php echo htmlspecialchars($permalink) ?>" rel="nofollow"><?php echo date('M d Y', $annotation['time']);?></a>
                            on <a href="<?php echo htmlspecialchars($permalink) ?>" rel="nofollow"><?php echo parse_url($permalink, PHP_URL_HOST)?></a></small></p>
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
