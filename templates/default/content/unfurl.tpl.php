<?php
$dataurl = "";
if (!empty($vars['data-url']))
    $dataurl = htmlentities($vars['data-url']);
?>
<div class="unfurl col-md-12" style="display:none;" data-url="<?= $dataurl; ?>"></div>
<?php
    // clean up
    unset($this->vars['data-url']);
?>