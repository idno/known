<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <h1><?php echo \Idno\Core\Idno::site()->language()->_('Following'); ?></h1>
        <?php echo $this->draw('following/menu')?>
    </div>
</div>
<?php
if (!empty($vars['subscriptions'])) {

    ?>
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <p id="follow-link"><a href="#" onclick="$('#follow-link').hide(); $('#follow-enclosure').slideDown(); return false;">+ <?php echo \Idno\Core\Idno::site()->language()->_('Follow another site'); ?></a></p>
            <div id="follow-enclosure" style="display:none"><?php echo $this->draw('following/add')?></div>
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
                <?php echo \Idno\Core\Idno::site()->language()->_("You're not following anyone yet."); ?>
                </p>
            <?php echo $this->draw('following/add')?>
            </div>
        </div>
    <?php
}
