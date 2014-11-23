<?php

    header('Content-type: application/json');
    header("Access-Control-Allow-Origin: *");
    unset($vars['body']);
    
    echo json_encode($vars);