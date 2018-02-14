<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <h1><?= \Idno\Core\Idno::site()->language()->_('Following'); ?></h1>
        <?=$this->draw('following/menu')?>
    </div>
</div>
<?php
    if (!empty($vars['subscriptions'])) {

?>
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <p id="follow-link"><a href="#" onclick="$('#follow-link').hide(); $('#follow-enclosure').slideDown(); return false;">+ <?= \Idno\Core\Idno::site()->language()->_('Follow another site'); ?></a></p>
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
            <div class="col-md-10 col-md-offset-1">
                <p>
                    <?= \Idno\Core\Idno::site()->language()->_("You're not following anyone yet."); ?>
                </p>
                <?=$this->draw('following/add')?>
            </div>
        </div>
<?php
    }
?>