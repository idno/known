<div class="row">

    <div class="span10 offset1">
        <h1>Themes</h1>
        <?=$this->draw('admin/menu')?>
        <div class="explanation">
            <p>
                Themes allow you to change the way your site looks.
            </p>
            <p>
                The following themes are installed. Click on one to enable it:
            </p>
        </div>
        <?php

            if (!empty($vars['themes_stored']) && is_array($vars['themes_stored'])) {
                foreach($vars['themes_stored'] as $shortname => $theme) {
                    $theme['shortname'] = $shortname;
                    echo $this->__(['theme' => $theme])->draw('admin/themes/theme');
                }
            }

        ?>
    </div>

</div>