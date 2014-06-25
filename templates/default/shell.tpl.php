<?php

    $messages = $vars['messages'];

    header('Content-type: text/html');
    header('Link: <' . \Idno\Core\site()->config()->url . 'webmention/>; rel="http://webmention.org/"');
    header('Link: <' . \Idno\Core\site()->config()->url . 'webmention/>; rel="webmention"');
    header("Access-Control-Allow-Origin: *");

    if (empty($vars['title']) && !empty($vars['description'])) {
        $vars['title'] = implode(' ',array_slice(explode(' ', strip_tags($vars['description'])),0,10));
    }

?>
<?php if (!$_SERVER["HTTP_X_PJAX"]): ?>
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($vars['title']); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="initial-scale=1.0" media="(device-height: 568px)"/>
    <meta name="description" content="<?= htmlspecialchars(strip_tags($vars['description'])) ?>">
    <meta name="generator" content="Known http://withknown.com">
    <?= $this->draw('shell/favicon'); ?>

    <!-- Dublin Core -->
    <link rel="schema.DC" href="http://purl.org/dc/elements/1.1/" >
    <meta name="DC.title" content="<?=htmlspecialchars($vars['title'])?>" >
    <meta name="DC.description" content="<?=htmlspecialchars($vars['description'])?>" ><?php

        if (\Idno\Core\site()->currentPage()->isPermalink()) {
            $object = $vars['object']; /* @var \Idno\Common\Entity $object */
            if ($creator = $object->getOwner()) {
                ?><meta name="DC.creator" content="<?=htmlentities($creator->getTitle())?>"><?php
            }
            if ($created = $object->created) {
                ?><meta name="DC.date" content="<?=date('c',$created)?>"><?php
            }
            if ($url = $object->getURL()) {
                ?><meta name="DC.identifier" content="<?=htmlspecialchars($url)?>"><?php
            }
        }

    ?>

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

    <!-- Default Known JavaScript -->
    <script src="<?= \Idno\Core\site()->config()->url . 'js/default.js' ?>"></script>

    <!-- To silo is human, to syndicate divine -->
    <link rel="alternate feed" type="application/rss+xml" title="<?= htmlspecialchars($vars['title']) ?>"
          href="<?= $this->getURLWithVar('_t', 'rss'); ?>"/>
    <link rel="alternate feed" type="application/rss+xml" title="<?= htmlspecialchars(\Idno\Core\site()->config()->title) ?>: all content"
          href="<?= \Idno\Core\site()->config()->url ?>content/all?_t=rss"/>
    <link rel="feed" type="text/html" title="<?= htmlspecialchars(\Idno\Core\site()->config()->title) ?>"
          href="<?= \Idno\Core\site()->config()->url ?>content/all"/>

    <!-- Webmention endpoint -->
    <link href="<?= \Idno\Core\site()->config()->url ?>webmention/" rel="http://webmention.org/"/>
    <link href="<?= \Idno\Core\site()->config()->url ?>webmention/" rel="webmention"/>

    <link type="text/plain" rel="author" href="<?= \Idno\Core\site()->config()->url ?>humans.txt"/>

    <?php
        // Load style assets
        if ((\Idno\Core\site()->currentPage()) && $style = \Idno\Core\site()->currentPage->getAssets('css')) {
            foreach ($style as $css) {
                ?>
                <link href="<?= $css; ?>" rel="stylesheet">
            <?php
            }
        }
    ?>

    <script src="<?=\Idno\Core\site()->config()->url?>external/fragmention/fragmention.js"></script>

    <!-- We need jQuery at the top of the page -->
    <script src="<?= \Idno\Core\site()->config()->url . 'external/jquery/' ?>jquery.min.js"></script>

    <?= $this->draw('shell/head', $vars); ?>

</head>

<body>
<?php endif; ?>
<div id="pjax-container">
    <?php
        $currentPage = \Idno\Core\site()->currentPage();

        if (!empty($currentPage))
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
                            <?php
                                if (\Idno\Core\site()->config()->isPublicSite() || \Idno\Core\site()->session()->isLoggedOn()) {
                                    echo $this->draw('shell/toolbar/search');
                                    echo $this->draw('shell/toolbar/content');
                                }
                            ?>
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

            if (!empty($messages)) {
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
<!-- Le javascript -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="<?= \Idno\Core\site()->config()->url . 'external/jquery-timeago/' ?>jquery.timeago.js"></script>
<script src="<?= \Idno\Core\site()->config()->url . 'external/jquery-pjax/' ?>jquery.pjax.js"></script>
<script src="<?= \Idno\Core\site()->config()->url . 'external/bootstrap/' ?>assets/js/bootstrap.min.js"></script>
<script src="<?= \Idno\Core\site()->config()->url . 'external/underscore/underscore-min.js' ?>" type="text/javascript"></script>
<script src="<?= \Idno\Core\site()->config()->url . 'external/mention/bootstrap-typeahead.js' ?>" type="text/javascript"></script>
<script src="<?= \Idno\Core\site()->config()->url . 'external/mention/mention.js' ?>" type="text/javascript"></script>

<!-- Flexible media player -->
<script src="<?=\Idno\Core\site()->config()->getURL()?>external/mediaelement/build/mediaelement-and-player.min.js"></script>
<link rel="stylesheet" href="<?=\Idno\Core\site()->config()->getURL()?>external/mediaelement/build/mediaelementplayer.css" />

<!-- Mention styles -->
<link rel="stylesheet" type="text/css" href="<?= \Idno\Core\site()->config()->url ?>external/mention/recommended-styles.css">

<?php
    if (\Idno\Core\site()->session()->isLoggedOn()) {
        echo $this->draw('js/mentions');
    }
?>

<!-- Video shim -->
<script src="<?= \Idno\Core\site()->config()->url . 'external/fitvids/jquery.fitvids.min.js' ?>"></script>

<?php
    // Load javascript assets
    if ((\Idno\Core\site()->currentPage()) && $scripts = \Idno\Core\site()->currentPage->getAssets('javascript')) {
        foreach ($scripts as $script) {
            ?>
            <script src="<?= $script ?>"></script>
        <?php
        }
    }
?>

<!-- HTML5 form element support for legacy browsers -->
<script src="<?= \Idno\Core\site()->config()->url . 'external/h5f/h5f.min.js' ?>"></script>

<script>

    function annotateContent() {
        $(".h-entry").fitVids();
        $("time.dt-published").timeago();
    }

    // Shim so that JS functions can get the current site URL
    function wwwroot() {
        return '<?=\Idno\Core\site()->config()->getURL()?>';
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
