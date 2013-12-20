
<div class="row">
    <div class="span8 offset2">
        <h2>
            <a href="<?=\Idno\Core\site()->config()->url . '?q=' . urlencode($vars['subject'])?>"><?=htmlspecialchars($vars['subject'])?></a>
        </h2>
    </div>
</div>

<?php

    if (!empty($vars['items'])) {
        foreach($vars['items'] as $item) {
            echo $this->__(array('object' => $item->getRelatedFeedItems()))->draw('entity/shell');
        }
    } else {
        echo '<div class="row"><div class="span8 offset2"><p>Nothing found.</p></div></div>';
    }

    echo $this->drawPagination($vars['count']);