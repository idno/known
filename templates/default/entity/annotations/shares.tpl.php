<?php

if (!empty($vars['annotations']) && is_array($vars['annotations'])) {
    uasort($vars['annotations'], function($a, $b) {
        return ($a['time'] < $b['time']) ? -1 : 1;
    });
    foreach($vars['annotations'] as $locallink => $annotation) {

        $permalink = $annotation['permalink'] ? $annotation['permalink'] : $locallink;

        ?>
            <div class="idno-annotation row">
                <div class="idno-annotation-image col-md-1 hidden-sm">
                    <p>
                        <a href="<?php echo $annotation['owner_url']?>" class="icon-container"><img src="<?php echo \Idno\Core\Idno::site()->config()->sanitizeAttachmentURL($annotation['owner_image'])?>" /></a>
                    </p>
                </div>
                <div class="idno-annotation-content col-md-6">
                    <p>
                        <a href="<?php echo htmlspecialchars($annotation['owner_url'])?>"><?php echo htmlentities($annotation['owner_name'], ENT_QUOTES, 'UTF-8')?></a>
                        <a href="<?php echo $permalink?>"><?php echo \Idno\Core\Idno::site()->language()->_('reshared this post'); ?></a>
                    </p>
                    <p><small><a href="<?php echo $permalink?>"><?php echo date('M d Y', $annotation['time']);?></a> on <a href="<?php echo $permalink?>"><?php echo parse_url($permalink, PHP_URL_HOST)?></a></small></p>
                </div>
            </div>
        <?php

    }
}