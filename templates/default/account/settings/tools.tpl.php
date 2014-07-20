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
                You can easily share websites you browse, reply to posts on other peoples' sites and RSVP to
                events by dragging the button below to your browser's bookmarked links bar:
            </p>
            <p>
                <?=$this->draw('entity/bookmarklet'); ?>
            </p>
        </div>
        <p>
            <strong>Don't see a bookmarks bar?</strong>
        </p>
        <p>
            Sometimes web browsers have their bookmarked links bar hidden by default. Here's how to reveal it:
        </p>
        <p>
            <strong>Chrome:</strong> Select <em>Always Show Bookmarks Bar</em> from the <em>View</em> menu.
        </p>
        <p>
            <strong>Firefox:</strong> Select <em>View</em>, then <em>Toolbars</em>, and make sure
            <em>Bookmarks Toolbar</em> is checked.
        </p>
        <p>
            <strong>Internet Explorer:</strong> Select <em>Tools</em>, then make sure <em>Favorites Bar</em> is
            checked.
        </p>
    </div>

</div>