<!DOCTYPE html>
<html lang="en">
    <head>
	<meta charset="utf-8">
	<title><?= $vars['title'] ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="generator" content="idno http://idno.co">

	<!-- Le styles -->
	<link href="<?= \Idno\Core\site()->config()->url . 'external/bootstrap/' ?>assets/css/bootstrap.css" rel="stylesheet">
	<style>
	    body {
		padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
	    }
	</style>
	<link href="<?= \Idno\Core\site()->config()->url . 'external/bootstrap/' ?>assets/css/bootstrap-responsive.css" rel="stylesheet">

	<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
	<!--[if lt IE 9]>
	  <script src="<?= \Idno\Core\site()->config()->url . 'external/bootstrap/' ?>assets/js/html5shiv.js"></script>
	<![endif]-->

    </head>

    <body>

	<div class="navbar navbar-inverse navbar-fixed-top">
	    <div class="navbar-inner">
		<div class="container">
		    <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		    </button>
		    <a class="brand" href="<?= \Idno\Core\site()->config()->url ?>"><?=  \Idno\Core\site()->config()->title?></a>
		    <div class="nav-collapse collapse">
			<ul class="nav" role="menu">
			</ul>
			<ul class="nav pull-right" role="menu">
<?php

    if (\Idno\Core\site()->session()->isLoggedIn()) {

?>
			    <li><a href="/account/settings">Settings</a></li>
			    <li><?=  \Idno\Core\site()->actions()->createLink('/session/logout', 'Sign out');?></li>
<?php

    } else {
	
?>
			    <li><a href="/session/login">Sign in</a></li>
			    <li><a href="/account/register">Register</a></li>
<?php
	
    }

?>  
			</ul>
		    </div><!--/.nav-collapse -->
		</div>
	    </div>
	</div>

	<div class="container">

	    <?php
	    
		if ($messages = \Idno\Core\site()->session()->getAndFlushMessages()) {
		    foreach($messages as $message) {
			
	    ?>
	    
	    <div class="alert <?=$message['message_type']?>">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<?=$message['message']?>
	    </div>
	    
	    <?php
			
		    }
		}
	    
	    ?>
	    <?= $vars['body'] ?>

	</div> <!-- /container -->
	
	<!-- Le javascript
	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
	<script src="<?= \Idno\Core\site()->config()->url . 'external/jquery/' ?>jquery.min.js"></script>
	<script src="<?= \Idno\Core\site()->config()->url . 'external/bootstrap/' ?>assets/js/bootstrap.min.js"></script>

    </body>
</html>