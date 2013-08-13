<?php

	$view = $_REQUEST['view'];
	if ($view == 'rss') {
		header('Location: http://benwerd.com/feed/');
		exit;
	}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head profile="http://gmpg.org/xfn/11">
	<link href='http://fonts.googleapis.com/css?family=Lato:300,400,700' rel='stylesheet' type='text/css'>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
	<title><?php bloginfo('name'); ?><?php wp_title(); ?></title>	
    <meta property="og:title" content="<?php wp_title(' '); ?>"/> 
	<meta property="og:site_name" content="<?php bloginfo('name'); ?>"/> 
<meta property="fb:admins" content="36802236"/> 
	<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" /> <!-- leave this for stats please -->
	<style type="text/css" media="screen">
		@import url( <?php bloginfo('stylesheet_url'); ?>?update=20120129 );
	</style>
	<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php bloginfo('rss2_url'); ?>" />
	<link rel="alternate" type="text/xml" title="RSS .92" href="<?php bloginfo('rss_url'); ?>" />
	<link rel="alternate" type="application/atom+xml" title="Atom 0.3" href="<?php bloginfo('atom_url'); ?>" />
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
    <?php wp_get_archives('type=monthly&format=link'); ?>
	<?php wp_head(); ?>
</head>

<body>
	<div id="toprap">
		<div id="headwrap">
			<div id="header">
				<p style="margin-bottom: 0px;">
                	<a href="<?php bloginfo('url'); ?>/feed/" rel="me"><img src="<?php echo get_bloginfo('template_url'); ?>/gfx/socmedicons/rss-32x32.png" alt="RSS" border="0" /></a>
                	<a href="http://twitter.com/benwerd/" rel="me"><img src="<?php echo get_bloginfo('template_url'); ?>/gfx/socmedicons/twitter-32x32.png" alt="@benwerd" border="0" /></a>
                    <a href="http://uk.linkedin.com/in/benwerd" rel="me"><img src="<?php echo get_bloginfo('template_url'); ?>/gfx/socmedicons/linkedin-32x32.png" alt="LinkedIn profile" border="0" /></a>
                    <a href="http://github.com/benwerd" rel="me"><img src="<?php echo get_bloginfo('template_url'); ?>/gfx/socmedicons/github-32x32.png" alt="GitHub profile" border="0" /></a>
                    <a href="http://plus.google.com/106119964731604142156?rel=author" rel="author"><img src="http://www.google.com/images/icons/ui/gprofile_button-32.png" width="32" height="32"></a>
                </p>

				<h1 >
					<a href="<?php bloginfo('url'); ?>/"><img src="<?php echo get_bloginfo('template_url'); ?>/gfx/benwerdmuller-2.png" alt="Ben Werdmuller" /></a>
				</h1>
			</div>
		</div> 
	</div>
	<div id="navrap">
					<div id="nav">
                        <ul>
                            <li><a href="/" rel="me">Blog</a></li>
                            <li><a href="/bio/" rel="me">About</a></li>
                            <li><a href="/writing/" rel="me">Highlighted posts</a></li>
                            <li><a href="/contact/" rel="me">Contact me</a></li>
							<li><a href="https://docs.google.com/spreadsheet/viewform?formkey=dFVhcnNiSnFwWVJEOFlic2N1VVBkMWc6MQ#gid=0" style="color: #ffaaaa">Profiled: a novel about privacy &amp; identity</a></li>
							<?php /* <li><a href="/friends/">Friends</a></li> */ ?>
                            <!-- <li><a href="http://projects.festivalslab.com/"  rel="co-worker">Festivals Lab</a></li> -->
                        </ul>
                    </div>
	</div>
	<div id="widerap">
	<div id="rap">	

		<div id="content">
        	<?php get_sidebar(); ?>
			<div id="mainContent">
		<!-- end header -->
