
<meta charset="utf-8">
<title <?php if (\Idno\Core\Idno::site()->template()->isHFeed()) echo 'class="p-name"'?>><?php echo htmlspecialchars($vars['title']); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="viewport" content="initial-scale=1.0" media="(device-height: 568px)"/>
<meta name="description" content="<?php echo htmlspecialchars(strip_tags($vars['description'])) ?>">
<meta name="generator" content="Known https://withknown.com">
<meta http-equiv="Content-Language" content="<?php echo $vars['lang']; ?>">
<meta http-equiv="Status" content="<?php echo \Idno\Core\Idno::site()->currentPage()->response();?>"/>
<?php

    // Optionally, pages can send a "noindex" header to indicate they don't want to be indexed by search engines
if (!empty($vars['noindex'])) {
    ?><meta name="robots" content="noindex" /><?php
}

