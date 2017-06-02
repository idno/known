<?php
    if (\Idno\Core\Idno::site()->currentPage()->isPermalink()) {
        $rel = 'rel="in-reply-to" class="u-in-reply-to"';
    } else {
        $rel = '';
    }
          if(\Idno\Core\Idno::site()->config()->truncate === true){
          $limit = \Idno\Core\Idno::site()->config->truncate_character;
          if($this->getCurrentURL()===\Idno\Core\Idno::site()->config()->getDisplayURL() && strlen( $vars['object']->body) >= $limit) 
          {
            $string = substr($vars['object']->body, 0,$limit);
            if((strrpos($string, " ")) !== false ) {
            $string = substr($string, 0, strrpos($string, " "));
            $vars['object']->body = $string."....".'<a
                    href="'. $vars['object']->getDisplayURL().'">read full article</a>';
          }
          }
          }

    if (!empty($vars['object']->tags)) {
        $tags = is_array($vars['object']->tags) ? implode(', ' , $vars['object']->tags) : $vars['object']->tags;
        $vars['object']->body .= '<p class="tag-row"><i class="icon-tag"></i>' . $tags . '</p>';
    }
?>

<?php
    if (empty($vars['feed_view'])) {
        ?>
        <h2 class="p-name"><a
                href="<?= $vars['object']->getDisplayURL() ?>"><?= htmlentities(strip_tags($vars['object']->getTitle()), ENT_QUOTES, 'UTF-8'); ?></a>
        </h2>
    <?php

    }

    if (empty($vars['feed_view']) && empty($vars['object']->notime)) {

        ?>
        <p class="reading">
            <span class="vague"><?php

                    $minutes = $vars['object']->getReadingTimeInMinutes();
                    echo $minutes . ' min';

                ?> read </span>
        </p>
    <?php

    }

?>
<div class="e-content entry-content">
<?php

    echo $this->__(['value' => $vars['object']->body, 'object' => $vars['object'], 'rel' => $rel])->draw('forms/output/richtext');

?>
</div>
