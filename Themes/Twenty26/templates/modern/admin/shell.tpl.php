<?php
    /* Admin layout wrapper. Admin page templates call:
     * $this->__(['body' => $content, 'title' => $title])->draw('admin/shell')
     */
?>
<div class="idno-admin-shell" style="margin-left:-14rem;min-height:100vh">
    <aside class="idno-admin-sidebar">
        <a href="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>admin/" class="idno-admin-logo">
            <?= htmlspecialchars(\Idno\Core\Idno::site()->config()->getTitle()) ?>
        </a>
        <?= $this->draw('admin/menu') ?>
        <div style="margin-top:auto;padding:0 0.75rem">
            <a href="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>" class="idno-admin-nav-item">
                <?= $this->__(['icon' => 'arrow-left'])->draw('shell/icon') ?>
                <span><?= \Idno\Core\Idno::site()->language()->_('Back to site') ?></span>
            </a>
        </div>
    </aside>
    <div class="idno-admin-main">
        <div class="idno-admin-content">
            <?php if (!empty($vars['title'])) { ?>
                <h1 class="idno-admin-page-title"><?= $vars['title'] ?></h1>
            <?php } ?>
            <?= $vars['body'] ?>
        </div>
    </div>
</div>
