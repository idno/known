<?php

    if (!empty($vars['service'])) {

?>
        <span class="syndication_<?=$vars['service']?>">
            <input type="checkbox" name="syndication[]" id="syndication_<?=$vars['service']?>_toggle" value="<?=$vars['service']?>">
            <label for="syndication_<?=$vars['service']?>_toggle"><?=ucfirst($vars['service']);?></label>
        </span>
<?php

    }