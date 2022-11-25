<div class="row h-card">
    <div class="col-md-8 profile col-md-offset-2">

        <div class="">
            <div class="namebadge">
                <p>
                    <a href="<?php echo $vars['user']->getDisplayURL() ?>" class="u-url icon-container"><img class="u-photo"
                                                                                               src="<?php echo $vars['user']->getIcon() ?>"/></a>
                </p>
            </div>
            <div class=" ">
                <div class="">
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
                        ?>
                        </div>

                        <?php echo $this->draw('entity/User/profile/fields') ?>
                        <?php

                        if ($vars['user']->canEdit() && $vars['user']->getUUID() == \Idno\Core\Idno::site()->session()->currentUserUUID()) {
                            // If you're wondering, this is wrapped in an h1 tag to keep it aligned with
                            // the user's name over in the next div. TODO: find a better way to do this
                            // that retains visual consistency.
                            ?>
                                <p style=""><a href="<?php echo $vars['user']->getEditURL() ?>" class="btn btn-large"><?= \Idno\Core\Idno::site()->language()->_('Edit profile'); ?></a></p>
                            <?php

                        }

                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>