<div class="navbar">
    <div class="navbar-inner">
        <ul class="nav">
            <li <?php if ($_SERVER['REQUEST_URI'] == '/admin/') echo 'class="active"'; ?>><a href="<?=\Idno\Core\site()->config()->getDisplayURL()?>admin/" >Site configuration</a></li>
            <li <?php if ($_SERVER['REQUEST_URI'] == '/admin/plugins/') echo 'class="active"'; ?>><a href="<?=\Idno\Core\site()->config()->getDisplayURL()?>admin/plugins/">Plugins</a></li>
            <li <?php if ($_SERVER['REQUEST_URI'] == '/admin/themes/') echo 'class="active"'; ?>><a href="<?=\Idno\Core\site()->config()->getDisplayURL()?>admin/themes/">Themes</a></li>
            <?=$this->draw('admin/menu/items')?>
            <li <?php if ($_SERVER['REQUEST_URI'] == '/admin/homepage/') echo 'class="active"'; ?>><a href="<?=\Idno\Core\site()->config()->getDisplayURL()?>admin/homepage/">Homepage</a></li>
            <li <?php if ($_SERVER['REQUEST_URI'] == '/admin/email/') echo 'class="active"'; ?>><a href="<?=\Idno\Core\site()->config()->getDisplayURL()?>admin/email/">Email</a></li>
            <li <?php if ($_SERVER['REQUEST_URI'] == '/admin/users/') echo 'class="active"'; ?>><a href="<?=\Idno\Core\site()->config()->getDisplayURL()?>admin/users/">Users</a></li>
            <li <?php if ($_SERVER['REQUEST_URI'] == '/admin/export/') echo 'class="active"'; ?>><a href="<?=\Idno\Core\site()->config()->getDisplayURL()?>admin/export/">Export</a></li>
            <li <?php if ($_SERVER['REQUEST_URI'] == '/admin/import/') echo 'class="active"'; ?>><a href="<?=\Idno\Core\site()->config()->getDisplayURL()?>admin/import/">Import</a></li>
            <li <?php if ($_SERVER['REQUEST_URI'] == '/admin/about/') echo 'class="active"'; ?>><a href="<?=\Idno\Core\site()->config()->getDisplayURL()?>admin/about/">About</a></li>

        </ul>
    </div>
</div>
