Forgot your password?
=====================

We heard you forgot your password. Don't worry. It happens to the best of us.

You can reset your password by clicking the link below (or copy and paste it into your browser).

<?php echo \Idno\Core\Idno::site()->config()->getDisplayURL() ?>account/password/reset/?email=<?php echo urlencode($vars['email']) ?>&code=<?php echo urlencode($vars['code'])
