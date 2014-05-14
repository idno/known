<?php

    header('Content-type: application/json');
    unset($vars['body']);
    $vars['messages'] = \known\Core\site()->session()->getAndFlushMessages();
    echo json_encode($vars);