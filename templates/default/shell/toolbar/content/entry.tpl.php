<li>
    <a href="<?= $vars['url'] ?>">
        <span class="dropdown-menu-icon"><?php
            if (!empty($vars['icon'])) {
                echo $vars['icon'];
            } else {
                echo '&nbsp;';
            }
        ?></span>
        <?= $vars['title'] ?>
    </a>
</li>
