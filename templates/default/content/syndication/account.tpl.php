<?php

    if (!empty($vars['service']) && !empty($vars['username'])) {

        if (empty($vars['name'])) {
            $vars['name'] = $vars['username'];
        }

        $identifier = preg_replace("/[^A-Za-z0-9 ]/", '', $vars['username']);

        ?>
        <span class="syndication_<?=$vars['service']?>_<?=$identifier?>">
            <input type="checkbox" name="syndication[]" id="syndication_<?=$vars['service']?>_<?=$identifier?>_toggle" value="<?=$vars['service']?>::<?=htmlentities($vars['username'])?>">
            <label for="syndication_<?=$vars['service']?>_<?=$identifier?>_toggle"><?=$vars['name'];?></label>
        </span>
    <?php

    }