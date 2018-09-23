<?php

?><a href="javascript:(function(){location.href='<?php echo \Idno\Core\Idno::site()->config()->getDisplayURL(); ?>account/settings/following/bookmarklet?u='+encodeURIComponent(location.href);})();" class="btn"><?php echo \Idno\Core\Idno::site()->language()->_('Add as friend...'); ?></a>