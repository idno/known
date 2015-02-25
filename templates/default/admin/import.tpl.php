<div class="row">
    <div class="span10 offset1">
        <?php

            echo $this->draw('admin/menu');

        ?>
        <h1>
            Import your data
        </h1>


        <p class="explanation">
            Take your posts from other sites and bring them into your Known site.
        </p>

        <form action="<?=\Idno\Core\site()->config()->getDisplayURL()?>admin/import/" method="post" enctype="multipart/form-data">

            <h2>
                Blogger
            </h2>
            <p>
                Upload a Blogger XML file and turn it into Known posts.
                <small><a href="#" onclick="$('#blogger-explanation').show(); return false;">How do I get my Blogger XML file?</a></small>
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
                                                                                                           class="span9"
                                                                                                           accept=".xml,.atom"
                                                                                                           onchange="$('#blogger-filename').html($('#blogger-file').val()); $('#blogger-filename-wrapper').css('background-color','#aaa'); $('#blogger-filename-wrapper').css('border','0'); $('#blogger-submit').show(); $('#blogger-submit').addClass('btn-primary')"/>

                    </span>
                </label>
                <?= \Idno\Core\site()->actions()->signForm('/admin/import') ?>
                <input type="hidden" name="import_type" value="Blogger">
                <input type="submit" class="btn " id="blogger-submit" value="Import your data" style="display:none">
            </p>

        </form>

    </div>
</div>