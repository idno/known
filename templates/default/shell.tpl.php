<?php

    $messages = $vars['messages'];

    header('Content-type: text/html');
    header('Link: <' . \Idno\Core\site()->config()->url . 'webmention/>; rel="http://webmention.org/"');
    header('Link: <' . \Idno\Core\site()->config()->url . 'webmention/>; rel="webmention"');
    header("Access-Control-Allow-Origin: *");

    if (empty($vars['title']) && !empty($vars['description'])) {
        $vars['title'] = implode(' ', array_slice(explode(' ', strip_tags($vars['description'])), 0, 10));
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
    
<!--Le fav and touch icons-->
<link rel="apple-touch-icon" sizes="57x57" href="<?=\Idno\Core\site()->config()->getURL()?>gfx/logos/apple-icon-57x57.png" />
<link rel="apple-touch-icon" sizes="72x72" href="<?=\Idno\Core\site()->config()->getURL()?>gfx/logos/apple-icon-72x72.png" />
<link rel="apple-touch-icon" sizes="114x114" href="<?=\Idno\Core\site()->config()->getURL()?>gfx/logos/apple-icon-114x114.png" />
<link rel="apple-touch-icon" sizes="144x144" href="<?=\Idno\Core\site()->config()->getURL()?>gfx/logos/apple-icon-144x144.png" />

    <?= $this->draw('shell/favicon'); ?>

    <?php
        $opengraph = [
            'og:type' => 'website',
            'og:title' => htmlspecialchars($vars['title']),
            'og:site_name' => \Idno\Core\site()->config()->title,
            'og:image' => \Idno\Core\site()->config()->getURL() . 'gfx/logos/logo_k.png'
	];

        if (\Idno\Core\site()->currentPage() && \Idno\Core\site()->currentPage()->isPermalink()) {

            $opengraph['og:url'] = \Idno\Core\site()->currentPage()->currentUrl();

            if (!empty($vars['object'])) {
                $owner = $vars['object']->getOwner();
                $object = $vars['object'];

                $opengraph['og:title'] = $vars['object']->getTitle();
                $opengraph['og:description'] = $vars['object']->getShortDescription();
                $opengraph['og:type'] = $vars['object']->getActivityStreamsObjectType();
                $opengraph['og:image'] = $owner->getIcon(); //Icon, for now set to being the author profile pic

                if ($url = $vars['object']->getURL()) {
                    $opengraph['og:url'] = $vars['object']->getURL();
                }
            }
            
        }
        
        foreach ($opengraph as $key => $value) 
            echo "<meta property=\"$key\" content=\"$value\" />\n";

    ?>

    <!-- Dublin Core -->
    <link rel="schema.DC" href="http://purl.org/dc/elements/1.1/">
    <meta name="DC.title" content="<?= htmlspecialchars($vars['title']) ?>">
    <meta name="DC.description" content="<?= htmlspecialchars($vars['description']) ?>"><?php

        if (\Idno\Core\site()->currentPage() && \Idno\Core\site()->currentPage()->isPermalink()) {
            /* @var \Idno\Common\Entity $object */
            if ($object instanceof \Idno\Common\Entity) {

                if ($creator = $object->getOwner()) {
                    ?>
                    <meta name="DC.creator" content="<?= htmlentities($creator->getTitle()) ?>"><?php
                }
                if ($created = $object->created) {
                    ?>
                    <meta name="DC.date" content="<?= date('c', $created) ?>"><?php
                }
                if ($url = $object->getURL()) {
                    ?>
                    <meta name="DC.identifier" content="<?= htmlspecialchars($url) ?>"><?php
                }

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
    <link rel="alternate feed" type="application/rss+xml"
          title="<?= htmlspecialchars(\Idno\Core\site()->config()->title) ?>: all content"
          href="<?= \Idno\Core\site()->config()->url ?>content/all?_t=rss"/>
    <link rel="feed" type="text/html" title="<?= htmlspecialchars(\Idno\Core\site()->config()->title) ?>"
          href="<?= \Idno\Core\site()->config()->url ?>content/all"/>

    <!-- Webmention endpoint -->
    <link href="<?= \Idno\Core\site()->config()->url ?>webmention/" rel="http://webmention.org/"/>
    <link href="<?= \Idno\Core\site()->config()->url ?>webmention/" rel="webmention"/>

    <link type="text/plain" rel="author" href="<?= \Idno\Core\site()->config()->url ?>humans.txt"/>
    <?php $this->draw('shell/identities') ?>
    <?php if (!empty(\Idno\Core\site()->config()->hub)) { ?>
        <!-- Pubsubhubbub -->
        <link href="<?= \Idno\Core\site()->config()->hub ?>" rel="hub"/>
    <?php } ?>

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

    <script src="<?= \Idno\Core\site()->config()->url ?>external/fragmention/fragmention.js"></script>

    <!-- We need jQuery at the top of the page -->
    <script src="<?= \Idno\Core\site()->config()->url . 'external/jquery/' ?>jquery.min.js"></script>

    <?= $this->draw('shell/head', $vars); ?>

</head>

<body class="<?php

    echo (str_replace('\\','_',strtolower(get_class(\Idno\Core\site()->currentPage()))));
    if ($path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)) {
        if ($path = explode('/',$path)) {
            $page_class = '';
            foreach($path as $element) {
                if (!empty($element)) {
                    if (!empty($page_class)) {
                        $page_class .= '-';
                    }
                    $page_class .= $element;
                    echo ' page-' . $page_class;
                }
            }
        }
    }

?>">
<?php endif; ?>
<div id="pjax-container" class="page-container">
    <?php
        $currentPage = \Idno\Core\site()->currentPage();

        if (!empty($currentPage)) {
            $hidenav = \Idno\Core\site()->embedded(); //\Idno\Core\site()->currentPage()->getInput('hidenav');
        }
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

    <div class="container page-body">

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

    <?= $this->draw('shell/contentfooter') ?>

</div>
<!-- Everything below this should be includes, not content -->

<?php if (!$_SERVER["HTTP_X_PJAX"]): ?>
<!-- Le javascript -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="<?= \Idno\Core\site()->config()->url . 'external/jquery-timeago/' ?>jquery.timeago.js"></script>
<script src="<?= \Idno\Core\site()->config()->url . 'external/jquery-pjax/' ?>jquery.pjax.js"></script>
<script src="<?= \Idno\Core\site()->config()->url . 'external/bootstrap/' ?>assets/js/bootstrap.min.js"></script>
<script src="<?= \Idno\Core\site()->config()->url . 'external/underscore/underscore-min.js' ?>"
        type="text/javascript"></script>
<script src="<?= \Idno\Core\site()->config()->url . 'external/mention/bootstrap-typeahead.js' ?>"
        type="text/javascript"></script>
<script src="<?= \Idno\Core\site()->config()->url . 'external/mention/mention.js' ?>" type="text/javascript"></script>

<!-- Flexible media player -->
<script
    src="<?= \Idno\Core\site()->config()->getURL() ?>external/mediaelement/build/mediaelement-and-player.min.js"></script>
<link rel="stylesheet"
      href="<?= \Idno\Core\site()->config()->getURL() ?>external/mediaelement/build/mediaelementplayer.css"/>

<!-- WYSIWYG editor -->
<link href="<?=\Idno\Core\site()->config()->getURL()?>external/summernote/dist/summernote.css" rel="stylesheet">
<script src="<?=\Idno\Core\site()->config()->getURL()?>external/summernote/dist/summernote.min.js"></script>

<!-- Mention styles -->
<link rel="stylesheet" type="text/css"
      href="<?= \Idno\Core\site()->config()->url ?>external/mention/recommended-styles.css">

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

    /**
     * Better handle links in iOS web applications.
     * This code (from the discussion here: https://gist.github.com/kylebarrow/1042026)
     * will prevent internal links being opened up in safari when known is installed
     * on an ios home screen.
     */
    (function (document, navigator, standalone) {
        if ((standalone in navigator) && navigator[standalone]) {
            var curnode, location = document.location, stop = /^(a|html)$/i;
            document.addEventListener('click', function (e) {
                curnode = e.target;
                while (!(stop).test(curnode.nodeName)) {
                    curnode = curnode.parentNode;
                }
                if ('href' in curnode && ( curnode.href.indexOf('http') || ~curnode.href.indexOf(location.host) ) && (!curnode.classList.contains('contentTypeButton'))) {
                    e.preventDefault();
                    location.href = curnode.href;
                }
            }, false);
        }
    })(document, window.navigator, 'standalone');

</script>

<?= $this->draw('shell/footer', $vars) ?>

</body>
</html>
<?php endif; ?>
