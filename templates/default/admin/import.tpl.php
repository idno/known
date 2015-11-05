<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <?php

            echo $this->draw('admin/menu');

        ?>
        <h1>
            Import your data
        </h1>


        <p class="explanation">
            Take your posts from other sites and bring them into your Known site.
        </p>

        <form action="<?=\Idno\Core\Idno::site()->config()->getDisplayURL()?>admin/import/" method="post" enctype="multipart/form-data">

            <h2>
                WordPress
            </h2>
            <p>
                Upload a WordPress XML file and turn it into Known posts.
                <small>(Experimental) <a href="#" onclick="$('#wordpress-explanation').show(); return false;">How do I get my WordPress XML file?</a></small>
            </p>
            <div id="wordpress-explanation" class="well" style="display:none">
                <p>
                    To get your WordPress XML file:
                </p>
                <ol>
                    <li>Log into your WordPress site</li>
                    <li>Click on Tools</li>
                    <li>Click on Export</li>
                    <li>Click to export posts</li>
                    <li>Click <em>Download Export File</em></li>
                </ol>
            </div>
            <p>
                <label>
                    <span class="btn btn-primary btn-file" id="wordpress-filename-wrapper">
                        <span id="wordpress-filename">Select your WordPress export file</span> <input type="file" name="import" id="wordpress-file"
                                                                                                  class="span9"
                                                                                                  accept=".xml,.atom"
                                                                                                  onchange="$('#wordpress-filename').html($('#wordpress-file').val()); $('#wordpress-filename-wrapper').css('background-color','#aaa'); $('#wordpress-filename-wrapper').css('border','0'); $('#wordpress-submit').show(); $('#wordpress-submit').addClass('btn-primary')"/>

                    </span>
                </label>
                <?= \Idno\Core\Idno::site()->actions()->signForm('/admin/import') ?>
                <input type="hidden" name="import_type" value="WordPress">
                <input type="submit" class="btn " id="wordpress-submit" value="Import your data" style="display:none"><br>
            </p>

        </form>

        <form action="<?=\Idno\Core\Idno::site()->config()->getDisplayURL()?>admin/import/" method="post" enctype="multipart/form-data">

            <h2>
                Blogger
            </h2>
            <p>
                Upload a Blogger XML file and turn it into Known posts.</p>
                <p><a href="#" onclick="$('#blogger-explanation').show(); return false;">How do I get my Blogger XML file?</a>
            </p>
            <div id="blogger-explanation" class="well" style="display:none">
                <p>
                    To get your Blogger XML file:
                </p>
                <ol>
                    <li>Log into Blogger, and click on your blog title</li>
                    <li>Click on Settings</li>
                    <li>Click on Other</li>
                    <li>Click on Export Posts at the top of your page</li>
                    <li>Click on Export Blog</li>
                </ol>
            </div>
            <p>
                <label>
                    <span class="btn btn-primary btn-file" id="blogger-filename-wrapper">
                        <span id="blogger-filename">Select your Blogger export file</span> <input type="file" name="import" id="blogger-file"
                                                                                                           class="col-md-9"
                                                                                                           accept=".xml,.atom"
                                                                                                           onchange="$('#blogger-filename').html($('#blogger-file').val()); $('#blogger-filename-wrapper').css('background-color','#aaa'); $('#blogger-filename-wrapper').css('border','0'); $('#blogger-submit').show(); $('#blogger-submit').addClass('btn-primary')"/>

                    </span>
                </label>
                <?= \Idno\Core\Idno::site()->actions()->signForm('/admin/import') ?>
                <input type="hidden" name="import_type" value="Blogger">
                <input type="submit" class="btn " id="blogger-submit" value="Import your data" style="display:none">
            </p>

        </form>

    </div>
</div>