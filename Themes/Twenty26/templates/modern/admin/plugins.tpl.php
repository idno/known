<?php
    ob_start();
?>
    <p class="idno-admin-description">
        <?= \Idno\Core\Idno::site()->language()->_('Plugins allow you to add features to your site. These include new kinds of content, options to syndicate content to different sites, and features to change the way Idno behaves. To enable or disable a plugin, just click its enable or disable button.') ?>
    </p>

    <?php
    $display = [];
    if (!empty($vars['plugins_stored']) && is_array($vars['plugins_stored'])) {
        foreach ($vars['plugins_stored'] as $shortname => $plugin) {
            if (\Idno\Core\Idno::site()->plugins()->isVisible($shortname)) {
                $plugin['shortname'] = $shortname;
                $display[$plugin['Plugin description']['name']] = $this->__(array('plugin' => $plugin))->draw('admin/plugins/plugin');
            }
        }
    }
    ksort($display);
    echo implode('', $display);
    ?>
<?php
    $content = ob_get_clean();
    echo $this->__([
        'body' => $content,
        'title' => \Idno\Core\Idno::site()->language()->_('Plugins')
    ])->draw('admin/shell');
?>
