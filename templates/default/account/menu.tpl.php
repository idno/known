<div class="navbar">
    <div class="navbar-inner">
        <ul class="nav">
            <li <?php if ($_SERVER['REQUEST_URI'] == '/account/settings/') echo 'class="active"'; ?>><a href="/account/settings/" >Account settings</a></li>
            <?=$this->draw('account/menu/items')?>
        </ul>
    </div>
</div>