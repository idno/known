<h1>
    Reset your password
</h1>
<p>
    To reset your password, click on the following link:
</p>
<p style="text-align: center; font-size: 1.2em">
    <a href="<?=\Idno\Core\site()->config()->getURL()?>account/password/reset/?email=<?=urlencode($vars['email'])?>&code=<?=urlencode($vars['code']?>">Click here to reset your password</a>
</p>