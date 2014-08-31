<?php

    $currentPage = \Idno\Core\site()->currentPage();
    $action = \Idno\Core\site()->config()->url;
    if (!empty($vars['content'])) {
        if (!is_array($vars['content'])) {
            $vars['content'] = [$vars['content']];
        }
        $action .= 'content/' . implode('/', $vars['content']);
    } else {
        $action .= 'content/all/';
    }

?>
<form class="navbar-search pull-left" action="<?=$action?>" method="get">
    <input type="search" class="search-query" name="q" placeholder="Search" value="<?php

        if (!empty($currentPage)) {
            if ($q = $currentPage->getInput('q')) {
                echo htmlspecialchars($q);
            }
        }

    ?>">
</form>