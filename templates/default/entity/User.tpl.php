<div class="row profile h-card">
    <div class="span8 offset2">
        <div class="row">
            <div class="span2 namebadge">
                <p>
                    <a href="<?=$vars['user']->getURL()?>" class="u-url icon-container"><img class="u-photo" src="<?=$vars['user']->getIcon()?>" /></a>
                </p>
            </div>
            <div class="span6 ">
                <div class="row">
                    <div class="span5">
                        <h1 >
                            <a href="<?=$vars['user']->getURL()?>" class="u-url p-name"><?=$vars['user']->getTitle()?></a>
                        </h1>
                    </div>
                    <div class="span1">
                        <?php

                            if ($vars['user']->canEdit()) {
                                // If you're wondering, this is wrapped in an h1 tag to keep it aligned with
                                // the user's name over in the next div. TODO: find a better way to do this
                                // that retains visual consistency.
                                ?>
                                <h1><a href="<?=$vars['user']->getEditURL()?>" class="btn btn-large">Edit</a></h1>
                            <?php

                            }

                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="span6">
                        <div class="p-note"><?=$this->autop($vars['user']->getDescription())?></div>

                        <?=$this->draw('entity/User/profile/fields')?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>