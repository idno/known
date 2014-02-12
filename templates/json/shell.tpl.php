<?php

    header('Content-type: text/json');
    unset($vars['body']);
    $vars['messages'] = \Idno\Core\site()->session()->getAndFlushMessages();
    echo json_encode($vars);