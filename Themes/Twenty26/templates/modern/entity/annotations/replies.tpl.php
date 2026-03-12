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
    <div class="idno-entry h-cite" style="padding:var(--spacing-gap);background:var(--color-bg);border-radius:var(--radius-sm);margin-top:var(--spacing-gap)">
        <div class="idno-entry-header">
            <?php if (!empty($annotation['owner_image'])) { ?>
            <a href="<?= htmlspecialchars($annotation['owner_url']) ?>" class="u-url">
                <img class="idno-entry-avatar u-photo" src="<?= htmlspecialchars($annotation['owner_image']) ?>"
                     alt="<?= htmlspecialchars($annotation['owner_name']) ?>" />
            </a>
            <?php } ?>
            <div>
                <a href="<?= htmlspecialchars($annotation['owner_url']) ?>" class="idno-entry-author p-name u-url p-author h-card">
                    <?= htmlspecialchars($annotation['owner_name']) ?>
                </a>
                <span class="idno-entry-meta">
                    <a href="<?= htmlspecialchars($permalink) ?>" rel="nofollow" class="u-url">
                        <time class="dt-published" datetime="<?= date(DATE_ATOM, $annotation['time']) ?>">
                            <?= date('M j, Y', $annotation['time']) ?>
                        </time>
                    </a>
                </span>
            </div>
        </div>
        <?php if (!empty($annotation['content'])) { ?>
        <div class="idno-entry-body e-content" style="margin-top:var(--spacing-gap)">
            <?= $this->autop($this->parseURLs(strip_tags($annotation['content']), 'rel="nofollow"')) ?>
        </div>
        <?php } ?>
        <?php if (!empty($permalink)) { ?>
        <a href="<?= htmlspecialchars($permalink) ?>" class="u-url idno-entry-meta" style="margin-top:0.25rem;display:inline-block" rel="nofollow">
            <?= parse_url($permalink, PHP_URL_HOST) ?>
        </a>
        <?php } ?>
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
?>
