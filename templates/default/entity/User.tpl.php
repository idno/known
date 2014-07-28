<div class="row profile h-card">
    <div class="span8 offset2">
        <div class="row visible-phone">
            <div class="span2">
                <div style="margin-bottom: 2em; margin-top: -2em; text-align: center">
                    <p>
                        <?= $this->draw('entity/User/profile/contact') ?>
                    </p>

                    <p style="margin-bottom: 2em" clear="all"></p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="span2 namebadge">
                <p>
                    <a href="<?= $vars['user']->getURL() ?>" class="u-url icon-container"><img class="u-photo"
                                                                                               src="<?= $vars['user']->getIcon() ?>"/></a>
                </p>
            </div>
            <div class="span6 ">
                <div class="row">
                    <div class="span5">
                        <h1 class="p-profile">
                            <a href="<?= $vars['user']->getURL() ?>"
                               class="u-url p-name fn"><?= $vars['user']->getTitle() ?></a>
                        </h1>
                    </div>
                    <div class="span1">
                        <?php

                            if ($vars['user']->canEdit()) {
                                // If you're wondering, this is wrapped in an h1 tag to keep it aligned with
                                // the user's name over in the next div. TODO: find a better way to do this
                                // that retains visual consistency.
                                ?>
                                <h1><a href="<?= $vars['user']->getEditURL() ?>" class="btn btn-large">Edit</a></h1>
                            <?php

                            }

                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="span6">
                        <div class="e-note"><?php
                                $description = $vars['user']->getDescription();
                                if (!empty($description)) {
                                    echo '<div class="highlightedText">' . $this->autop($vars['user']->getDescription()) . '</div>';
                                } else if ($vars['user']->getUUID() == \Idno\Core\site()->session()->currentUserUUID()) {
                                    ?>
                                    <p class="highlightedText">
                                        A profile helps you describe yourself to other people on the site
                                        and on the web. You haven't described yourself yet.
                                        <a href="<?= $vars['user']->getURL() ?>/edit/">Click here to fill in your
                                            profile information.</a>
                                    </p>
                                <?php
                                }
                            ?></div>

                        <?= $this->draw('entity/User/profile/fields') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>