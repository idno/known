<div style="font-weight: bold; font-size: 30px; line-height: 32px; color: #333" align="center">
    <?php \Idno\Core\Idno::site()->language()->_('Join us!') ?>
</div><br>
<hr/>
<br>
<?php echo \Idno\Core\Idno::site()->language()->_('Hi there! Your friend <strong>%s</strong> has invited you to join <%s>', [$vars['inviter'], 'a href="' . \Idno\Core\Idno::site()->config()->getDisplayURL() . '" style="color: #73b2e3; text-decoration: none;">' . \Idno\Core\Idno::site()->config()->title . '</a']) ?>.
<br><br>
<?php echo \Idno\Core\Idno::site()->language()->_('You can use this space to publish your stories, share things that interest you, and discuss things that matter.') ?>
<br><br>
<?php echo \Idno\Core\Idno::site()->language()->_('Get started with <strong>%s</strong> by setting up your account', [\Idno\Core\Idno::site()->config()->title]) ?>.
<br><br>
<div align="center">
<a href="<?php echo \Idno\Core\Idno::site()->config()->getDisplayURL()?>account/register/?email=<?php echo urlencode($vars['email'])?>&code=<?php echo urlencode($vars['code'])?>" style="background-color:#73B2E3;border:1px solid #73B2E3;border-radius:4px;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:17px;font-weight:normal;line-height:40px;text-align:center;text-decoration:none;width:200px;-webkit-text-size-adjust:none;mso-hide:all;"><?php echo \Idno\Core\Idno::site()->language()->_('Sign up now') ?></a>
</div>
<br>
<?php echo \Idno\Core\Idno::site()->language()->_('If you have any questions at all, please don\'t hesitate to contact us by sending an email to %s',
[
'<a href="mailto:' . \Idno\Core\Idno::site()->config()->from_email . '" style="color: #73b2e3; text-decoration: none;">' . \Idno\Core\Idno::site()->config()->from_email . '</a>'
]
) ?>.

<br><br>
