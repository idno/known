<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <?php

            echo $this->draw('admin/menu');

        ?>
        <h1>
            <?php echo \Idno\Core\Idno::site()->language()->_('Import content'); ?>
        </h1>
        <p class="explanation">
            <?php echo \Idno\Core\Idno::site()->language()->_('Import your content from other sites into Known. All imported content will be treated as a post, with a title and body content.'); ?>
        </p>        
    </div>
</div>
<div class="row import">
    <div class="col-md-1 col-md-offset-1">
        <img src="<?php echo \Idno\Core\Idno::site()->config()->getStaticURL() ?>gfx/other/known.png" alt="Known" class="img-responsive">
    </div>
    <div class="col-md-9">
        <form action="<?php echo \Idno\Core\Idno::site()->config()->getDisplayURL()?>admin/import/" method="post" enctype="multipart/form-data">

            <h3>
                <?php echo \Idno\Core\Idno::site()->language()->_('Known'); ?>
            </h3>
            <p>
                <?php echo \Idno\Core\Idno::site()->language()->_('Upload a Known RSS export file and turn it into Known posts.'); ?>
                <a href="#" onclick="$('#known-explanation').show(); return false;"><?php echo \Idno\Core\Idno::site()->language()->_('How do I get my Known RSS file?'); ?></a>
            </p>
            <div id="known-explanation" class="well" style="display:none">
                <p>
                    <?php echo \Idno\Core\Idno::site()->language()->_('To get your Known RSS file'); ?>:
                </p>
                <ol>
                    <li><?php echo \Idno\Core\Idno::site()->language()->_('Log into your Known site'); ?></li>
                    <li><?php echo \Idno\Core\Idno::site()->language()->_('Click on Site Configuration'); ?></li>
                    <li><?php echo \Idno\Core\Idno::site()->language()->_('Click on Export'); ?></li>
                    <li><?php echo \Idno\Core\Idno::site()->language()->_('Download your RSS file'); ?></li>
                </ol>
            </div>
            <p>
                <label>
                    <span class="btn btn-primary btn-file" id="known-filename-wrapper">
                        <span id="known-filename"><?php echo \Idno\Core\Idno::site()->language()->_('Select Known export file'); ?></span> 
                        <input type="file" name="import" id="known-file" accept=".atom,.rss" onchange="$('#known-filename').html($('#known-file').val()); $('#known-filename-wrapper').css('background-color','#aaa'); $('#known-filename-wrapper').css('border','0'); $('#known-submit').show(); $('#known-submit').addClass('btn-primary')"/>
                    </span>
                </label>
                <?php echo \Idno\Core\Idno::site()->actions()->signForm('/admin/import') ?>
                <input type="hidden" name="import_type" value="Known">
                <input type="submit" class="btn " id="known-submit" value="<?php echo \Idno\Core\Idno::site()->language()->_('Import your data'); ?>" style="display:none"><br>
            </p>

        </form>
    </div>
</div>
<div class="row import">
    <div class="col-md-1 col-md-offset-1">
        <img src="<?php echo \Idno\Core\Idno::site()->config()->getStaticURL() ?>gfx/other/wordpress.png" alt="WordPress" class="img-responsive">
    </div>
    <div class="col-md-9">      
        <form action="<?php echo \Idno\Core\Idno::site()->config()->getDisplayURL()?>admin/import/" method="post" enctype="multipart/form-data">

            <h3>
                <?php echo \Idno\Core\Idno::site()->language()->_('WordPress'); ?>
            </h3>
            <p>
                <?php echo \Idno\Core\Idno::site()->language()->_('Upload a WordPress XML file and turn it into Known posts.'); ?>
                <a href="#" onclick="$('#wordpress-explanation').show(); return false;"><?php echo \Idno\Core\Idno::site()->language()->_('How do I get my WordPress XML file?'); ?></a>
            </p>
            <div id="wordpress-explanation" class="well" style="display:none">
                <p>
                    <?php echo \Idno\Core\Idno::site()->language()->_('To get your WordPress XML file'); ?>:
                </p>
                <ol>
                    <li><?php echo \Idno\Core\Idno::site()->language()->_('Log into your WordPress site'); ?></li>
                    <li><?php echo \Idno\Core\Idno::site()->language()->_('Click on Tools'); ?></li>
                    <li><?php echo \Idno\Core\Idno::site()->language()->_('Click on Export'); ?></li>
                    <li><?php echo \Idno\Core\Idno::site()->language()->_('Click to export posts'); ?></li>
                    <li><?php echo \Idno\Core\Idno::site()->language()->_('Click'); ?> <em><?php echo \Idno\Core\Idno::site()->language()->_('Download Export File'); ?></em></li>
                </ol>
            </div>
            <p>
                <label>
                    <span class="btn btn-primary btn-file" id="wordpress-filename-wrapper">
                        <span id="wordpress-filename"><?php echo \Idno\Core\Idno::site()->language()->_('Select WordPress export file'); ?></span> 
                        <input type="file" name="import" id="wordpress-file" accept=".xml,.atom,.rss" onchange="$('#wordpress-filename').html($('#wordpress-file').val()); $('#wordpress-filename-wrapper').css('background-color','#aaa'); $('#wordpress-filename-wrapper').css('border','0'); $('#wordpress-submit').show(); $('#wordpress-submit').addClass('btn-primary')"/>
                    </span>
                </label>
                <?php echo \Idno\Core\Idno::site()->actions()->signForm('/admin/import') ?>
                <input type="hidden" name="import_type" value="WordPress">
                <input type="submit" class="btn " id="wordpress-submit" value="<?php echo \Idno\Core\Idno::site()->language()->_('Import your data'); ?>" style="display:none"><br>
            </p>

        </form>  
    </div>
</div>
<div class="row import">
    <div class="col-md-1 col-md-offset-1">
        <img src="<?php echo \Idno\Core\Idno::site()->config()->getStaticURL() ?>gfx/other/blogger.png" alt="Blogger" class="img-responsive">
    </div>
    <div class="col-md-9">      
        <form action="<?php echo \Idno\Core\Idno::site()->config()->getDisplayURL()?>admin/import/" method="post" enctype="multipart/form-data">

            <h3>
                <?php echo \Idno\Core\Idno::site()->language()->_('Blogger'); ?>
            </h3>
            <p>
                <?php echo \Idno\Core\Idno::site()->language()->_('Upload a Blogger XML file and turn it into Known posts.'); ?> <a href="#" onclick="$('#blogger-explanation').show(); return false;"><?php echo \Idno\Core\Idno::site()->language()->_('How do I get my Blogger XML file?'); ?></a>
            </p>
            <div id="blogger-explanation" class="well" style="display:none">
                <p>
                    <?php echo \Idno\Core\Idno::site()->language()->_('To get your Blogger XML file'); ?>:
                </p>
                <ol>
                    <li><?php echo \Idno\Core\Idno::site()->language()->_('Log into Blogger, and click on your blog title'); ?></li>
                    <li><?php echo \Idno\Core\Idno::site()->language()->_('Click on Settings'); ?></li>
                    <li><?php echo \Idno\Core\Idno::site()->language()->_('Click on Other'); ?></li>
                    <li><?php echo \Idno\Core\Idno::site()->language()->_('Click on Export Posts at the top of your page'); ?></li>
                    <li><?php echo \Idno\Core\Idno::site()->language()->_('Click on Export Blog'); ?></li>
                </ol>
            </div>
            <p>
                <label>
                    <span class="btn btn-primary btn-file" id="blogger-filename-wrapper">
                        <span id="blogger-filename">Select Blogger export file</span> <input type="file" name="import" id="blogger-file"
                                                                                                           accept=".xml,.atom"
                                                                                                           onchange="$('#blogger-filename').html($('#blogger-file').val()); $('#blogger-filename-wrapper').css('background-color','#aaa'); $('#blogger-filename-wrapper').css('border','0'); $('#blogger-submit').show(); $('#blogger-submit').addClass('btn-primary')"/>

                    </span>
                </label>
                <?php echo \Idno\Core\Idno::site()->actions()->signForm('/admin/import') ?>
                <input type="hidden" name="import_type" value="Blogger">
                <input type="submit" class="btn " id="blogger-submit" value="<?php echo \Idno\Core\Idno::site()->language()->_('Import your data'); ?>" style="display:none">
            </p>

        </form>

    </div>
</div>
