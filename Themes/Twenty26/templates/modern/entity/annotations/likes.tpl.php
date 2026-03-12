<?php

if (!empty($vars['annotations']) && is_array($vars['annotations'])) {
    uasort(
        $vars['annotations'], function ($a, $b) {
            return ($a['time'] < $b['time']) ? -1 : 1;
        }
    );
    foreach ($vars['annotations'] as $locallink => $annotation) {
        $permalink = $annotation['permalink'] ? $annotation['permalink'] : $locallink;
?>
        <div class="idno-annotation">
            <?php if (!empty($annotation['owner_image'])) { ?>
            <img class="idno-annotation-avatar"
                 src="<?= htmlspecialchars($annotation['owner_image']) ?>"
                 alt="<?= htmlspecialchars($annotation['owner_name']) ?>" />
            <?php } ?>
            <div class="idno-annotation-content">
                <span>
                    <a href="<?= htmlspecialchars($annotation['owner_url']) ?>" rel="nofollow">
                        <?= htmlentities($annotation['owner_name'], ENT_QUOTES, 'UTF-8') ?>
                    </a>
                    <?= \Idno\Core\Idno::site()->language()->_('liked this post') ?>
                </span>
                <br>
                <span class="idno-entry-meta">
                    <a href="<?= htmlspecialchars($permalink) ?>" rel="nofollow"><?= date('M j, Y', $annotation['time']) ?></a>
                    on <a href="<?= htmlspecialchars($permalink) ?>" rel="nofollow"><?= parse_url($permalink, PHP_URL_HOST) ?></a>
                </span>
            </div>
            <?php
            $this->annotation_permalink = $locallink;
            if ($vars['object']->canEditAnnotation($annotation)) {
                echo $this->draw('content/annotation/edit');
            }
            ?>
        </div>
<?php
    }
}
