<?php
    $user = \Idno\Core\site()->session()->currentUser();
?>
<div class="row">

    <div class="span10 offset1">
        <h1>
            Settings
        </h1>
        <?= $this->draw('account/menu') ?>
        <div class="explanation">
            <p>
                Change your basic account settings here. You may also want to <a
                    href="<?= \Idno\Core\site()->session()->currentUser()->getURL() ?>/edit/">edit your
                    profile</a>.
            </p>
        </div>

        <form action="<?= \Idno\Core\site()->config()->url ?>account/settings" method="post" class="form-horizontal"
              enctype="multipart/form-data">
            <div class="control-group">
                <label class="control-label" for="inputName">Your name</label>

                <div class="controls">
                    <input type="text" id="inputName" placeholder="Your name" class="span4" name="name"
                           value="<?= htmlspecialchars($user->getTitle()) ?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputHandle">Your username</label>

                <div class="controls">
                    <input type="text" id="inputHandle" placeholder="Your handle" class="span4" name="handle"
                           value="<?= htmlspecialchars($user->handle) ?>" disabled>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputEmail">Your email address</label>

                <div class="controls">
                    <input type="email" id="inputEmail" placeholder="Your email address" class="span4" name="email"
                           value="<?= htmlspecialchars($user->email) ?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputPassword">Your password<br/>
                   <!-- <small>Leave this blank if you don't want to change it</small>-->
                </label>

                <div class="controls">
                    <input type="password" id="inputPassword" placeholder="Password" class="span4" name="password">
                     
                </div>
                <div class="controls"><small>Leave this blank if you don't want to change it</small></div>
            </div>
            <div class="control-group">
                <div class="controls">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
            <?= \Idno\Core\site()->actions()->signForm('/account/settings') ?>

        </form>
    </div>

</div>