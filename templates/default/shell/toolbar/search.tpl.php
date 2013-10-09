<?php

    $currentPage = \Idno\Core\site()->currentPage();

?>
<form class="navbar-search pull-left" action="/search/" method="get">
    <input type="text" class="search-query" name="q" placeholder="Search" value="<?php

        if (!empty($currentPage)) {
            if ($q = $currentPage->getInput('q')) {
                echo htmlspecialchars($q);
            }
        }

    ?>">
</form>