
<div class="row">
    <div class="span8 offset2">
        <h2>
            <a href="<?=\Idno\Core\site()->config()->url . 'search/?q=' . urlencode($vars['subject'])?>"><?=htmlspecialchars($vars['subject'])?></a>
        </h2>
    </div>
</div>

<?php

    if (!empty($vars['items'])) {
        foreach($vars['items'] as $item) {
            echo $this->__(array('object' => $item->getRelatedFeedItems()))->draw('entity/shell');
        }
    } else {
        echo '<p>Nothing found.</p>';
    }

    echo $this->drawPagination($vars['count']);