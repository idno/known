<?php
$plugin = trim($vars['plugin']);
$version = trim($vars['version']);

$loaded_plugin = \Idno\Core\site()->plugins->get($plugin); 
$details = \Idno\Core\site()->plugins->getStored()[$plugin]; 

$v_value = version_compare($details['Plugin description']['version'], strtolower($version));

if ($loaded_plugin) {
    $label = 'label-success';
} else {
    $label = 'label-important';
}

if ($version && $details && $v_value<0)
	$label = 'label-important';

?><span class="label <?= $label ?>"><?= $plugin ?><?php
    if ($details) {
	echo " v".$details['Plugin description']['version'];
    }
    
    if ($version && $v_value<0)
	echo " (requires v$version)";
    
    
?></span> 