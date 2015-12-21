<link rel="openid.delegate" href="<?=\Idno\Core\Idno::site()->config()->getURL()?>" />
<link rel="openid.server" href="https://indieauth.com/openid" />
<link rel="authorization_endpoint" href="<?=\Idno\Core\Idno::site()->config()->getURL()?>indieauth/auth">
<link rel="token_endpoint" href="<?=\Idno\Core\Idno::site()->config()->getURL()?>indieauth/token">
<link rel="micropub" href="<?=\Idno\Core\Idno::site()->config()->getURL()?>micropub/endpoint">