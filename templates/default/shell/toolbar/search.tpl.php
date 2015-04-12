<?php

    $currentPage = \Idno\Core\site()->currentPage();
    $action = \Idno\Core\site()->config()->getDisplayURL();
    if (!empty($vars['content'])) {
        if (!is_array($vars['content'])) {
            $vars['content'] = array($vars['content']);
        }
        $action .= 'content/' . implode('/', $vars['content']);
    } else {
        $action .= 'content/all/';
    }

?>
<form class="navbar-form navbar-search navbar-left" action="<?=$action?>" method="get">
    <div class="form-group">
        <input type="search" class="search-query form-control" name="q" placeholder="Search" value="<?php

            if (!empty($currentPage)) {
                if ($q = $currentPage->getInput('q')) {
                    echo htmlspecialchars($q);
                }
            }

        ?>">
    </div>
</form>