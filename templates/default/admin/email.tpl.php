<div class="row">

    <div class="span10 offset1">
        <h1>Email</h1>
        <?=$this->draw('admin/menu')?>
        <div class="explanation">
            <p>
                Known tries to send email using your server's default email settings. If you'd like it to do
                something else - for example, if you'd like to send email using an external provider - enter the
                new SMTP settings below.
            </p>
        </div>
    </div>
    <div class="span10 offset1">
        <form action="<?=\Idno\Core\site()->config()->url?>admin/email" class="form-horizontal" method="post">
            <div class="control-group">
                &nbsp;
                <div class="controls">
                    <p>
                        Fill in the site email address if you would like your site to send email.
                    </p>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="from_email">Site email address<br /><small>The email address that this site will send email from. Every site notification will be sent from this address.</small></label>
                <div class="controls">
                    <input type="text" id="from_email" placeholder="Site email address" class="span4" name="from_email" value="<?=htmlspecialchars(\Idno\Core\site()->config()->from_email)?>" >
                </div>
            </div>
            <div class="control-group">
                &nbsp;
                <div class="controls">
                    <p>
                        You can often leave the following settings blank. However, you may wish to set them if you're
                        using a third-party service to send email.
                    </p>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="smtp_host">SMTP host<br /><small>The address of the server that will send email for you.</small></label>
                <div class="controls">
                    <input type="text" id="smtp_host" placeholder="SMTP host" class="span4" name="smtp_host" value="<?=htmlspecialchars(\Idno\Core\site()->config()->smtp_host)?>" >
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="smtp_username">SMTP username<br /><small>If you need a username to authenticate with your mail server, enter it here.</small></label>
                <div class="controls">
                    <input type="text" id="smtp_username" placeholder="SMTP username" class="span4" name="smtp_username" value="<?=htmlspecialchars(\Idno\Core\site()->config()->smtp_username)?>" >
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="smtp_password">SMTP password<br /><small>If your mail server needs a password, enter it here.</small></label>
                <div class="controls">
                    <input type="password" id="smtp_password" placeholder="SMTP password" class="span4" name="smtp_password" value="<?=htmlspecialchars(\Idno\Core\site()->config()->smtp_password)?>" >
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="smtp_password">SMTP port<br /><small>SMTP port to use.</small></label>
                <div class="controls">
                    <input type="number" id="smtp_port" placeholder="SMTP password" class="span4" name="smtp_port" value="<?php

                        $port = (int) \Idno\Core\site()->config()->smtp_port;
                        if (empty($port)) {
                            $port = 25;
                        }
                        echo $port;

                    ?>" >
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="smtp_tls">Use TLS?<br /><small>Use secure logins to your mail server?</small></label>
                <div class="controls">
                    <input type="checkbox" id="smtp_tls" name="smtp_tls" value="1" <?php if (!empty(\Idno\Core\site()->config()->smtp_tls)) { echo 'checked'; }  ?>>
                </div>
            </div>
            <div class="control-group">
                <div class="controls">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
            <?= \Idno\Core\site()->actions()->signForm('/admin/email')?>
        </form>
    </div>
</div>