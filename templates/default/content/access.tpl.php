<?php
    if (\Idno\Core\site()->config->experimental) {
        ?>
        <div class="access-control-block">
            <input type="hidden" name="access" id="access-control-id" value="PUBLIC"/>

            <div id="access-control" class="acl">
                <div class="btn-group">
                    <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                        <span id="acl-text"><i class="icon-globe"> </i> Public</span>
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="#" data-acl="PUBLIC" class="acl-option"><i class="icon-globe"> </i> Public</a></li>
                        <li><a href="#" data-acl="<?= \Idno\Core\site()->session()->currentUserUUID() ?>"
                               class="acl-option"><i class="icon-lock"> </i> Private</a></li>
                        <?php
                            $acls = \Idno\Entities\AccessGroup::get(['owner' => \Idno\Core\site()->session()->currentUserUUID()]);
                            if (!empty($acls)) {
                                foreach ($acls as $acl) {

                                    $icon = 'icon-cog';
                                    if ($acl->access_group_type == 'FOLLOWING')
                                        $icon = 'icon-group';
                                    ?>
                                    <li><a href="#" data-acl="<?= $acl->getUUID(); ?>" class="acl-option"><i
                                                class="<?= $icon; ?>"> </i> <?= $acl->title; ?></a></li>
                                <?php
                                }
                            }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    <?php
    }
?>