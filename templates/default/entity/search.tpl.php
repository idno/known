
<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <h2>
            <a href="<?php echo \Idno\Core\Idno::site()->config()->getDisplayURL() . '?q=' . urlencode($vars['subject'])?>"><?php echo htmlspecialchars($vars['subject'])?></a>
        </h2>
    </div>
</div>

<?php

if (!empty($vars['items'])) {
    foreach($vars['items'] as $item) {
        echo $this->__(['object' => $item])->draw('entity/shell');
    }
} else {
    ?>
        <div class="row"><div class="col-md-8 col-md-offset-2"><p><?php echo \Idno\Core\Idno::site()->language()->_('Nothing found.'); ?></p></div></div>';
        <?php
}

    echo $this->drawPagination($vars['count']);