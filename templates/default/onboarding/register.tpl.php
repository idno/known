<div id="form-main">
    <div id="form-div">
        <h2 class="register">Hello there!</h2>

        <p>Create a new account to get started.</p>

        <?=$this->draw('shell/simple/messages')?>

        <form action="<?=\Idno\Core\site()->config()->getDisplayURL()?>begin/register" method="post" class="form-horizontal">

            <p class="username">
                <label class="control-label" for="inputUsername">Choose a username<br/></label>
                <input name="handle" type="text" class="feedback-input" placeholder="username" id="username" autocapitalize="off" required />
            </p>

            <p class="email">
                <label class="control-label" for="inputUsername">Your email address<br/></label>
                <input name="email" type="email" class="feedback-input" id="email" placeholder="you@email.com" autocapitalize="off" required />
            </p>

            <p class="password">
                <label class="control-label" for="inputUsername">Create a password <small>(at least 7 characters please)</small><br/></label>
                <input name="password" type="password" class="feedback-input" id="password" placeholder="secret-password" required />
            </p>

            <p class="password">
                <label class="control-label" for="inputUsername">Your password again<br/></label>
                <input name="password2" type="password" class="feedback-input" id="password" placeholder="secret-password" required />
            </p>

          <!--  <div class="submit">
                <input type="submit" value="Register" class="btn btn-reg"/>
            </div>-->
            
          <div class="col-md-12">
          <input type ="submit" value="Register" class="btn btn-primary btn-lg btn-responsive">
        
		  </div>

            <?= \Idno\Core\site()->actions()->signForm('/begin/register') ?>
        </form>

    </div>
</div>
