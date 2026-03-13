<?php
if (!empty($vars['annotations']) && is_array($vars['annotations'])) {
    uasort(
        $vars['annotations'], function ($a, $b) {
            return ($a['time'] < $b['time']) ? -1 : 1;
        }
    );
    foreach ($vars['annotations'] as $locallink => $annotation) {
        $permalink = $annotation['permalink'] ? $annotation['permalink'] : $locallink;
        // Extract annotation hash for anchor ID from the local link
        $annotationHash = basename(parse_url($locallink, PHP_URL_PATH));
        // For direct comments (permalink is a local annotation URL), use an anchor link
        $isLocalAnnotation = preg_match('#/annotations/[a-f0-9]+$#', $permalink);
        $dateHref = $isLocalAnnotation ? '#annotation-' . $annotationHash : $permalink;
?>
    <div class="idno-annotation h-cite" id="annotation-<?= htmlspecialchars($annotationHash) ?>">
        <?php if (!empty($annotation['owner_image'])) { ?>
        <a href="<?= htmlspecialchars($annotation['owner_url']) ?>" class="u-url">
            <img class="idno-annotation-avatar u-photo"
                 src="<?= htmlspecialchars($annotation['owner_image']) ?>"
                 alt="<?= htmlspecialchars($annotation['owner_name']) ?>" />
        </a>
        <?php } ?>
        <div class="idno-annotation-content">
            <div>
                <a href="<?= htmlspecialchars($annotation['owner_url']) ?>" class="p-name u-url p-author h-card" rel="nofollow" style="font-weight:600;color:var(--color-text-strong)">
                    <?= htmlspecialchars($annotation['owner_name']) ?>
                </a>
                <span class="idno-entry-meta">
                    <a href="<?= htmlspecialchars($dateHref) ?>" rel="nofollow" class="u-url">
                        <time class="dt-published" datetime="<?= date(DATE_ATOM, $annotation['time']) ?>">
                            <?= date('M j, Y', $annotation['time']) ?>
                        </time>
                    </a>
                </span>
            </div>
            <?php if (!empty($annotation['content'])) { ?>
            <div class="e-content" style="margin-top:0.25rem">
                <?= $this->autop($this->parseURLs(strip_tags($annotation['content']), 'rel="nofollow"')) ?>
            </div>
            <?php } ?>
            <?php if (!empty($permalink)) { ?>
            <div class="idno-annotation-source">
                via <a href="<?= htmlspecialchars($permalink) ?>" class="u-url" rel="nofollow">
                    <?= parse_url($permalink, PHP_URL_HOST) ?>
                </a>
            </div>
            <?php } ?>
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
?>
