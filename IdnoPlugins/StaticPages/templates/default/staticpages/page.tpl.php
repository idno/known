<div class="row">

    <div class="col-md-10 col-md-offset-1">

        <?php

        if (!empty($vars['object']) && $vars['object'] instanceof \IdnoPlugins\StaticPages\StaticPage) {
            echo $vars['object']->draw();
        }



        ?>

    </div>

</div>