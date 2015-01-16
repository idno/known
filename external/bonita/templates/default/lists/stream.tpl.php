<?php

	if (is_array($vars['items']) && !empty($vars['items']))
		foreach($vars['item'] as $item)
			$t->drawObject($item);