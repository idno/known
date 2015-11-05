Forgot your password?
=====================

We heard you forgot your password. Don't worry. It happens to the best of us.

You can reset your password by clicking the link below (or copy and paste it into your browser).

<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>account/password/reset/?email=<?= urlencode($vars['email']) ?>&code=<?= urlencode($vars['code']) ?>