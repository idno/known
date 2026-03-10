<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <?php echo $this->draw('admin/menu')?>

        <h1><?php echo \Idno\Core\Idno::site()->language()->_('Standard.site Sync'); ?></h1>

        <p class="explanation">
            <?php echo \Idno\Core\Idno::site()->language()->_('Sync your writing to <a href="https://standard.site">standard.site</a> records on an AT Protocol Personal Data Server (PDS). This makes your content discoverable across the AT Protocol network via apps like Leaflet, Frontpage, and others that support the standard.site lexicons.'); ?>
        </p>

        <!-- Connection Status -->
        <div class="well">
            <h3><?php echo \Idno\Core\Idno::site()->language()->_('Connection'); ?></h3>

            <?php if (!empty($vars['connected'])) { ?>

                <p>
                    <span class="label label-success"><?php echo \Idno\Core\Idno::site()->language()->_('Connected'); ?></span>
                </p>
                <p>
                    <strong><?php echo \Idno\Core\Idno::site()->language()->_('DID'); ?>:</strong>
                    <code><?php echo htmlspecialchars($vars['did'])?></code>
                </p>
                <p>
                    <strong><?php echo \Idno\Core\Idno::site()->language()->_('PDS'); ?>:</strong>
                    <code><?php echo htmlspecialchars($vars['pds'])?></code>
                </p>
                <p>
                    <strong><?php echo \Idno\Core\Idno::site()->language()->_('Publication URI'); ?>:</strong>
                    <code>at://<?php echo htmlspecialchars($vars['did'])?>/site.standard.publication/self</code>
                </p>
                <p>
                    <strong><?php echo \Idno\Core\Idno::site()->language()->_('Well-Known'); ?>:</strong>
                    <code><?php echo \Idno\Core\Idno::site()->config()->getDisplayURL()?>.well-known/site.standard.publication</code>
                </p>

                <form action="<?php echo \Idno\Core\Idno::site()->config()->getDisplayURL()?>admin/standardsitesync/disconnect/" method="post" style="margin-top: 1em;">
                    <?php echo \Idno\Core\Idno::site()->actions()->signForm('/admin/standardsitesync/disconnect/') ?>
                    <input class="btn btn-danger btn-sm" type="submit" value="<?php echo \Idno\Core\Idno::site()->language()->_('Disconnect from PDS'); ?>" onclick="return confirm('<?php echo \Idno\Core\Idno::site()->language()->_('Are you sure? This will stop syncing and remove the stored credentials.'); ?>');">
                </form>

            <?php } else { ?>

                <p>
                    <span class="label label-default"><?php echo \Idno\Core\Idno::site()->language()->_('Not connected'); ?></span>
                </p>
                <p>
                    <?php echo \Idno\Core\Idno::site()->language()->_('Connect to your AT Protocol PDS to start syncing your writing. You will be redirected to your PDS to authorize this site.'); ?>
                </p>

                <form action="<?php echo \Idno\Core\Idno::site()->config()->getDisplayURL()?>admin/standardsitesync/" method="post">
                    <?php echo \Idno\Core\Idno::site()->actions()->signForm('/admin/standardsitesync/') ?>
                    <input type="hidden" name="action" value="connect">
                    <div class="row" style="margin-bottom: 1em;">
                        <div class="col-md-6">
                            <input type="text" name="handle" placeholder="<?php echo \Idno\Core\Idno::site()->language()->_('your-handle.bsky.social'); ?>" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <input class="btn btn-primary" type="submit" value="<?php echo \Idno\Core\Idno::site()->language()->_('Connect via OAuth'); ?>">
                        </div>
                    </div>
                </form>

            <?php } ?>
        </div>

        <?php if (!empty($vars['connected'])) { ?>

        <!-- Sync Settings -->
        <div class="well">
            <h3><?php echo \Idno\Core\Idno::site()->language()->_('Automatic Sync'); ?></h3>
            <p class="explanation">
                <?php echo \Idno\Core\Idno::site()->language()->_('When enabled, any new or edited content will be automatically synced to your PDS as standard.site document records.'); ?>
            </p>

            <form action="<?php echo \Idno\Core\Idno::site()->config()->getDisplayURL()?>admin/standardsitesync/" method="post">
                <?php echo \Idno\Core\Idno::site()->actions()->signForm('/admin/standardsitesync/') ?>
                <input type="hidden" name="action" value="toggle_sync">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="sync_enabled" value="1" <?php if (!empty($vars['sync_enabled'])) echo 'checked'; ?>>
                        <?php echo \Idno\Core\Idno::site()->language()->_('Enable automatic sync of new and edited content'); ?>
                    </label>
                </div>
                <p>
                    <input class="btn btn-primary" type="submit" value="<?php echo \Idno\Core\Idno::site()->language()->_('Save'); ?>">
                </p>
            </form>
        </div>

        <!-- Backfill -->
        <div class="well">
            <h3><?php echo \Idno\Core\Idno::site()->language()->_('Backfill Existing Content'); ?></h3>
            <p class="explanation">
                <?php echo \Idno\Core\Idno::site()->language()->_('Sync all your existing published content to your PDS as a one-time operation. Posts that have already been synced will be skipped.'); ?>
            </p>

            <form action="<?php echo \Idno\Core\Idno::site()->config()->getDisplayURL()?>admin/standardsitesync/backfill/" method="post">
                <?php echo \Idno\Core\Idno::site()->actions()->signForm('/admin/standardsitesync/backfill/') ?>
                <input class="btn btn-warning" type="submit" value="<?php echo \Idno\Core\Idno::site()->language()->_('Backfill All Posts to PDS'); ?>" onclick="return confirm('<?php echo \Idno\Core\Idno::site()->language()->_('This will sync all your existing published content to your PDS. Already-synced posts will be skipped. Continue?'); ?>');">
            </form>
        </div>

        <?php } ?>

    </div>
</div>
