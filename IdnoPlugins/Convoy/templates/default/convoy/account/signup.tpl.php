<div class="row">
    <div class="col-lg-10 col-lg-offset-1">

        <?= $this->draw('account/menu') ?>

        <h1 style="text-align: center; margin: 1em">
            Connect to social media
        </h1>
        <h2 style="margin-bottom: 1em !important; margin-top: 1em !important">
            <img src="https://withknown.com/img/convoy/seamless.png" align="right">
            Sign up with Convoy
        </h2>
        <p>
            Convoy allows you to syndicate your content to social media without the need to set up complicated
            APIs on your own server.
        </p>
        <p>
            Connect to Twitter, Facebook, LinkedIn, Flickr and more - instantly.
        </p>
        <p style="margin-top: 2em">
            <a href="https://withknown.com/convoy/?domain=<?=\Idno\Core\Idno::site()->config()->host?>" class="btn btn-primary">Get started</a>
        </p>

    </div>
</div>
