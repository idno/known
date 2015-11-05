<div style="font-weight: bold; font-size: 30px; line-height: 32px; color: #333" align="center">
    Join us!
</div><br>
<hr/>
<br>
Hi there! Your friend <strong><?=$vars['inviter']?></strong> has invited you to join
<a href="<?=\Idno\Core\Idno::site()->config()->getDisplayURL()?>" style="color: #73b2e3; text-decoration: none;"><?=\Idno\Core\Idno::site()->config()->title?></a>.
<br><br>
You can use this space to publish your stories, share things that interest you, and discuss things that matter.
<br><br>
Get started with <strong><?=\Idno\Core\Idno::site()->config()->title?></strong> by setting up your account.
<br><br>
<div align="center">
<a href="<?=\Idno\Core\Idno::site()->config()->getDisplayURL()?>account/register/?email=<?=urlencode($vars['email'])?>&code=<?=urlencode($vars['code'])?>" style="background-color:#73B2E3;border:1px solid #73B2E3;border-radius:4px;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:17px;font-weight:normal;line-height:40px;text-align:center;text-decoration:none;width:200px;-webkit-text-size-adjust:none;mso-hide:all;">Sign up now</a>
</div>
<br>
If you have any questions at all, please don't hesitate to contact us by sending an email to
<a href="mailto:<?=\Idno\Core\Idno::site()->config()->from_email?>" style="color: #73b2e3; text-decoration: none;"><?=\Idno\Core\Idno::site()->config()->from_email?></a>.
<br><br>