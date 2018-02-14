<div class="row">
    <div class="col-md-10 col-md-offset-1">
	    <?=$this->draw('admin/menu')?>
        <h1><?= \Idno\Core\Idno::site()->language()->_('Themes'); ?></h1>
        <div class="explanation">
            <p>
                <?= \Idno\Core\Idno::site()->language()->_('Themes allow you to change the way your site looks.
                The following themes are installed.'); ?>
            </p>
        </div>
        <?php

            if (!empty($vars['themes_stored']) && is_array($vars['themes_stored'])) {
                // Check for active theme
                $currentTheme = !empty($vars['theme']) ? $vars['theme'] : false;
                // Loop through the array to pull out active theme and draw it
                foreach($vars['themes_stored'] as $shortname => $theme) {
                    $theme['shortname'] = $shortname;
                    if($theme['shortname']==$currentTheme){
                        echo $this->__(array('theme' => $theme))->draw('admin/themes/theme');
                    }
                }
                // Loop through one more time to draw everything else
                foreach($vars['themes_stored'] as $shortname => $theme) {
                    $theme['shortname'] = $shortname;
                    if($theme['shortname']!=$currentTheme){
                        echo $this->__(array('theme' => $theme))->draw('admin/themes/theme');
                    }
                }
            }

        ?>
    </div>

</div>
