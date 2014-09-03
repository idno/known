<div class="row idno-entry idno-entry-helper">
    <div class="span1 offset1 owner h-card hidden-phone">
        <p>
            <a class="u-url icon-container"><img class="u-photo"
                                                          src="<?=\Idno\Core\site()->config()->getURL()?>gfx/robots/1.png"/></a><br/>
            <a class="p-name u-url fn">Timble</a>
        </p>
    </div>
    <div class="visible-phone">
        <p class="p-author author h-card vcard">
            <a href="#" class="icon-container"><img
                    class="u-logo logo u-photo photo" src="<?=\Idno\Core\site()->config()->getURL()?>gfx/robots/1.png"/></a>
            <a class="p-name fn u-url url" href="#">Timble</a>
        </p>
    </div>
    <div
        class="span8 idno-helper idno-object idno-content">
        <div class="visible-phone">
            <p class="p-author author h-card vcard">
                <a href="#" class="icon-container"><img
                        class="u-logo logo u-photo photo" src="#"/></a>
                <a class="p-name fn u-url url" href="#">Timble</a>
            </p>
        </div>
        <div class="e-content entry-content">
            <?= $vars['body'] ?>
        </div>
        <div class="footer">
            <p>
                <?php

                    echo \Idno\Core\site()->actions()->createLink(\Idno\Core\site()->config()->getURL() . 'robot/remove', "Click here to remove robot helpers");

                ?>
            </p>
        </div>
    </div>
</div>