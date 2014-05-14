<?php

    if (!empty($vars['annotations']) && is_array($vars['annotations'])) {
        foreach($vars['annotations'] as $permalink => $annotation) {
            ?>
            <div class="known-annotation row">
                <div class="known-annotation-content span6 offset1">
                    <p>
                        <a href="<?=htmlspecialchars($annotation['owner_url'])?>"><?=$annotation['owner_name']?></a>
                        <a href="<?=$permalink?>">reshared this post</a>
                    </p>
                    <p><small><a href="<?=$permalink?>"><?=date('M d Y', $annotation['time']);?></a></small></p>
                </div>
            </div>
        <?php

        }
    }