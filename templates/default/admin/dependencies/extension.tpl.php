<?php
$extension = $vars['extension'];

if (extension_loaded($extension)) {
    $label = 'label-success';
} else {
    $label = 'label-important';
}
?><span class="label <?= $label ?>"><a href="http://php.net/<?= urlencode($extension) ?>" target="_blank" style="color: #fff"><?= $extension ?></a></span> 