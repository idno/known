<div class="row">

    <div class="span10 offset1">
        <h1>Plugins</h1>
        <?=$this->draw('admin/menu')?>
        <div class="explanation">
            <p>
                Plugins allow you to add new kinds of content, syndicate content to different sites,
                and change the way idno behaves. To enable or disable a plugin, just click its enable or
                disable button. You can always get more plugins from
                <a href="http://idno.co" target="_blank">the official idno website</a>.
            </p>
        </div>
        <?php

            if (!empty($vars['plugins_stored']) && is_array($vars['plugins_stored'])) {
                foreach($vars['plugins_stored'] as $shortname => $plugin) {
                    $plugin['shortname'] = $shortname;
                    echo $this->__(['plugin' => $plugin])->draw('admin/plugins/plugin');
                }
            }

        ?>
    </div>

</div>