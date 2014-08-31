<?php

    $path = $vars['theme']['Theme description']['path'];
    $url = $vars['theme']['Theme description']['url'];

?>
<div class="span4 theme">

    <h4><?= $vars['theme']['Theme description']['name'] ?> <?php if (\Idno\Core\site()->themes()->get() == $vars['theme']['shortname']) {
            echo '(Selected)';
        } ?></h4>
    <?php

        if (!empty($vars['theme']['shortname'])) {
            if (file_exists($path . 'preview.png')) {
                $src = $url . 'preview.png';
            }
        } else {
            $src = \Idno\Core\site()->config()->getURL() . 'gfx/themes/default.png';
        }
        if (!empty($src)) {

    ?>
    <p><?php

            echo \Idno\Core\site()->actions()->createLink(\Idno\Core\site()->config()->url . 'admin/themes/', '<img src="' . $src . '" style="width: 100%">', ['theme' => $vars['theme']['shortname'], 'action' => 'install'], ['class' => '']);


        ?></p>
    <?php
        }
    ?>

    <p>
        Version <?= $vars['theme']['Theme description']['version'] ?><br>
        <a href="<?= $vars['theme']['Theme description']['author_url'] ?>"><?= $vars['theme']['Theme description']['author'] ?></a>
    </p>

    <p>
        <?= $vars['theme']['Theme description']['description'] ?>
    </p>

</div>