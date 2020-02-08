<?php $joinus = \Idno\Core\Idno::site()->language()->_('Forgot your password?');

    echo $joinus . "\n";

    $underline = mb_strlen($joinus);

    for($u = 0; $u < $underline; $u++) {
        echo '=';
    }

 ?>


<?php echo \Idno\Core\Idno::site()->language()->_('We heard you forgot your password. Don\'t worry. It happens to the best of us.') ?>


<?php echo \Idno\Core\Idno::site()->language()->_('You can reset your password by clicking the link below (or copy and paste it into your browser).') ?>


<?php echo \Idno\Core\Idno::site()->config()->getDisplayURL() ?>account/password/reset/?email=<?php echo urlencode($vars['email']) ?>&code=<?php echo urlencode($vars['code']);
