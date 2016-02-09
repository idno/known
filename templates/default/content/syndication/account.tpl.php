<?php

    if (!empty($vars['service']) && !empty($vars['username'])) {

        if (empty($vars['name'])) {
            $vars['name'] = $vars['username'];
        }

        $identifier = preg_replace("/[^A-Za-z0-9 ]/", '', $vars['username']);

        $human_name = str_replace(' ', '&nbsp;', htmlspecialchars($vars['name']));
        $human_icon = $this->draw('content/syndication/icon/' . $vars['service']);
        if (empty($human_icon)) {
            $human_icon = $this->draw('content/syndication/icon/generic');
        }
        $human_icon = htmlspecialchars($human_icon);

        ?>
        <span class="syndication-toggle">
            <input type="checkbox" class="checkbox" <?=$vars['disabled']?> name="syndication[]" id="syndication_<?=$vars['service']?>_<?=$identifier?>_toggle" value="<?=$vars['service']?>::<?=htmlentities($vars['username'])?>" data-toggle="toggle" data-onstyle="info" data-on="<?=$human_icon;?>&nbsp;<?=$human_name;?>" data-off="<?=$human_icon;?>&nbsp;<?=$human_name;?>" <?php if ($vars['selected'] == true) echo 'checked'; ?>>
        </span>
    <?php

    }