<?php

    /* @var $this \Idno\Core\Template */

    if (isset($vars['offset']) && !empty($vars['count'])) {

        if (empty($vars['items_per_page'])) {
            $items_per_page = \Idno\Core\Idno::site()->config()->items_per_page;
        } else {
            $items_per_page = $vars['items_per_page'];
        }
        $prev_offset = $vars['offset'] - $items_per_page;
        if ($prev_offset < 0) $prev_offset = 0;
        $next_offset = $vars['offset'] + $items_per_page;
        if ($next_offset > ($vars['count'] - 1)) $next_offset = $vars['count'] - 1;
?>

        <div class="pager">
            <ul>
                <?php if ($vars['offset'] > 0) { ?><li><a href="<?=$this->getURLWithVar('offset', $prev_offset);?>" title="Newer" rel="next">&laquo; Newer</a></li><?php } ?>
                <?php if ($vars['offset'] <= $vars['count'] - $items_per_page) { ?><li><a href="<?=$this->getURLWithVar('offset', $next_offset);?>" title="Older" rel="prev">Older &raquo;</a></li><?php } ?>
            </ul>
        </div>

<?php

    }

?>