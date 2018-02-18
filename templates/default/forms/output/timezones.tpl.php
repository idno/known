<?php 

$offset = \Idno\Core\Time::timezoneToGMTOffset($vars['value']);


?><span class="timezone" data-timezone="<?= $vars['value']; ?>" data-timezone-offset="<?= $offset; ?>"><?= $vars['value']; ?> 
    
    <?php if (\Idno\Core\Idno::site()->session()->isLoggedIn()) { ?>
        <?php
        $diff = \Idno\Core\Time::printTimezoneDiff(\Idno\Core\Time::timezoneDiff($vars['value'], \Idno\Core\Idno::site()->session()->currentUser()->getTimezone()));
        ?>
        <?php if ($offset!=0 && $diff) { ?>(<?= $diff; ?>)<?php } ?>
    <?php } else { ?>
    <?php if ($offset!=0) { ?>(GMT <?php echo \Idno\Core\Time::printTimezoneOffset($offset); ?>) <?php } ?>
    <?php } ?>
</span>