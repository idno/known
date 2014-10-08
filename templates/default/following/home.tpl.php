<div class="row">
    <div class="span10 offset1">
        <h1>Following</h1>
        <?=$this->draw('following/menu')?>
    </div>
</div>
<?php
    if (!empty($vars['subscriptions'])) {

?>
    <div class="row">
        <div class="span10 offset1">
            <p id="follow-link"><a href="#" onclick="$('#follow-link').hide(); $('#follow-enclosure').slideDown(); return false;">+ Follow another site</a></p>
            <div id="follow-enclosure" style="display:none"><?=$this->draw('following/add')?></div>
        </div>
    </div>
<?php

        foreach($vars['subscriptions'] as $subscription) {

            /* @var \Idno\Entities\Reader\Subscription $subscription */
            echo $subscription->draw();

        }

    } else {
?>
        <div class="row">
            <div class="span10 offset1">
                <p>
                    You're not following anyone yet.
                </p>
                <?=$this->draw('following/add')?>
            </div>
        </div>
<?php
    }
?>