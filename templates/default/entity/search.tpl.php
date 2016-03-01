
<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <h2>
            <a href="<?=\Idno\Core\Idno::site()->config()->getDisplayURL() . '?q=' . urlencode($vars['subject'])?>"><?=htmlspecialchars($vars['subject'])?></a>
        </h2>
    </div>
</div>

<?php

    if (!empty($vars['items'])) {
        foreach($vars['items'] as $item) {
            echo $this->__(['object' => $item])->draw('entity/shell');
        }
    } else {
        echo '<div class="row"><div class="col-md-8 col-md-offset-2"><p>Nothing found.</p></div></div>';
    }

    echo $this->drawPagination($vars['count']);