<?php

    header('Content-type: text/html');
    header('Link: <' . \Idno\Core\Idno::site()->config()->getDisplayURL() . 'webmention/>; rel="http://webmention.org/"');
    header('Link: <' . \Idno\Core\Idno::site()->config()->getDisplayURL() . 'webmention/>; rel="webmention"');
    header("Access-Control-Allow-Origin: *");

    if (empty($vars['title'])) {
        if (!empty($vars['description'])) {
            $vars['title'] = implode(' ', array_slice(explode(' ', strip_tags($vars['description'])), 0, 10));
        } else {
            $vars['title'] = '';
        }
    }

    if (empty($vars['description'])) {
        $vars['description'] = '';
    }

?><!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <title><?= htmlspecialchars($vars['title']); ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="viewport" content="initial-scale=1.0" media="(device-height: 568px)"/>
        <meta name="description" content="<?= htmlspecialchars(strip_tags($vars['description'])) ?>">
        <meta name="generator" content="Known https://withknown.com">
        <?= $this->draw('shell/favicon'); ?>
        <link rel="schema.DC" href="http://purl.org/dc/elements/1.1/">
        <meta name="DC.title" content="<?= htmlspecialchars($vars['title']) ?>">
        <meta name="DC.description" content="<?= htmlspecialchars($vars['description']) ?>"><?php

            if (\Idno\Core\Idno::site()->currentPage() && \Idno\Core\Idno::site()->currentPage()->isPermalink()) {
                $object = $vars['object'];
                /* @var \Idno\Common\Entity $object */
                if ($creator = $object->getOwner()) {
                    ?>
                    <meta name="DC.creator" content="<?= htmlentities($creator->getTitle()) ?>"><?php
                }
                if ($created = $object->created) {
                    ?>
                    <meta name="DC.date" content="<?= date('c', $created) ?>"><?php
                }
                if ($url = $object->getDisplayURL()) {
                    ?>
                    <meta name="DC.identifier" content="<?= htmlspecialchars($url) ?>"><?php
                }
            }

        ?>
        <link rel="alternate feed" type="application/rss+xml"
              title="<?= htmlspecialchars(\Idno\Core\Idno::site()->config()->title) ?>: all content"
              href="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>content/all?_t=rss"/>
        <link href="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>webmention/" rel="http://webmention.org/"/>
        <link href="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>webmention/" rel="webmention"/>
        <link href="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>css/simple.css" rel="stylesheet">
        <link href="//fonts.googleapis.com/css?family=Pontano+Sans" rel="stylesheet" type="text/css">
		<link href="//fonts.googleapis.com/css?family=Open+Sans:400,300,600,700,800" rel='stylesheet' type='text/css'>
        <script src="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() . 'external/jquery/' ?>jquery.min.js"></script>
        <?= $this->draw('shell/simple/head', $vars); ?>

        <?php

            if (!empty($vars['bootstrap'])) {

        ?>

                <link href="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() . 'external/bootstrap/' ?>assets/css/bootstrap.css"
                      rel="stylesheet">
                <link rel="stylesheet"
                      href="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>external/font-awesome/css/font-awesome.min.css">
                <style>
                    body {
                        padding-top: 10px; /* 60px to make the container go all the way to the bottom of the topbar */
                    }
                </style>
                <link
                    href="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() . 'external/bootstrap/' ?>assets/css/bootstrap-responsive.css"
                    rel="stylesheet">

        <?php

            }

        ?>

        <!-- Syndication -->
        <link href="<?=\Idno\Core\Idno::site()->config()->getDisplayURL()?>external/bootstrap-toggle/css/bootstrap2-toggle.min.css" rel="stylesheet" />
        <script src="<?=\Idno\Core\Idno::site()->config()->getDisplayURL()?>external/bootstrap-toggle/js/bootstrap2-toggle.min.js"></script>

    </head>
    <body class="<?php

        echo (str_replace('\\','_',strtolower(get_class(\Idno\Core\Idno::site()->currentPage()))));
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
        <?= $vars['body'] ?>
        <?= $this->draw('shell/simple/footer', $vars) ?>

        <!-- HTML5 form element support for legacy browsers -->
        <script src="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() . 'external/h5f/h5f.min.js' ?>"></script>
    </body>
</html>
