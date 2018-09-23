<?php
$details = \Idno\Core\Idno::site()->language()->_('%s installed', [\Idno\Core\Version::build()]);
if (\Idno\Core\Version::build() < $vars['version']) {
    $label = 'label-danger';
    $details = $vars['version'] . " - ($details)";
} else {
    $label = 'label-success';
}    ?><span class="label <?php echo $label ?>"><?php echo $details ?></span>
