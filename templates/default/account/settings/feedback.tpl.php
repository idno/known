<div class="row">

    <div class="col-md-10 col-md-offset-1">
        <h1>
            <?= \Idno\Core\Idno::site()->language()->_('Share your feedback'); ?>
        </h1>
        <!--<?= $this->draw('account/menu') ?>-->
        <p class="explanation">
            <?= \Idno\Core\Idno::site()->language()->_("Want to share something with the Known team? We'd love to read your thoughts, suggestions, or ideas. We will personally read all of your feedback."); ?>
        </p>
        
    </div>
</div>

<form class="form-horizontal" action="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>account/settings/feedback"
      method="post">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">

            <p class="feedback">
                <strong><?= \Idno\Core\Idno::site()->language()->_('From'); ?>:</strong> <?= \Idno\Core\Idno::site()->session()->currentUser()->email ?>
                <input type="hidden" name="email"
                       value="<?= htmlentities(\Idno\Core\Idno::site()->session()->currentUser()->email) ?>">
            </p>

            <p class="feedback"><strong>To:</strong> feedback@withknown.com</p>
            <br>

            <p class="feedback"><strong>Subject:</strong> <?= \Idno\Core\Idno::site()->language()->_('Feedback for the Known team'); ?></p>

            <div class="control-group">
                <textarea rows="7" class="feedback" placeholder="<?= \Idno\Core\Idno::site()->language()->_('Let us know what you think.'); ?>" name="message" required></textarea>

                <div class="control-group">
                    <div class="feedback-btn">
                        <?= \Idno\Core\Idno::site()->actions()->signForm('/account/settings/feedback') ?>
                        <input type="submit" class="btn btn-primary" value="<?= \Idno\Core\Idno::site()->language()->_('Send feedback'); ?>">
                    </div>
                </div>


            </div>
        </div>
    </div>
</form>
