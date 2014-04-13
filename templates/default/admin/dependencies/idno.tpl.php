<?php 
if (version_compare(\Idno\Core\site()->version(), strtolower($vars['version'])) < 0) {
    $label = 'label-important';
} else {
    $label = 'label-success';
}    ?><span class="label <?= $label ?>"><?= \Idno\Core\site()->version() ?> installed</span> 
