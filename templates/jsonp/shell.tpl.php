<?php

    header('Content-type: application/x-javascript; charset=UTF-8');
    header("Access-Control-Allow-Origin: *");
    
    unset($vars['body']);

    if (!($callback = \Idno\Core\Idno::site()->currentPage()->getInput('callback'))) {
        if (!($callback = \Idno\Core\Idno::site()->currentPage()->getInput('jsonp'))) {
            $callback = 'response';
        }
    }

    if (!empty($vars['exception'])) {
        $e = [
            'class' => get_class($vars['exception']),
            'message' => $vars['exception']->getMessage(),
            'file' => $vars['exception']->getFile(),
            'line' => $vars['exception']->getLine()
        ];
        $vars['exception'] = $e;
    }

    echo $callback . "(".json_encode($vars).")";