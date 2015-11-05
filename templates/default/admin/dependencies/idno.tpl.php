<?php 
if (version_compare(\Idno\Core\Idno::site()->version(), strtolower($vars['version'])) < 0) {
    $label = 'label-danger';
} else {
    $label = 'label-success';
}    ?><span class="label <?= $label ?>"><?= \Idno\Core\Idno::site()->version() ?> installed</span>
