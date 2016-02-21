<?php

    if ($staticpages = \Idno\Core\Idno::site()->plugins()->get('StaticPages')) {
        /* @var \IdnoPlugins\StaticPages\Main $staticpages */
        if ($pages_list = $staticpages->getPagesAndCategories()) {

            foreach($pages_list as $category => $pages) {

                if (!empty($pages) || substr($category, 0, 1) == '#') {

                    if ($category == 'No Category') {

                        if (!empty($pages)) {
                            foreach($pages as $page) {
                                if (!$page->isHomepage()) {
                                    ?>
                                    <li>
                                    	<ul class="nav">
                                        	<li>
                                            	<a href="<?= $page->getURL() ?>"><?= htmlspecialchars($page->getTitle()) ?></a>
    											</li>
                                    	</ul>
                                    </li>
                                    <?php
                                }
                            }
                        }

                    } else {

                        // If the category's only element is a the homepage, we should suppress it rather than showing
                        // an empty dropdown. At this point, the array is proven to be non-empty so there's no need to
                        // guard for that here.
                        if ( count($pages) > 1 || !array_values($pages)[0]->isHomepage() ) {
                            ?>

                           <li>
                            <ul class="nav">
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                        <?= $category ?>
                                        <b class="caret"></b>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <?php

                                            if (substr($category, 0, 1) == '#') {
                                                ?>
                                                <li>
                                                    <a href="<?= \Idno\Core\Idno::site()->config()->getURL() ?>content/all/?q=<?= urlencode($category) ?>">Stream</a>
                                                </li>
                                            <?php
                                            }
                                            if (!empty($pages)) {
                                                foreach ($pages as $page) {
                                                    if (!$page->isHomepage()) {
                                                        ?>
                                                        <li>
                                                            <a href="<?= $page->getURL() ?>"><?= htmlspecialchars($page->getTitle()) ?></a>
                                                        </li>
                                                    <?php
                                                    }
                                                }
                                            }

                                        ?>
                                    </ul>
                                </li>
                            </ul>

                           </li>

                        <?php
                        }
                    }

                }

            }

        }
    }
