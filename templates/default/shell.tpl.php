<?php

    header('Content-type: text/html');
    header('Link: <'.\Idno\Core\site()->config()->url.'webmention/>; rel="http://webmention.org/"')

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($vars['title']); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?=htmlspecialchars($vars['description'])?>">
    <meta name="generator" content="idno http://idno.co">

    <!-- Le styles -->
    <link href="<?= \Idno\Core\site()->config()->url . 'external/bootstrap/' ?>assets/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="<?=\Idno\Core\site()->config()->url?>external/font-awesome/css/font-awesome.min.css">
    <style>
        body {
            padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
        }
    </style>
    <link href="<?= \Idno\Core\site()->config()->url . 'external/bootstrap/' ?>assets/css/bootstrap-responsive.css" rel="stylesheet">
    <link href="<?= \Idno\Core\site()->config()->url ?>css/default.css" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="<?= \Idno\Core\site()->config()->url . 'external/bootstrap/' ?>assets/js/html5shiv.js"></script>
    <![endif]-->

    <!-- Default idno JavaScript -->
    <script src="<?=\Idno\Core\site()->config()->url . 'js/default.js'?>"></script>

    <!-- To silo is human, to syndicate divine -->
    <link rel="alternate" type="application/rss+xml" title="<?=htmlspecialchars($vars['title'])?>" href="<?=$this->getCurrentURLWithVar('_t','rss');?>" />

    <!-- Webmention endpoint -->
    <link href="<?=\Idno\Core\site()->config()->url?>webmention/" rel="http://webmention.org/" />

    <?=$this->draw('shell/head',$vars);?>

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
            <form class="navbar-search pull-left" action="/search/" method="get">
                <input type="text" class="search-query" name="q" placeholder="Search" value="<?php

                    if ($q = \Idno\Core\site()->currentPage()->getInput('q')) {
                        echo htmlspecialchars($q);
                    }

                ?>">
            </form>
            <div class="nav-collapse collapse">
                <ul class="nav" role="menu">
                </ul>
                <?=$this->draw('shell/toolbar/content')?>
                <ul class="nav pull-right" role="menu">
                    <?php

                        if (\Idno\Core\site()->session()->isLoggedIn()) {

                            echo $this->draw('shell/toolbar/logged-in');

                        } else {

                            echo $this->draw('shell/toolbar/logged-out');

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
    <?=$this->draw('shell/beforecontent')?>
    <?= $vars['body'] ?>
    <?=$this->draw('shell/aftercontent')?>

</div> <!-- /container -->

<!-- Le javascript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="<?= \Idno\Core\site()->config()->url . 'external/jquery/' ?>jquery.min.js"></script>
<script src="<?= \Idno\Core\site()->config()->url . 'external/bootstrap/' ?>assets/js/bootstrap.min.js"></script>
<!-- Sisyphus for localStorage forms support -->
<script src="<?= \Idno\Core\site()->config()->url . 'external/sisyphus/' ?>sisyphus.min.js"></script>
<!-- Video shim -->
<script src="<?=\Idno\Core\site()->config()->url . 'external/fitvids/jquery.fitvids.min.js'?>"></script>
<script>
    $('form').sisyphus({
        locationBased: true
    });
    $(document).ready(function(){
        $(".h-entry").fitVids();
    });
</script>

<?=$this->draw('shell/footer',$vars)?>

</body>
</html>