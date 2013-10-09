<input type="hidden" name="access" id="access-control-id" value="PUBLIC" />
<div id="access-control" class="acl">
    <div class="btn-group">
        <button class="btn btn-mini" id="acl-text">Access</button>
        <button class="btn btn-mini dropdown-toggle" data-toggle="dropdown">
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu">
            <li><a href="#" data-access="PUBLIC"><i class="icon-globe"> </i> Public</a></li>
            <?php
            $acls = \Idno\Entities\AccessGroup::get(['owner' => \Idno\Core\site()->session()->currentUserUUID()]);
            if (!empty($acls)) {
                foreach ($acls as $acl) {
                    ?>
                    <li><a href="#" data-acl="<?= $acl->getUUID(); ?>"><i class="icon-group"> </i> <?= $acl->title; ?></a></li>
                        <?php
                    }
                }
                ?>
        </ul>
    </div>
</div>
