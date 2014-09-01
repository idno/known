<?php

    if (\Idno\Core\site()->canWrite()) {
        echo $this->draw('content/create');
    }

?>

<div class="row">

    <!--<div class="span1 offset3 explanation">
    <img src="../../../gfx/onboarding/arrow.png" alt="arrow"/></div>
    <div class="span5 explanation"><h3>Hi there, <a href="#">friend</a>!</h3><p>Publishing your first moment is easy. Just select a type of content above to get started.<br>You can also customize your site with <a href="#">a theme</a>, or update your <a href="#">settings</a>.</p></div>
    <div class="span6 offset3 explanation">
    <p>You can also customize your site with <a href="#">a theme</a>, or update your <a href="#">settings</a>.</p>
    </div>-->
    <div class="span6 offset3">

        <h2 style="text-align: center">
            Hi there, <a
                href="<?= \Idno\Core\site()->session()->currentUser()->getURL() ?>"><?= \Idno\Core\site()->session()->currentUser()->getHandle() ?></a>!
        </h2>

    </div>

    <div class="span6 offset3 explanation">

        <p style="text-align: center">
            Publishing your first moment is easy. Select a type of content above to get started.
        </p>
        <?php

            if (\Idno\Core\site()->session()->currentUser()->isAdmin()) {

                ?>
                <p style="text-align: center">
                    You can also customize your site with <a
                        href="<?= \Idno\Core\site()->config()->getURL() ?>admin/themes">a theme</a>, or update your <a
                        href="<?= \Idno\Core\site()->config()->getURL() ?>account/settings">settings</a>.
                </p>
            <?php

            } else {

                ?>
                <p style="text-align: center">
                    You can also update your <a href="<?= \Idno\Core\site()->config()->getURL() ?>account/settings">settings</a>
                    or add to your <a href="<?= \Idno\Core\site()->session()->currentUser()->getURL() ?>">profile</a>.
                </p>
            <?php


            }

        ?>

    </div>

</div>