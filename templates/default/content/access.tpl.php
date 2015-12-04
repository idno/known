<?php

    $access = 'PUBLIC';
    if (!empty($vars['object'])) {
        if (!empty($vars['object']->access)) {
            $access = $vars['object']->access;
        }
    }

    if (!empty(\Idno\Core\Idno::site()->config()->show_privacy) || $access != 'PUBLIC') {

        ?>
        <div class="access-control-block">
            <input type="hidden" name="access" id="access-control-id" value="<?= htmlspecialchars($access); ?>"/>

            <?php

                //if (!empty(\Idno\Core\Idno::site()->config()->experimental)) {

            ?>

            <div id="access-control" class="acl">
                <div class="btn-group">
                    <a class="btn btn-info access dropdown-toggle" data-toggle="dropdown" href="#" id="access-button">
                        <span id="acl-text"><i class="fa fa-globe"> </i> Public</span>
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="#" data-acl="PUBLIC" class="acl-option"><i class="fa fa-globe"> </i> <?=\Idno\Core\Idno::site()->language()->get('Public')?></a>
                        </li>
                        <li><a href="#" data-acl="SITE" class="acl-option"><i class="fa fa-lock"> </i> <?=\Idno\Core\Idno::site()->language()->get('Members only')?></a></li>
                        <li><a href="#" data-acl="<?= \Idno\Core\Idno::site()->session()->currentUserUUID() ?>"
                               class="acl-option"><i class="fa fa-lock"></i> <?=\Idno\Core\Idno::site()->language()->get('Private')?></a></li>
                        <?php
                            $acls = \Idno\Entities\AccessGroup::get(array('owner' => \Idno\Core\Idno::site()->session()->currentUserUUID()));
                            if (!empty($acls)) {
                                foreach ($acls as $acl) {

                                    $icon = 'fa fa-cog';
                                    if ($acl->access_group_type == 'FOLLOWING')
                                        $icon = 'fa fa-users';
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

            <?php

                //}

            ?>

        </div>
        <script>

            $(document).ready(function () {
                $('.acl-option').each(function () {
                    if ($(this).data('acl') == $('#access-control-id').val()) {
                        $('#access-button').html($(this).html() + ' <span class="caret"></span>');
                    }
                })
            });
            $('.acl-option').on('click', function () {
                $('#access-control-id').val($(this).data('acl'));
                $('#access-button').html($(this).html() + ' <span class="caret"></span>');
                $('#access-button').click();
                //return false;
            });

            $('#access-control-id').on('change', function () {

            });

        </script>

    <?php

    } else {

        ?>
        <input type="hidden" name="access" id="access-control-id" value="<?= htmlspecialchars($access); ?>"/>
        <?php

    }

?>