<div class="row">

    <div class="col-md-10 col-md-offset-1">

        <?= $this->draw('account/menu') ?>
        <h1>Member Directory</h1>

    </div>

</div>

<div class="member-directory">

    <form action="<?= \Idno\Core\site()->config()->getDisplayURL() ?>directory/" method="get">
        <div class="row" style="margin-bottom: 20px">
            <div class="col-md-10 col-md-offset-1">
                    <?= $this->__([
                        'name'        => 'q',
                        'value'       => $vars['query'],
                        'class'       => 'col-lg-6',
                        'placeholder' => 'Search'
                    ])->draw('forms/input/input'); ?>
            </div>
        </div>
    </form>

<?php

    if (!empty($vars['users'])) {
        foreach ($vars['users'] as $user) {
            /* @var \Idno\Entities\User $user */
            $post_count = $user->countPosts();
            ?>
            <div class="row h-card">
                <div class="col-md-1 col-md-offset-1">
                    <p>
                        <a href="<?= $user->getDisplayURL() ?>" class="u-url"><img src="<?= $user->getIcon() ?>"
                                                                                   class="u-photo member-icon"></a>
                    </p>
                </div>
                <div class="col-md-2">
                    <p>
                            <span class="p-name member-name"><a
                                    href="<?= $user->getDisplayURL() ?>"><?= $user->getTitle() ?></a></span><br>
                        <a href="<?= $user->getDisplayURL() ?>">@<?= $user->getHandle() ?></a>
                    </p>
                </div>
                <div class="col-md-3 light-description">
                    <p><?= $user->getShortDescription(15); ?></p>
                </div>
                <div class="col-md-3 light-description">
                    <p><a href="<?= $user->getDisplayURL() ?>">Published <?= $post_count ?>
                            time<?php if ($post_count != 1) echo 's'; ?></a></p>
                </div>
                <div class="col-md-1">
                    <p>
                        <a href="<?= $user->getDisplayURL() ?>"><i class="fa fa-chevron-right"></i></a>
                    </p>
                </div>
            </div>
            <?php

        }
    }

?>
</div>
<?= $this->drawPagination($vars['count']); ?>