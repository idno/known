<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <?= $this->draw('admin/menu') ?>
        <h1>
            Pages
        </h1>

        <p class="explanation">
            Pages are a great way to add content to your site that you want to keep separate from your stream of normal posts and updates.  Common examples of pages include an about page, a contact page, or a resume.
        </p>

    </div>
</div>

<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <p class="pages">
            <a href="<?= \Idno\Core\site()->config()->getDisplayURL() ?>staticpages/edit/?category=<?= urlencode($category) ?>"
               class="btn btn-primary btn-add">Add new page</a>
        </p>
    </div>
</div>
<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <?php

            if (!empty($vars['pages'])) {

                ?>
                <table style="width: 100%; margin-bottom: 3em">
                    <tr class="pages">
                        <td class="pages" width="35%">Title</td>
                        <td class="pages" width="35%">Category</td>
                        <td class="pages" width="15%">&nbsp;</td>
                        <td class="pages" width="15%">&nbsp;</td>
                    </tr>
                    <?php

                        $categories = [];
                        foreach ($vars['pages'] as $category => $pages) {

                            $categories[$category] = sizeof($pages);

                            if (!empty($pages)) {
                                foreach ($pages as $page) {

                                    ?>
                                    <tr class="items">
                                        <td>
                                            <a href="<?= $page->getURL() ?>"><?= htmlspecialchars($page->getTitle()) ?></a>
                                        </td>
                                        <td>
                                            <?= $category ?>
                                        </td>
                                        <td>
                                            <icon class="fa fa-pencil"></icon> <a href="<?= \Idno\Core\site()->config()->getDisplayURL() ?>staticpage/edit/<?= $page->_id ?>">Edit</a>
                                        </td>
                                        <td><icon class="fa fa-trash-o"></icon>
                                            <?=  \Idno\Core\site()->actions()->createLink($page->getDeleteURL(), 'Delete', array(), array('method' => 'POST', 'class' => 'edit', 'confirm' => true, 'confirm-text' => 'Are you sure you want to permanently delete this page?'));?>
                                        </td>
                                    </tr>
                                <?php

                                }
                            }

                        }

                    ?>
                </table>
            <?php


            }

        ?>
    </div>
</div>
<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <h2>
            Categories
        </h2>

        <p class="explanation">
            If you plan on adding many pages, you may want to group them under categories.  However, you don’t have to assign a page to a category.
        </p>
    </div>
</div>
<div class="row">
    <div class="col-md-10 col-md-offset-1">

        <p class="pages" id="add-category-button">
            <a href="#" class="btn btn-primary btn-add" onclick="$('#add-category-button').hide(); $('#add-category').show(); return false;">Add new category</a>
        </p>
    </div>
</div>
<div class="row">
    <div class="col-md-10 col-md-offset-1">

        <div id="add-category" style="display:none">

            <form class="form-inline" action="<?=\Idno\Core\site()->config()->getDisplayURL()?>admin/staticpages/add/" method="post">
                <input id="pages-add" class="form-control" type="text" name="category" placeholder="Name of category to add">
                <input type="submit" class="btn btn-primary btn-page" value="Add">
                <?= \Idno\Core\site()->actions()->signForm('/admin/staticpages/add') ?>
            </form>

        </div>

        <?php

            if (!empty($categories)) {

                ?>
                <table style="width: 100%; margin-bottom: 3em">
                    <tr class="pages">
                        <td width="35%">Category Name</td>
                        <td width="35%">Count</td>
                        <td width="15%">&nbsp;</td>
                        <td width="15%">&nbsp;</td>
                    </tr>
                    <?php

                        foreach ($categories as $category => $count) {

                            $unique_id = md5($category);

                            ?>
                            <tr class="items">
                                <td>
                                    <div id="category-name-<?=$unique_id?>"><?= $category ?></div>
                                    <div id="edit-category-<?=$unique_id?>" style="display: none">
                                        <form class="form-inline" action="<?=\Idno\Core\site()->config()->getDisplayURL()?>admin/staticpages/edit/" method="post">
                                            <input class="form-control" type="text" name="new_category" value="<?=htmlspecialchars($category)?>">
                                            <input type="submit" value="Save" class="btn btn-primary btn-page">
                                            <input class="form-control" type="hidden" name="category" value="<?=htmlspecialchars($category)?>">
                                            <?= \Idno\Core\site()->actions()->signForm('/admin/staticpages/edit') ?>
                                        </form>
                                    </div>
                                </td>
                                <td>
                                    <?= $count ?>
                                </td>
                                <td>
                                    <?php

                                        if ($category != 'No Category') {

                                            ?>
                                            <i class="fa fa-pencil"></i>
                                            <a href="#" onclick="$('#category-name-<?=$unique_id?>').hide(); $('#edit-category-<?=$unique_id?>').show(); return false;">Edit</a>
                                        <?php

                                        }

                                    ?>
                                </td>
                                <td>
                                    <?php

                                        if ($category != 'No Category') {

                                    ?><i class="fa fa-trash-o"></i>
                                    <?=  \Idno\Core\site()->actions()->createLink(\Idno\Core\site()->config()->getDisplayURL() . 'admin/staticpages/delete/', 'Delete', array('category' => $category), array('method' => 'POST', 'class' => 'edit', 'confirm' => true, 'confirm-text' => 'Are you sure you want to permanently delete this category?'));?>
                                        <?php

                                        }

                                    ?>
                                </td>
                            </tr>
                        <?php

                        }

                    ?>
                </table>
            <?php

            }

        ?>
    </div>

</div>