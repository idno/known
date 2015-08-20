<?php

    if (!empty($vars['annotations']) && is_array($vars['annotations'])) {
        foreach($vars['annotations'] as $locallink => $annotation) {
	    
	    $permalink = $annotation['permalink'] ? $annotation['permalink'] : $locallink;
?>
        <div class="idno-annotation row u-comment h-cite">
            <div class="idno-annotation-image col-md-1 hidden-sm">
                <p>
                    <a href="<?=$annotation['owner_url']?>" class="icon-container"><img src="<?=\Idno\Core\site()->config()->sanitizeAttachmentURL($annotation['owner_image'])?>" /></a>
                </p>
            </div>
            <div class="idno-annotation-content col-md-9">
                <div class="p-summary e-content"><?=$this->autop($this->parseURLs(strip_tags($annotation['content'])));?></div>
                <div class="h-card">
                    <p><small><a href="<?=htmlspecialchars($annotation['owner_url'])?>" class="p-author p-name"><?=htmlentities($annotation['owner_name'], ENT_QUOTES, 'UTF-8')?></a>,
                            <a href="<?=$permalink?>"><?=date('M d Y', $annotation['time']);?></a>
                            on <a href="<?=$permalink?>" class="u-url"><?=parse_url($permalink, PHP_URL_HOST)?></a></small> <img src="<?=$annotation['owner_image']?>" class="u-photo" style="display:none"/></p></small></p>
                </div>
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
