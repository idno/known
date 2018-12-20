<?php

// Note: if installing with composer you should require 'vendor/autoload.php' instead
include('src/IndieWeb/MentionClient.php');

$url = 'https://github.com/aaronpk/mention-client';
$client = new IndieWeb\MentionClient();
$client->enableDebug();
$sent = $client->sendMentions($url);

echo "Sent $sent mentions\n";
