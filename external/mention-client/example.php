<?php
include('mention-client.php');

$url = 'https://github.com/aaronpk/mention-client';
$client = new MentionClient($url);
$client->debug = true;
$sent = $client->sendSupportedMentions();

echo "Sent $sent mentions\n";
