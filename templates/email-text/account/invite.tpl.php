<?php $joinus = \Idno\Core\Idno::site()->language()->_('Join us!');

    echo $joinus . "\n";

    $underline = mb_strlen($joinus);

    for($u = 1; $u < $underline; $u++) {
        echo '=';
    }

 ?>


<?php echo \Idno\Core\Idno::site()->language()->_('Hi there! Your friend *%s* has invited you to join %s', [$vars['inviter'],\Idno\Core\Idno::site()->config()->title]) ?>: <?php echo \Idno\Core\Idno::site()->config()->getDisplayURL()?>.


<?php echo \Idno\Core\Idno::site()->language()->_('You can use this space to publish your stories, share things that interest you, and discuss things that matter.') ?>


<?php echo \Idno\Core\Idno::site()->language()->_('Get started with *%s* by setting up your account', [\Idno\Core\Idno::site()->config()->title]) ?>.


<?php echo \Idno\Core\Idno::site()->language()->_('Sign up now') ?>: <?php echo \Idno\Core\Idno::site()->config()->getDisplayURL()?>account/register/?email=<?php echo urlencode($vars['email'])?>&code=<?php echo urlencode($vars['code'])?>


<?php echo \Idno\Core\Idno::site()->language()->_('If you have any questions at all, please don\'t hesitate to contact us by sending an email to %s', [\Idno\Core\Idno::site()->config()->from_email]);
