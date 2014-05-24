<div class="row">

    <div class="span10 offset1">
        <h3>
            Create an account
        </h3>
        <div class="hero-unit">
            <p>

            </p>
            <form action="<?=\Idno\Core\site()->config()->url?>account/register" method="post" class="form-horizontal">
                <div class="control-group">
                    <label class="control-label" for="inputUsername">Your name<br />
                    <small>The name other people will see.</small></label>
                    <div class="controls">
                        <input type="text" id="inputName" placeholder="Your name" class="span4" name="name" value="">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="inputUsername">Your handle<br />
                        <small>A one-word name that will be part of your profile URL.</small>
                    </label>
                    <div class="controls">
                        <input type="text" id="inputUsername" placeholder="Your username" class="span4" name="handle" value="">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="inputEmail">Your email address</label>
                    <div class="controls">
                        <input type="email" id="inputEmail" placeholder="Your email address" class="span4" name="email" value="<?=htmlentities($vars['email'])?>">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="inputEmail">Your password<br /><small>Leave this blank if you don't want to change it</small></label>
                    <div class="controls">
                        <input type="password" id="inputPassword" placeholder="Password" class="span4" name="password" >
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="inputEmail">Your password again</label>
                    <div class="controls">
                        <input type="password" id="inputPassword" placeholder="Your password again" class="span4" name="password2">
                    </div>
                </div>
                <div class="control-group">
                    <div class="controls">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <input type="hidden" name="code" value="<?=htmlspecialchars($vars['code'])?>">
                    </div>
                </div>
                <?= \Idno\Core\site()->actions()->signForm('/account/register') ?>

            </form>
        </div>
    </div>

</div>