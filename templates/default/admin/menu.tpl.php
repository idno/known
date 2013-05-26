<div class="navbar">
    <div class="navbar-inner">
        <ul class="nav">
            <li <?php if ($_SERVER['REQUEST_URI'] == '/admin/') echo 'class="active"'; ?>><a href="/admin/" >Administration</a></li>
            <li <?php if ($_SERVER['REQUEST_URI'] == '/admin/plugins/') echo 'class="active"'; ?>><a href="/admin/plugins/">Plugins</a></li>
            <?=$this->draw('admin/menu/items')?>
        </ul>
    </div>
</div>