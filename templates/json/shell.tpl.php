<?php

    header('Content-type: application/json');
    header("Access-Control-Allow-Origin: *");
    header('Access-Control-Allow-Credentials: true');
    header("Access-Control-Allow-Headers: Authorization, Content-Type");

    unset($vars['body']);

if (!empty($vars['exception'])) {
    $e = [
        'class' => get_class($vars['exception']),
        'message' => $vars['exception']->getMessage(),
        'file' => $vars['exception']->getFile(),
        'line' => $vars['exception']->getLine()
    ];
    $vars['exception'] = $e;
}

    echo json_encode($vars, JSON_PRETTY_PRINT);
