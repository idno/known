<div class="navbar">
    <div class="navbar-inner">
        <ul class="nav">
            <li <?php if ($_SERVER['REQUEST_URI'] == '/admin/') echo 'class="active"'; ?>><a href="<?=\Idno\Core\site()->config()->url?>admin/" >Site configuration</a></li>
            <li <?php if ($_SERVER['REQUEST_URI'] == '/admin/plugins/') echo 'class="active"'; ?>><a href="<?=\Idno\Core\site()->config()->url?>admin/plugins/">Site Features</a></li>
            <li <?php if ($_SERVER['REQUEST_URI'] == '/admin/themes/') echo 'class="active"'; ?>><a href="<?=\Idno\Core\site()->config()->url?>admin/themes/">Themes</a></li>
            <?=$this->draw('admin/menu/items')?>
            <li <?php if ($_SERVER['REQUEST_URI'] == '/admin/email/') echo 'class="active"'; ?>><a href="<?=\Idno\Core\site()->config()->url?>admin/email/">Email</a></li>
            <li <?php if ($_SERVER['REQUEST_URI'] == '/admin/users/') echo 'class="active"'; ?>><a href="<?=\Idno\Core\site()->config()->url?>admin/users/">Users</a></li>
            <li <?php if ($_SERVER['REQUEST_URI'] == '/admin/about/') echo 'class="active"'; ?>><a href="<?=\Idno\Core\site()->config()->url?>admin/about/">About</a></li>

        </ul>
    </div>
</div>