<?php

    $access = 'PUBLIC';
    if (!empty($vars['object'])) {
        if (!empty($vars['object']->access)) {
            $access = $vars['object']->access;
        }
    }

    if (\Idno\Core\site()->config->experimental) {
        ?>
        <div class="access-control-block">
            <input type="hidden" name="access" id="access-control-id" value="<?=htmlspecialchars($access);?>"/>

            <div id="access-control" class="acl">
                <div class="btn-group">
                    <a class="btn dropdown-toggle" data-toggle="dropdown" href="#" id="access-button">
                        <span id="acl-text"><i class="icon-globe"> </i> Public</span>
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="#" data-acl="PUBLIC" class="acl-option"><i class="icon-globe"> </i> Public</a></li>
                        <li><a href="#" data-acl="SITE" class="acl-option"><i class="icon-lock"> </i> Members only</a></li>
                        <li><a href="#" data-acl="<?= \Idno\Core\site()->session()->currentUserUUID() ?>"
                               class="acl-option"><i class="icon-lock"> </i> Private</a></li>
                        <?php
                            $acls = \Idno\Entities\AccessGroup::get(array('owner' => \Idno\Core\site()->session()->currentUserUUID()));
                            if (!empty($acls)) {
                                foreach ($acls as $acl) {

                                    $icon = 'icon-cog';
                                    if ($acl->access_group_type == 'FOLLOWING')
                                        $icon = 'icon-group';
                                    ?>
                                    <li><a href="#" data-acl="<?= $acl->getUUID(); ?>" class="acl-option"><i
                                                class="<?= $icon; ?> acl-option"> </i> <?= $acl->title; ?></a></li>
                                <?php
                                }
                            }
                        ?>
                    </ul>
                </div>
            </div>

        </div>
        <script>

            $(document).ready(function() {
                $('.acl-option').each(function() {
                    if ($(this).data('acl') == $('#access-control-id').val()) {
                        $('#access-button').html($(this).html() + ' <span class="caret"></span>');
                    }
                })
            });
            $('.acl-option').on('click', function() {
                $('#access-control-id').val($(this).data('acl'));
                $('#access-button').html($(this).html() + ' <span class="caret"></span>');
                $('#access-button').click();
                return false;
            });

            $('#access-control-id').on('change', function() {

            });

        </script>
    <?php
    } else {

        ?>
        <input type="hidden" name="access" id="access-control-id" value="<?=htmlspecialchars($access);?>"/>
        <?php

    }
?>