<div class="navbar">
    <div class="navbar-inner">
        <ul class="nav">
            <li <?php if ($_SERVER['REQUEST_URI'] == '/account/settings/') echo 'class="active"'; ?>><a href="<?=\Idno\Core\Idno::site()->config()->getDisplayURL()?>account/settings/" >Account settings</a></li>
            <li <?php if ($_SERVER['REQUEST_URI'] == '/account/settings/notifications/') echo 'class="active"'; ?>><a href="<?=\Idno\Core\Idno::site()->config()->getDisplayURL()?>account/settings/notifications/" >Notifications</a></li>
            <?php /*

            This is an early development feature and is not ready to be exposed.
            */ 
            if (\Idno\Core\Idno::site()->config()->experimental) {
            ?>
	        <li <?php if ($_SERVER['REQUEST_URI'] == '/account/settings/following/') echo 'class="active"'; ?>><a href="<?=\Idno\Core\Idno::site()->config()->getDisplayURL()?>account/settings/following/" >Following</a></li>
            <?php } ?>
            <?=$this->draw('account/menu/items')?>
        </ul>
    </div>
</div>