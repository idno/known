<?php

    if (!empty($vars['annotations']) && is_array($vars['annotations'])) {
        foreach($vars['annotations'] as $locallink => $annotation) {

            $permalink = $annotation['permalink'] ? $annotation['permalink'] : $locallink;

            ?>
            <div class="idno-annotation row">
                <div class="idno-annotation-image span1 hidden-phone">
                    <p>
                        <a href="<?=$annotation['owner_url']?>" class="icon-container"><img src="<?=$annotation['owner_image']?>" /></a>
                    </p>
                </div>
                <div class="idno-annotation-content span6">
                    <p>
                        <a href="<?=htmlspecialchars($annotation['owner_url'])?>"><?=$annotation['owner_name']?></a>
                        liked this post
                    </p>
                    <p><small><a href="<?=$permalink?>"><?=date('M d Y', $annotation['time']);?></a> on <a href="<?=$permalink?>"><?=parse_url($permalink, PHP_URL_HOST)?></a></small></p>
                </div>
            </div>
        <?php

        }
    }