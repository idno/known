<?php

    header('Content-type: text/html');
    header('Link: <' . \Idno\Core\site()->config()->url . 'webmention/>; rel="http://webmention.org/"');
    header('Link: <' . \Idno\Core\site()->config()->url . 'webmention/>; rel="webmention"');
    header("Access-Control-Allow-Origin: *");

    if (empty($vars['title']) && !empty($vars['description'])) {
        $vars['title'] = implode(' ', array_slice(explode(' ', strip_tags($vars['description'])), 0, 10));
    }

?><!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <title><?= htmlspecialchars($vars['title']); ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="viewport" content="initial-scale=1.0" media="(device-height: 568px)"/>
        <meta name="description" content="<?= htmlspecialchars(strip_tags($vars['description'])) ?>">
        <meta name="generator" content="Known http://withknown.com">
        <?= $this->draw('shell/favicon'); ?>
        <link rel="schema.DC" href="http://purl.org/dc/elements/1.1/">
        <meta name="DC.title" content="<?= htmlspecialchars($vars['title']) ?>">
        <meta name="DC.description" content="<?= htmlspecialchars($vars['description']) ?>"><?php

            if (\Idno\Core\site()->currentPage() && \Idno\Core\site()->currentPage()->isPermalink()) {
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
                if ($url = $object->getURL()) {
                    ?>
                    <meta name="DC.identifier" content="<?= htmlspecialchars($url) ?>"><?php
                }
            }

        ?>
        <link rel="alternate feed" type="application/rss+xml"
              title="<?= htmlspecialchars(\Idno\Core\site()->config()->title) ?>: all content"
              href="<?= \Idno\Core\site()->config()->url ?>content/all?_t=rss"/>
        <link href="<?= \Idno\Core\site()->config()->url ?>webmention/" rel="http://webmention.org/"/>
        <link href="<?= \Idno\Core\site()->config()->url ?>webmention/" rel="webmention"/>
        <link href="<?= \Idno\Core\site()->config()->url ?>css/simple.css" rel="stylesheet">
        <link href="http://fonts.googleapis.com/css?family=Pontano+Sans" rel="stylesheet" type="text/css">
		<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700,800" rel='stylesheet' type='text/css'>
        <script src="<?= \Idno\Core\site()->config()->url . 'external/jquery/' ?>jquery.min.js"></script>
        <?= $this->draw('shell/simple/head', $vars); ?>

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
        <?= $vars['body'] ?>
        <?= $this->draw('shell/simple/footer', $vars) ?>
    </body>
</html>
