<?php
$plugin = trim($vars['plugin']);
$version = trim($vars['version']);

$loaded_plugin = \Idno\Core\Idno::site()->plugins()->get($plugin);
$getstored = \Idno\Core\Idno::site()->plugins->getStored();
if(!empty($getstored[$plugin]))
    $details = $getstored[$plugin];

if (!empty($details['Plugin description']['version']))
    $v_value = version_compare($details['Plugin description']['version'], strtolower($version));
else
    $v_value = 0;

if ($loaded_plugin) {
    $label = 'label-success';
} else {
    $label = 'label-danger';
}

if ($version && !empty($details) && $v_value<0)
	$label = 'label-danger';

?><span class="label <?= $label ?>"><?= $plugin ?><?php
    if (!empty($details)) {
	echo " v".$details['Plugin description']['version'];
    }
    
    if (($version && $v_value<0) || ($version && empty($details)))
	echo " (v$version required)";
    
    
?></span> 