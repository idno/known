<?php

    if (\Idno\Core\site()->canWrite()) {
        echo $this->draw('content/create');
    }

?>

<div class="row">

    <div class="span6 offset3">

        <h2 style="text-align: center">
            Publish your first story!
        </h2>

    </div>

    <div class="span6 offset3 explanation">

        <p>
            Sharing a moment is easy. Select a type of content above to get started.
        </p>
        <p>
            Not all stories get told through words. Try capturing a moment with an image
            or verbalizing your point of view through audio.
        </p>

    </div>

</div>