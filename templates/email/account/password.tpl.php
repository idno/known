<div style="font-weight: bold; font-size: 30px; line-height: 32px; color: #333" align="center">
    Forgot your password?
</div><br>
<hr/>
<br>
We heard you forgot your password. Don't worry. It happens to the best of us.
<br><br>
You can reset your password by clicking the link below (or copy and paste it into your browser).
<br><br>

<a href="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>account/password/reset/?email=<?= urlencode($vars['email']) ?>&code=<?= urlencode($vars['code']) ?>" style="color: #73b2e3; text-decoration: none;"><?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>account/password/reset/?email=<?= urlencode($vars['email']) ?>&code=<?= urlencode($vars['code']) ?></a>
<br><br>