<form action="<?=$vars['object']->getURL()?>" method="post">

    <div class="row">

        <div class="span10 offset1">

            <p>
<?php
    if (empty($vars['url'])) {

?>
        <small><a href="#" onclick="$('#in-reply-to').show()">Is this a reply to a post somewhere?</a></small>
        <span id="in-reply-to" style="display:none">
            <br />
<?php

    } else {

?>

    <span id="in-reply-to">

<?php

    }

?>
                    <input type="text" name="inreplyto" placeholder="The website address of the post you're replying to" class="span9" value="<?php if (empty($vars['url'])) { echo htmlspecialchars($vars['object']->inreplyto); } else { echo htmlspecialchars($vars['url']); } ?>" />
                </span>
            </p>
            <p>
                <label>
                    <?php
                        if (empty($vars['url']) && empty($vars['object']->inreplyto)) {
                            echo 'What\'s going on?';
                        } else {
                            echo 'Your message:';
                        }
                    ?>
                    <br />
                    <input type="text" name="body" id="body" value="<?php if (!empty($vars['body'])) { echo htmlspecialchars($vars['body']); } else { echo htmlspecialchars($vars['object']->body); } ?>" class="span9" />
                </label>
                <?= \Idno\Core\site()->actions()->signForm('/status/edit') ?>
                <input type="submit" class="btn btn-primary" value="Save" />
                <input type="button" class="btn" value="Cancel" onclick="hideContentCreateForm();" />
            </p>
        </div>

    </div>
</form>