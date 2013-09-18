<?php

    header('Content-type: text/html');
    header('Link: <' . \Idno\Core\site()->config()->url . 'webmention/>; rel="http://webmention.org/"')

?>
<?php if (!$_SERVER["HTTP_X_PJAX"]): ?>
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($vars['title']); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars($vars['description']) ?>">
    <meta name="generator" content="idno http://idno.co">
    <?= $this->draw('shell/favicon'); ?>

    <!-- Le styles -->
    <link href="<?= \Idno\Core\site()->config()->url . 'external/bootstrap/' ?>assets/css/bootstrap.css"
          rel="stylesheet">
    <link rel="stylesheet" href="<?= \Idno\Core\site()->config()->url ?>external/font-awesome/css/font-awesome.min.css">
    <style>
        body {
            padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
        }
    </style>
    <link href="<?= \Idno\Core\site()->config()->url . 'external/bootstrap/' ?>assets/css/bootstrap-responsive.css"
          rel="stylesheet">
    <link href="<?= \Idno\Core\site()->config()->url ?>css/default.css" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="<?= \Idno\Core\site()->config()->url . 'external/bootstrap/' ?>assets/js/html5shiv.js"></script>
    <![endif]-->

    <!-- Default idno JavaScript -->
    <script src="<?= \Idno\Core\site()->config()->url . 'js/default.js' ?>"></script>

    <!-- To silo is human, to syndicate divine -->
    <link rel="alternate" type="application/rss+xml" title="<?= htmlspecialchars($vars['title']) ?>"
          href="<?= $this->getCurrentURLWithVar('_t', 'rss'); ?>"/>

    <!-- Webmention endpoint -->
    <link href="<?= \Idno\Core\site()->config()->url ?>webmention/" rel="http://webmention.org/"/>

    <?= $this->draw('shell/head', $vars); ?>

</head>

<body>
<?php endif; ?>
<div id="pjax-container">
    <?php

        $hidenav = \Idno\Core\site()->currentPage()->getInput('hidenav');
        if (empty($vars['hidenav']) && empty($hidenav)) {
            ?>
            <div class="navbar navbar-inverse navbar-fixed-top">
                <div class="navbar-inner">
                    <div class="container">
                        <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="brand"
                           href="<?= \Idno\Core\site()->config()->url ?>"><?= \Idno\Core\site()->config()->title ?></a>

                        <div class="nav-collapse collapse">
                            <form class="navbar-search pull-left" action="/search/" method="get">
                                <input type="text" class="search-query" name="q" placeholder="Search" value="<?php

                                    if ($q = \Idno\Core\site()->currentPage()->getInput('q')) {
                                        echo htmlspecialchars($q);
                                    }

                                ?>">
                            </form>
                            <ul class="nav" role="menu">
                            </ul>
                            <?= $this->draw('shell/toolbar/content') ?>
                            <ul class="nav pull-right" role="menu">
                                <?php

                                    if (\Idno\Core\site()->session()->isLoggedIn()) {

                                        echo $this->draw('shell/toolbar/logged-in');

                                    } else {

                                        echo $this->draw('shell/toolbar/logged-out');

                                    }

                                ?>
                            </ul>
                        </div>
                        <!--/.nav-collapse -->
                    </div>
                </div>
            </div>

        <?php
        } else {

            ?>
            <div style="height: 1em;"><br/></div>
        <?php

        } // End hidenav test
    ?>

    <div class="container">

        <?php

            if ($messages = \Idno\Core\site()->session()->getAndFlushMessages()) {
                foreach ($messages as $message) {

                    ?>

                    <div class="alert <?= $message['message_type'] ?>">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <?= $message['message'] ?>
                    </div>

                <?php

                }
            }

        ?>
        <?= $this->draw('shell/beforecontent') ?>
        <?= $vars['body'] ?>
        <?= $this->draw('shell/aftercontent') ?>

    </div>
    <!-- /container -->
</div>
<!-- pjax-container -->
<?php if (!$_SERVER["HTTP_X_PJAX"]): ?>
<!-- Le javascript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="<?= \Idno\Core\site()->config()->url . 'external/jquery/' ?>jquery.min.js"></script>
<script src="<?= \Idno\Core\site()->config()->url . 'external/jquery-timeago/' ?>jquery.timeago.js"></script>
<script src="<?= \Idno\Core\site()->config()->url . 'external/jquery-pjax/' ?>jquery.pjax.js"></script>
<script src="<?= \Idno\Core\site()->config()->url . 'external/bootstrap/' ?>assets/js/bootstrap.min.js"></script>
<!-- Video shim -->
<script src="<?= \Idno\Core\site()->config()->url . 'external/fitvids/jquery.fitvids.min.js' ?>"></script>
<script>

    //$(document).pjax('a:not([href^=\\.],[href^=file])', '#pjax-container');    // In idno, URLs with extensions are probably files.
    /*$(document).on('pjax:click', function(event) {
     if (event.target.href.match('/edit/')) {
     // For a reason I can't actuallly figure out, /edit pages never render with chrome
     // when PJAXed. I don't understand the rendering pipeline well enough to figure out
     // what's up --jrv 20130705
     return false;
     }
     if (event.target.onclick) { // If there's an onclick handler, we don't want to pjax this
     return false;
     } else {
     return true;
     }
     });*/

    function annotateContent() {
        $(".h-entry").fitVids();
        $("time.dt-published").timeago();

    }
    $(document).ready(function () {
        annotateContent();
    })

    $(document).on('pjax:complete', function () {
        annotateContent();
    });

</script>

<?= $this->draw('shell/footer', $vars) ?>

</body>
</html>
<?php endif; ?>
