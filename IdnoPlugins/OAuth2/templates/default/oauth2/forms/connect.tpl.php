<div class="row">
    <div class="col-md-6 col-md-offset-3 well text-center">

        <h2 class="text-center welcome"><?= \Idno\Core\Idno::site()->language()->_('Authorise connection to %s', [$vars['client']->getTitle()]); ?></h2>

        <form action="<?= \Idno\Core\site()->config()->getDisplayURL() ?>oauth2/connect" method="post">
        <input type="hidden" name="client_id" value="<?= htmlspecialchars($vars['client_id']);?>" />
        <input type="hidden" name="scope" value="<?= htmlspecialchars($vars['scope']);?>" />
        
            <div class="explanation">
        <p>
            <?php if ($vars['scope']) { ?>
                <?= \Idno\Core\Idno::site()->language()->_('Application has asked to connect to your account with the following privileges %s, do you want to allow it?', [$vars['scope']]); ?>
            <?php } else { ?>
                <?= \Idno\Core\Idno::site()->language()->_('Application has asked to connect to your account, do you want to allow it?'); ?>
            <?php } ?>
        </p>
        </div>
            <div class="control-group">
                <div class="controls">
                    <button type="submit" class="btn btn-primary btn-large">Allow</button>
            <a href="<?= \Idno\Core\site()->config()->getDisplayURL() ?>" class="btn btn-large btn-default"><?= \Idno\Core\Idno::site()->language()->_('Cancel'); ?></a>
                    <input type="hidden" name="fwd" value="<?= htmlspecialchars($vars['fwd']);?>" />
                </div>
            </div>
            
            <?= \Idno\Core\site()->actions()->signForm('/oauth2/connect') ?>
        </form>

    </div>
</div>
