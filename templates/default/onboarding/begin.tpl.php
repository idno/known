<div id="bg">
    <img src="<?=\Idno\Core\site()->config()->getURL()?>gfx/onboarding/airballoon.jpg" alt="">
</div>
<div id="form-main">
    <div id="form-div">
        <h1 class="h-register">Known</h1>

        <p class="p-register">Known is a social publishing platform.<br>
            <br>Capture your moments, share your stories, and own your space on the web.</p>

        <?=$this->draw('shell/simple/messages')?>

        <form class="form" method="get" action="<?=\Idno\Core\site()->config()->getURL()?>begin/register/">

            <div class="submit">
                <input class="btn btn-reg" type="submit" value="Register"/>
            </div>

        </form>
        <div class="space">&nbsp;</div>
        <form class="form" method="get" action="<?=\Idno\Core\site()->config()->getURL()?>session/login/">

            <div class="submit">
                <input type="submit" value="Login" class="btn btn-login"/>
            </div>

        </form>

    </div>
</div>