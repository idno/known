<?php

	require_once('ogp/Parser.php');

	$content = file_get_contents("https://www.youtube.com/watch?v=EIGGsZZWzZA");
	
	print_r(\ogp\Parser::parse($content));
