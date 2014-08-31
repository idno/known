<div id="bg">
    <img src="<?=\Idno\Core\site()->config()->getURL()?>gfx/onboarding/sky.jpg" alt="">
</div>
<div id="form-main">
    <div id="form-div">
		<p class="social-connect">Connect your social accounts to make sharing easier</p>

        <?=$this->draw('onboarding/connect/networks');?>

        <div class="next-bar">
            <input class="btn btn-skip" type="button" value="Skip" onclick="window.location = '<?=\Idno\Core\site()->config()->getURL()?>begin/publish'; return false;"/>
            <input class="btn btn-continue" type="button" value="Continue" onclick="window.location = '<?=\Idno\Core\site()->config()->getURL()?>begin/publish'; return false;" />
        </div>

        <p>
            Don't worry, you can always connect these later.
        </p>

    </div>
</div>