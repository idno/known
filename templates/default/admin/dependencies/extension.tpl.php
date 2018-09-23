<?php
$extension = $vars['extension'];

if (extension_loaded($extension)) {
    $label = 'label-success';
} else {
    $label = 'label-danger';
}
?><span class="label <?php echo $label ?>"><a href="https://php.net/<?php echo urlencode($extension) ?>" target="_blank" style="color: #fff"><?php echo $extension ?></a></span> 