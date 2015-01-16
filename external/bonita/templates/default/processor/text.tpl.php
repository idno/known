<?php

	// Default text processor.
	
	// Really we should be using something like WordPress's wpautop() 
	// here - but what are the licensing restrictions?

	echo nl2br($vars['content']);

?>