<?php
// Note: if installing with composer you should require 'vendor/autoload.php' instead
include('src/IndieWeb/MentionClient.php');

$url = 'https://github.com/aaronpk/mention-client';
$client = new IndieWeb\MentionClient($url);
$client->debug = true;
$sent = $client->sendSupportedMentions();

echo "Sent $sent mentions\n";
