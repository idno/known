<div class="row">

    <div class="span10 offset1">

        <h1>
            Follow <?=$vars['feed']->getTitle()?>? <input type="submit" class="btn btn-primary" value="Follow">
        </h1>
        <p class="explanation">
            Here's the latest content.
        </p>

    </div>

</div>
<?php

    if (!empty($vars['items'])) {

        foreach($vars['items'] as $item) {

            /* @var \Idno\Entities\Reader\FeedItem $item */
?>

            <?=$item->draw()?>

<?php

        }

    }

?>