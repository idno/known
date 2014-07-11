<?php
    $user = \Idno\Core\site()->session()->currentUser();
?>
<div class="row">

    <div class="span10 offset1">
        <h1>
            Tools and Apps
        </h1>
        <?= $this->draw('account/menu') ?>
        <div class="explanation">
            <p>
                Drag the following link into your browser links bar to easily share links or reply to posts on other sites:
                
                <?=$this->draw('entity/bookmarklet'); ?>
            </p>
        </div>
    </div>

</div>