<div id="form-main">
    <div id="form-div">
        <div class="h-register"><img src="<?=\Idno\Core\site()->config()->getURL()?>gfx/onboarding/logo_black.png" alt="Known" class="img-responsive"></div>

        <p class="p-register">Known is your space for sharing content and discussing ideas.</p>

        <?=$this->draw('shell/simple/messages')?>
        
        <div align="center"><form class="form" method="get" action="<?=\Idno\Core\site()->config()->getURL()?>begin/register/">

            <!--<div class="submit">
                <input class="btn btn-reg" align="center" type="submit" value="Get started"/>
            </div>-->
            
        <div class="col-md-12 submit">
          <button class="btn btn-primary btn-lg btn-responsive" type="submit" value="Get started">Get started</button>
        
		  </div>


        </form></div>
        

        <!--<div class="space">&nbsp;</div>-->
        <p class="signin" align="center"><a href="<?=\Idno\Core\site()->config()->getURL()?>session/login/">Already have an account? Sign in.</a></p>
        <!--<form class="form" method="get" action="<?=\Idno\Core\site()->config()->getURL()?>session/login/">

            <div class="submit">
                <input type="submit" value="Login" class="btn btn-login"/>
            </div>

        </form>-->
 <div class="space">&nbsp;</div>
 
    </div>
</div>