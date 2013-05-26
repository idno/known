<div class="row">

    <div class="span10 offset1">
        <h1>Plugins</h1>
        <?=$this->draw('admin/menu')?>
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