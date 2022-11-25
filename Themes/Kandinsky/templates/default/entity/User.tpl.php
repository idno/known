<div class="row profile h-card">
    <div class="col-md-8 col-md-offset-2">
        <div class="row visible-sm">
            <div class="col-md-2">
                <div style="margin-bottom: 2em; margin-top: -2em; text-align: center">
                    <p>
                        <?php echo $this->draw('entity/User/profile/contact') ?>
                    </p>

                    <p style="margin-bottom: 2em" class="clearall"></p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8 col-md-offset-2 namebadge">
                <p style="margin: 0px;">
                    <a href="<?php echo $vars['user']->getDisplayURL() ?>" class="u-url icon-container"><img class="u-photo"
                                                                                               src="<?php echo $vars['user']->getIcon() ?>"/></a>
                </p>
            </div>
            <div class="col-md-8 col-md-offset-2">
                <div class="row">
                                        <div class="">
                        <?php

                        if ($vars['user']->canEdit()) {
                            // If you're wondering, this is wrapped in an h1 tag to keep it aligned with
                            // the user's name over in the next div. TODO: find a better way to do this
                            // that retains visual consistency.
                            ?>
                                            <p style="text-align: center;"><a href="<?php echo $vars['user']->getEditURL() ?>" class="btn btn-primary"><?= \Idno\Core\Idno::site()->language()->_('Edit profile'); ?></a></p>
                            <?php

                        }

                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="">
                        <h1 class="p-profile">
                            <a href="<?php echo $vars['user']->getDisplayURL() ?>"
                               class="u-url p-name fn"><?php echo $vars['user']->getTitle() ?></a>
                        </h1>
                    </div>

                </div>
                <div class="row">
                    <div class="">
                        <div class="e-note"><?php
                                $description = $vars['user']->getDescription();
                        if (!empty($description)) {
                            echo '<div class="highlightedText">' . $this->autop($vars['user']->getDescription()) . '</div>';
                        } else if ($vars['user']->getUUID() == \Idno\Core\Idno::site()->session()->currentUserUUID()) {
                            ?>
                                    <p class="highlightedText">
                                        <?= \Idno\Core\Idno::site()->language()->_('A profile helps you describe yourself to other people on the site and on the web. You haven\'t described yourself yet.'); ?>
                                        <a href="<?php echo $vars['user']->getDisplayURL() ?>/edit/"><?= \Idno\Core\Idno::site()->language()->_('Click here to fill in your profile information.'); ?></a>
                                    </p>
                                <?php
                        }
                        ?></div>
                    </div>
                </div>
                                <div class="row">
                    <div style="text-align: center;">

                        <?php echo $this->draw('entity/User/profile/fields') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>