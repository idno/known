Join us!
=======

Hi there! Your friend *<?php echo $vars['inviter']?>* has invited you to join <?php echo \Idno\Core\Idno::site()->config()->title?>: <?php echo \Idno\Core\Idno::site()->config()->getDisplayURL()?>.

You can use this space to publish your stories, share things that interest you, and discuss things that matter.

Get started with *<?php echo \Idno\Core\Idno::site()->config()->title?>* by setting up your account.

Sign up now: <?php echo \Idno\Core\Idno::site()->config()->getDisplayURL()?>account/register/?email=<?php echo urlencode($vars['email'])?>&code=<?php echo urlencode($vars['code'])?>

If you have any questions at all, please don't hesitate to contact us by sending an email to <?php echo \Idno\Core\Idno::site()->config()->from_email;
