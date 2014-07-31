<div id="bg">
    <img src="<?=\Idno\Core\site()->config()->getURL()?>gfx/onboarding/sky.jpg" alt="">
</div>
<div id="form-main">
    <div id="form-div">
        <h2 class="profile">Connect some networks</h2>

        <p>Add your favorite social networks and share with your audience.</p>

        <?=$this->draw('onboarding/connect/networks');?>

        <div class="next-bar">
            <input class="btn btn-skip" type="button" value="Skip" onclick="window.location = '<?=\Idno\Core\site()->config()->getURL()?>begin/publish'; return false;"/>
            <input class="btn btn-continue" type="button" value="Continue" onclick="window.location = '<?=\Idno\Core\site()->config()->getURL()?>begin/publish'; return false;" />
        </div>

        <p>
            Don't worry, you can always connect your social networks later.
        </p>

    </div>
</div>