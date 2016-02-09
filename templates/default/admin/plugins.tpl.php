<div class="row">

    <div class="col-md-10 col-md-offset-1">
	<?=$this->draw('admin/menu')?>
        <h1>Plugins</h1>

        <div class="explanation">
            <p>
                Plugins allow you to add features to your site. These include new kinds of content, options to syndicate
                content to different sites, and features to change the way Known behaves. To enable or disable a plugin,
                just click its enable or disable button.
            </p>
        </div>
        <?php

            $display = [];
            if (!empty($vars['plugins_stored']) && is_array($vars['plugins_stored'])) {
                foreach($vars['plugins_stored'] as $shortname => $plugin) {
                    if (\Idno\Core\Idno::site()->plugins()->isVisible($shortname)) {
                        $plugin['shortname'] = $shortname;
                        $display[$plugin['Plugin description']['name']] = $this->__(array('plugin' => $plugin))->draw('admin/plugins/plugin');
                    }
                }
            }
            ksort($display);
            echo implode('',$display);

        ?>
    </div>

</div>