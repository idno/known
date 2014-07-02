<?php

    header('Content-type: text/plain');
    header("Access-Control-Allow-Origin: *");
    unset($vars['body']);
    $structure = json_decode(json_encode($vars));

    function such_explain($structure) {

        if (is_array($structure)) {
            if (!empty($structure)) {
                echo "so "; //. '"' . addslashes($name) . '" ';
                $i = 0;
                foreach($structure as $key => $value) {
                    if ($i > 0) {
                        if (rand(0,1)) {
                            echo "also ";
                        } else {
                            echo "and ";
                        }
                    }
                    echo '"' . addslashes($key) . '" is ';
                    echo such_explain($value);
                    $i++;
                }
                echo "many ";
            } else {
                echo "nullish ";
            }
        } else if (is_object($structure)) {
            if ($properties = get_object_vars($structure)) {
                if (!empty($properties)) {
                    echo "such ";
                    $i = 0;
                    foreach($properties as $key => $value) {
                        if ($i > 0) {
                            switch(rand(0,3)) {
                                case 0: echo ", "; break;
                                case 1: echo ". "; break;
                                case 2: echo "! "; break;
                                case 3: echo "? "; break;
                            }
                        }
                        echo '"' . addslashes($key) . '" is ';
                        echo such_explain($value);
                        $i++;
                    }
                }
                echo "wow ";
            } else {
                echo "nullish ";
            }
        } else {
            switch(gettype($structure)) {
                case 'boolean':
                    if ($structure) {
                        echo 'yes ';
                    } else {
                        echo 'no ';
                    }
                    break;
                case 'NULL':
                    echo 'empty ';
                    break;
                default:
                    //echo '"'.addslashes($structure).'" ';
                    $var = json_encode($structure);
                    /*$var = str_replace("\n"," so freighten ",$var);
                    $var = str_replace("\\n"," so freighten ",$var);
                    $var = str_replace("\""," what is? ",$var);
                    $var = str_replace("\\\""," what is? ",$var);
                    $var = str_replace("\\/"," very scare. ",$var);
                    $var = str_replace("\b"," warn. ",$var);
                    $var = str_replace("\\b"," warn. ",$var);
                    $var = str_replace("\f"," much run. ",$var);
                    $var = str_replace("\\f"," much run. ",$var);
                    $var = str_replace("\r"," stay. ",$var);
                    $var = str_replace("\\r"," stay. ",$var);
                    $var = str_replace("\t"," be brave shibe. ",$var);
                    $var = str_replace("\\/"," very scare. ",$var);
                    $var = str_replace("\\"," don't know ",$var);
                    $var = str_replace("  "," ",$var);*/
                    echo '"' . trim($var) . '" ';
                    break;
            }
        }

    }

    such_explain($structure, $name);

    echo "wow";