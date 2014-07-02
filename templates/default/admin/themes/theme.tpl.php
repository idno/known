<div class="span3 theme">

    <h4><?= $vars['theme']['Theme description']['name'] ?> <?php if (\Idno\Core\site()->themes()->get() == $vars['theme']['shortname']) {
            echo '(Selected)';
        } ?></h4>
    <?php

        $src = '';
        if (!empty($vars['theme']['shortname'])) {
            if (file_exists(\Idno\Core\site()->config()->path . '/Themes/' . $vars['theme']['shortname'] . '/preview.png')) {
                $src = \Idno\Core\site()->config()->getURL() . 'Themes/' . $vars['theme']['shortname'] . '/preview.png';
            }
        } else {
            $src = \Idno\Core\site()->config()->getURL() . 'gfx/themes/default.png';
        }
        if (!empty($src)) {

    ?>
    <p><?php

            echo \Idno\Core\site()->actions()->createLink(\Idno\Core\site()->config()->url . 'admin/themes/', '<img src="' . $src . '" style="width: 100%">', ['theme' => $vars['theme']['shortname'], 'action' => 'install'], ['class' => '']);

            }

        ?></p>

    <p>
        Version <?= $vars['theme']['Theme description']['version'] ?><br>
        <a href="<?= $vars['theme']['Theme description']['author_url'] ?>"><?= $vars['theme']['Theme description']['author'] ?></a>
    </p>

    <p>
        <?= $vars['theme']['Theme description']['description'] ?>
    </p>

</div>