
    <div class="well well-large">
        <div class="row">
            <div class="span2">
                <p>
                    <strong><?=$vars['plugin']['name']?></strong> <?=$vars['plugin']['version']?><br />
                    <small>
                        by <a href="<?php

                            if (!empty($vars['plugin']['author_url'])) {
                                echo htmlspecialchars($vars['plugin']['author_url']);
                            } else {
                                echo '#';
                            }

                        ?>"><?=$vars['plugin']['author']?></a>
                    </small><br />
                    <?php

                        if (array_key_exists($vars['plugin']['shortname'],$vars['plugins_loaded'])) {
                            echo '<span class="label label-success">Loaded</span>';
                        } else {
                            echo '<span class="label">Not loaded</span>';
                        }

                    ?>
                </p>
            </div>
            <div class="span5">
                <?php

                    if (!empty($vars['plugin']['description'])) echo $this->autop($vars['plugin']['description']);

                ?>
            </div>
            <div class="span1 offset1">
                <?php

                    if (array_key_exists($vars['plugin']['shortname'],$vars['plugins_loaded'])) {
?>
                        <form action="<?=\Idno\Core\site()->config()->url; ?>admin/plugins/" method="post">
                            <p>
                                <input type="hidden" name="plugin" value="<?=$vars['plugin']['shortname']?>" />
                                <input type="hidden" name="action" value="uninstall" />
                                <input class="btn" type="submit" value="Uninstall" />
                            </p>
                            <?= \Idno\Core\site()->actions()->signForm('/admin/plugins/')?>
                        </form>
<?php
                    } else {
?>
                        <form action="<?=\Idno\Core\site()->config()->url; ?>admin/plugins/" method="post">
                            <p>
                                <input type="hidden" name="plugin" value="<?=$vars['plugin']['shortname']?>" />
                                <input type="hidden" name="action" value="install" />
                                <input class="btn" type="submit" value="Install" />
                            </p>
                            <?= \Idno\Core\site()->actions()->signForm('/admin/plugins/')?>
                        </form>
<?php
                    }

                ?>
            </div>
        </div>
    </div>
