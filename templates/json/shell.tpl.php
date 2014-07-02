<?php

    header('Content-type: application/json');
    header("Access-Control-Allow-Origin: *");
    unset($vars['body']);
    $vars['messages'] = \Idno\Core\site()->session()->getAndFlushMessages();
    echo json_encode($vars);