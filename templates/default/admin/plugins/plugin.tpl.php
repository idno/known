<?php
    $plugin_description = $vars['plugin']['Plugin description'];
    $shortname = $vars['plugin']['shortname'];

    $requirements = null;
    if (isset($vars['plugin']['requirements'])) {
        $requirements = $vars['plugin']['requirements'];
    }

?>
<div class="well well-large">
    <div class="row">
        <div class="span2">
            <p>
                <strong><?= $plugin_description['name'] ?></strong> <?= $plugin_description['version'] ?><br/>
                <small>
                    by <a href="<?php

                        if (!empty($plugin_description['author_url'])) {
                            echo htmlspecialchars($plugin_description['author_url']);
                        } else {
                            echo '#';
                        }

                    ?>"><?= $plugin_description['author'] ?></a>
                </small>
                <br/>
                <?php

                    if (array_key_exists($shortname, $vars['plugins_loaded'])) {
                        echo '<span class="label label-success">Enabled</span>';
                    } else {
                        echo '<span class="label">Disabled</span>';
                    }

                ?>
            </p>
        </div>
        <div class="span5">
            <?php

                if (!empty($plugin_description['description'])) echo $this->autop($plugin_description['description']);

                if (isset($requirements)) {

                    ?>
                    <div class="requirements">

                        <?php
                            if (isset($requirements['idno'])) {
                                ?>
                                <p><label>Known
                                        Version: <?php echo $this->__(['version' => $requirements['idno']])->draw('admin/dependencies/idno'); ?> </label>
                                </p>
                            <?php
                            }
                        ?>

                        <?php
                            if (isset($requirements['php'])) {
                                ?>
                                <p><label>PHP
                                        Version: <?php echo $this->__(['version' => $requirements['php']])->draw('admin/dependencies/php'); ?> </label>
                                </p>
                            <?php
                            }
                        ?>

                        <?php
                            if (isset($requirements['extension'])) {
                                if (!is_array($requirements['extension']))
                                    $requirements['extension'] = [$requirements['extension']];
                                ?>
                                <p><label>Extensions: <?php
                                            foreach ($requirements['extension'] as $extension)
                                                echo $this->__(['extension' => $extension])->draw('admin/dependencies/extension');
                                        ?> </label></p>
                            <?php
                            }
                        ?>

                        <?php
                            if (isset($requirements['plugin'])) {
                                if (!is_array($requirements['plugin']))
                                    $requirements['plugin'] = [$requirements['plugin']];
                                ?>
                                <p><label>Plugins: <?php
                                            foreach ($requirements['plugin'] as $plugin) {
                                                list($plugin, $version) = explode(',', $plugin);
                                                echo $this->__(['plugin' => $plugin, 'version' => $version])->draw('admin/dependencies/plugin');
                                            }
                                        ?> </label></p>
                            <?php
                            }
                        ?>
                    </div>
                <?php
                }
            ?>
        </div>
        <div class="span1 offset1">
            <?php
                if (!in_array($shortname, \Idno\Core\site()->config()->alwaysplugins)) {
                    if (array_key_exists($shortname, $vars['plugins_loaded'])) {
                        ?>
                        <form action="<?= \Idno\Core\site()->config()->url ?>admin/plugins/" method="post">
                            <p>
                                <input type="hidden" name="plugin" value="<?= $shortname ?>"/>
                                <input type="hidden" name="action" value="uninstall"/>
                                <input class="btn" type="submit" value="Disable"/>
                            </p>
                            <?= \Idno\Core\site()->actions()->signForm(\Idno\Core\site()->config()->url . 'admin/plugins/') ?>
                        </form>
                    <?php
                    } else {
                        ?>
                        <form action="<?= \Idno\Core\site()->config()->url ?>admin/plugins/" method="post">
                            <p>
                                <input type="hidden" name="plugin" value="<?= $shortname ?>"/>
                                <input type="hidden" name="action" value="install"/>
                                <input class="btn" type="submit" value="Enable"/>
                            </p>
                            <?= \Idno\Core\site()->actions()->signForm(\Idno\Core\site()->config()->url . 'admin/plugins/') ?>
                        </form>
                    <?php
                    }
                }
            ?>
        </div>
    </div>
</div>
