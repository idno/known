<!doctype html>
<?php

    $title = 'Database error';
    $heading = 'Oh no! We couldn\'t connect to the database.';
    if (empty($message)) 
        $message = "";
    $body = "
        <p>
            This probably means that the database settings changed, this Known site hasn't been set up yet, or
            there's a database problem.
        </p>
        $message
        
    ";
    $helplink = "
        <a href=\"http://docs.withknown.com\">See the Known documentation for help.</a>
    ";
        
   require_once(dirname(__FILE__) . '/error-page.php');
    