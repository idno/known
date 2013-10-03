<form class="navbar-search pull-left" action="/search/" method="get">
    <input type="text" class="search-query" name="q" placeholder="Search" value="<?php
           if ($q = \Idno\Core\site()->currentPage()->getInput('q')) {
               echo htmlspecialchars($q);
           }
           ?>">
</form>