<?php 
if (version_compare(\known\Core\site()->version(), strtolower($vars['version'])) < 0) {
    $label = 'label-important';
} else {
    $label = 'label-success';
}    ?><span class="label <?= $label ?>"><?= \known\Core\site()->version() ?> installed</span> 
