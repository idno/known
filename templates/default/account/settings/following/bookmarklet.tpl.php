<?php

?><a href="javascript:(function(){location.href='<?= \Idno\Core\Idno::site()->config()->getDisplayURL(); ?>account/settings/following/bookmarklet?u='+encodeURIComponent(location.href);})();" class="btn"><?= \Idno\Core\Idno::site()->language()->_('Add as friend...'); ?></a>