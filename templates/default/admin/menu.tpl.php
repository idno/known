<div class="navbar">
    <div class="navbar-inner">
        <ul class="nav">
            <li <?php if ($_SERVER['REQUEST_URI'] == '/admin/') echo 'class="active"'; ?>><a href="<?=\Idno\Core\site()->config()->url?>admin/" >Administration</a></li>
            <li <?php if ($_SERVER['REQUEST_URI'] == '/admin/plugins/') echo 'class="active"'; ?>><a href="<?=\Idno\Core\site()->config()->url?>admin/plugins/">Plugins</a></li>
            <li <?php if ($_SERVER['REQUEST_URI'] == '/admin/dependencies/') echo 'class="active"'; ?>><a href="<?=\Idno\Core\site()->config()->url?>admin/dependencies/">Dependencies</a></li>
            <li <?php if ($_SERVER['REQUEST_URI'] == '/admin/email/') echo 'class="active"'; ?>><a href="<?=\Idno\Core\site()->config()->url?>admin/email/">Email</a></li>
            <?=$this->draw('admin/menu/items')?>
            <li <?php if ($_SERVER['REQUEST_URI'] == '/admin/management/') echo 'class="active"'; ?>><a href="<?=\Idno\Core\site()->config()->url?>admin/management/">User Management</a></li>
            <li <?php if ($_SERVER['REQUEST_URI'] == '/admin/about/') echo 'class="active"'; ?>><a href="<?=\Idno\Core\site()->config()->url?>admin/about/">About</a></li>

        </ul>
    </div>
</div>