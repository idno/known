Join us!
=======

Hi there! Your friend *<?=$vars['inviter']?>* has invited you to join <?=\Idno\Core\site()->config()->title?>: <?=\Idno\Core\site()->config()->getDisplayURL()?>.

You can use this space to publish your stories, share things that interest you, and discuss things that matter.

Get started with *<?=\Idno\Core\site()->config()->title?>* by setting up your account.

Sign up now: <?=\Idno\Core\site()->config()->getDisplayURL()?>account/register/?email=<?=urlencode($vars['email'])?>&code=<?=urlencode($vars['code'])?>

If you have any questions at all, please don't hesitate to contact us by sending an email to <?=\Idno\Core\site()->config()->from_email?>
