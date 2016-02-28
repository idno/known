<div class="row">

    <div class="col-md-10 col-md-offset-1">
        <?= $this->draw('admin/menu') ?>
        <h2>Blog</h2>
        <div class="explanation">
            <p>
                Lets you configure how blogs should be displayed on your homepage
            </p>
        </div>
    </div>        
</div>



<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <form action="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>admin/blog/" method="post">

            <div class="row">
                <div class="col-md-2">
                    <p><label class="control-label" for="open_registration"><strong>Truncate Blog Post on homepage</strong></label></p>
                </div>
                <div class="config-toggle col-md-4">
                    <input type="checkbox" data-toggle="toggle" data-onstyle="info" data-on="Yes" data-off="No"
                           name="truncate"
                           value="true" <?php if (\Idno\Core\Idno::site()->config()->truncate == true) echo 'checked'; ?>>
                </div>
            </div>


            <div class="row">
                <div class="col-md-2">
                    <p><label class="control-label" for="character"><strong>Number of character</strong></label></p>
                </div>
                <div class="col-md-4">
                    <input type="number" id="chracter" placeholder="eg. 200" class="input col-md-4 form-control" name="character"
                           value="<?= htmlspecialchars(\Idno\Core\Idno::site()->config()->truncate_character) ?>">
                </div>
                <div class="col-md-6">
                    <p class="config-desc">Number of character after which the post should be truncated</p>
                </div>
            </div>

            <div class="controls-save">
                <button type="submit" class="btn btn-primary">Save updates</button>
            </div>

    </div>
</div>
<?= \Idno\Core\Idno::site()->actions()->signForm(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'admin/blog/') ?>

</form>
