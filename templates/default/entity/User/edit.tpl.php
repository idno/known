<form action="<?=$vars['user']->getURL()?>" method="post">

    <div class="row beforecontent">
        <div class="span11 offset1">
            <h1>Edit your profile</h1>
            <p>
                Your profile is how other users see you across the site. It's up to you how much or how little information you choose to provide.
            </p>
        </div>
    </div>

    <div class="row">

        <div class="span6 offset1">

            <p>
                <label>
                    About you<br />
                    <textarea name="profile[description]" id="body" class="span6 bodyInput"><?=htmlspecialchars($vars['user']->getDescription())?></textarea>
                </label>
            </p>

        </div>

        <div class="span4">
            <p>
                <label>
                    Website<br />
                    <input type="url" name="profile[url]" id="title" value="<?=htmlspecialchars($vars['user']->profile['url'])?>" placeholder="http://" class="span4" />
                </label>
            </p>
            <p>
                <?= \Idno\Core\site()->actions()->signForm('/profile/' . $vars['user']->getHandle()) ?>
                <input type="submit" class="btn btn-primary" value="Save" />
                <input type="button" class="btn" value="Cancel" onclick="hideContentCreateForm();" />
            </p>
        </div>

    </div>
</form>