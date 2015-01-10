<div class="row">
    <div class="span10 offset1">
	    <?=$this->draw('admin/menu')?>
        <h1>Themes</h1>
        <div class="explanation">
            <p>
                Themes allow you to change the way your site looks.
                The following themes are installed.
            </p>
        </div>
        <?php

            if (!empty($vars['themes_stored']) && is_array($vars['themes_stored'])) {
                foreach($vars['themes_stored'] as $shortname => $theme) {
                    $theme['shortname'] = $shortname;
                    echo $this->__(array('theme' => $theme))->draw('admin/themes/theme');
                }
            }

        ?>
    </div>

</div>