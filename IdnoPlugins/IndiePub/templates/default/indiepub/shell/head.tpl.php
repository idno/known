<link rel="openid.delegate" href="<?php echo \Idno\Core\Idno::site()->config()->getURL()?>" />
<link rel="openid.server" href="https://indieauth.com/openid" />
<link rel="authorization_endpoint" href="<?php echo \Idno\Core\Idno::site()->config()->getURL()?>indieauth/auth">
<link rel="token_endpoint" href="<?php echo \Idno\Core\Idno::site()->config()->getURL()?>indieauth/token">
<link rel="micropub" href="<?php echo \Idno\Core\Idno::site()->config()->getURL()?>micropub/endpoint">