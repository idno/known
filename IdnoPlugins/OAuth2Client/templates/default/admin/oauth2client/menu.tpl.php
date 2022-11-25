<li <?php if (strpos($_SERVER['REQUEST_URI'], '/admin/oauth2client/') !== false) echo 'class="active"'; ?>>
    <a href="<?=\Idno\Core\site()->config()->getDisplayURL(); ?>admin/oauth2client/"><?= \Idno\Core\Idno::site()->language()->_('OAuth2 Client'); ?></a>
</li>
