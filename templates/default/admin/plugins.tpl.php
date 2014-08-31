<div class="row">

    <div class="span10 offset1">
        <h1>Site Features</h1>
        <?=$this->draw('admin/menu')?>
        <div class="explanation">
            <p>
                Site features allow you to add new kinds of content, syndicate content to different sites,
                and change the way Known behaves. To enable or disable a feature, just click its enable or
                disable button.
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