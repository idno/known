<ul class="nav nav-tabs">
  <li <?php if (\Idno\Core\Idno::site()->currentPage()->doesPathMatch('/admin/')) echo 'class="active"'; ?> role="presentation"><a href="<?=\Idno\Core\Idno::site()->config()->getDisplayURL()?>admin/">Site configuration</a></li>
  <li <?php if (\Idno\Core\Idno::site()->currentPage()->doesPathMatch('/admin/plugins/')) echo 'class="active"'; ?> role="presentation"><a href="<?=\Idno\Core\Idno::site()->config()->getDisplayURL()?>admin/plugins/">Plugins</a></li>
  <li <?php if (\Idno\Core\Idno::site()->currentPage()->doesPathMatch('/admin/themes/')) echo 'class="active"'; ?> role="presentation"><a href="<?=\Idno\Core\Idno::site()->config()->getDisplayURL()?>admin/themes/">Themes</a></li>
            <?=$this->draw('admin/menu/items')?>
  <li <?php if (\Idno\Core\Idno::site()->currentPage()->doesPathMatch('/admin/homepage/')) echo 'class="active"'; ?> role="presentation"><a href="<?=\Idno\Core\Idno::site()->config()->getDisplayURL()?>admin/homepage/" >Homepage</a></li>
  <li <?php if (\Idno\Core\Idno::site()->currentPage()->doesPathMatch('/admin/email/')) echo 'class="active"'; ?> role="presentation"><a href="<?=\Idno\Core\Idno::site()->config()->getDisplayURL()?>admin/email/" >Email</a></li>
  <li <?php if (\Idno\Core\Idno::site()->currentPage()->doesPathMatch('/admin/users/')) echo 'class="active"'; ?> role="presentation"><a href="<?=\Idno\Core\Idno::site()->config()->getDisplayURL()?>admin/users/" >Users</a></li>
  <li <?php if (\Idno\Core\Idno::site()->currentPage()->doesPathMatch('/admin/export/')) echo 'class="active"'; ?> role="presentation"><a href="<?=\Idno\Core\Idno::site()->config()->getDisplayURL()?>admin/export/" >Export</a></li>
  <li <?php if (\Idno\Core\Idno::site()->currentPage()->doesPathMatch('/admin/import/')) echo 'class="active"'; ?> role="presentation"><a href="<?=\Idno\Core\Idno::site()->config()->getDisplayURL()?>admin/import/" >Import</a></li>
  <li <?php if (\Idno\Core\Idno::site()->currentPage()->doesPathMatch('/admin/diagnostics/')) echo 'class="active"'; ?> role="presentation"><a href="<?=\Idno\Core\Idno::site()->config()->getDisplayURL()?>admin/diagnostics/">Diagnostics</a></li>
  <?php if (!empty(\Idno\Core\Idno::site()->config()->capture_logs) && \Idno\Core\Idno::site()->config()->capture_logs) { ?>
    <li <?php if (\Idno\Core\Idno::site()->currentPage()->doesPathMatch('/admin/logs/')) echo 'class="active"'; ?> role="presentation"><a href="<?=\Idno\Core\Idno::site()->config()->getDisplayURL()?>admin/logs/">Captured Logs</a></li>
  <?php } ?>
  <li <?php if (\Idno\Core\Idno::site()->currentPage()->doesPathMatch('/admin/about/')) echo 'class="active"'; ?> role="presentation"><a href="<?=\Idno\Core\Idno::site()->config()->getDisplayURL()?>admin/about/">About</a></li>
</ul>
