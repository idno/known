<?php

    if (!empty($vars['annotations']) && is_array($vars['annotations'])) {
        foreach($vars['annotations'] as $permalink => $annotation) {
            ?>
            <div class="known-annotation row">
                <div class="known-annotation-image span1 hidden-phone">
                    <p>
                        <a href="<?=$annotation['owner_url']?>" class="icon-container"><img src="<?=$annotation['owner_image']?>" /></a>
                    </p>
                </div>
                <div class="known-annotation-content span6">
                    <p>
                        <a href="<?=htmlspecialchars($annotation['owner_url'])?>"><?=$annotation['owner_name']?></a>
                        RSVPed <strong><?=$annotation['content']?></strong>
                    </p>
                    <p><small><a href="<?=$permalink?>"><?=date('M d Y', $annotation['time']);?></a></small></p>
                </div>
            </div>
        <?php

        }
    }