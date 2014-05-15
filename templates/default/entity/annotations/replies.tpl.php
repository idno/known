<?php

    if (!empty($vars['annotations']) && is_array($vars['annotations'])) {
        foreach($vars['annotations'] as $locallink => $annotation) {
	    
	    $permalink = $annotation['permalink'] ? $annotation['permalink'] : $locallink;
?>
        <div class="known-annotation row">
            <div class="known-annotation-image span1 hidden-phone">
                <p>
                    <a href="<?=$annotation['owner_url']?>" class="icon-container"><img src="<?=$annotation['owner_image']?>" /></a>
                </p>
            </div>
            <div class="known-annotation-content span6">
                <?=$this->autop($this->parseURLs($annotation['content']));?>
                <p><small><a href="<?=htmlspecialchars($annotation['owner_url'])?>"><?=$annotation['owner_name']?></a>,
                    <a href="<?=$permalink?>"><?=date('M d Y', $annotation['time']);?></a></small></p>
            </div>
            <?php
                $this->annotation_permalink = $locallink;
                
                if ($vars['object']->canEdit())
                    echo $this->draw('content/annotation/edit');
            ?>
        </div>
<?php

        }
    }