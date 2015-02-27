<div class="row">
    <div class="span10 offset1">
        <?= $this->draw('admin/menu') ?>
        <h1>Diagnostics</h1>


        <div class="explanation">
            <p>
                This tool will provide you with a set of diagnostics which may be helpful to you or others to get to the bottom of any problems you may have.
            </p>
            <p>
                Please note, this report may contain sensitive and security related information, so you absolutely must not send it to anyone in an unencrypted form!
            </p>

        </div>
    </div>
</div>

<div class="row">
    <div class="pane span10 offset1">
        <small><pre>
                <?= $vars['report']; ?>
            </pre></small>
    </div>

</div>