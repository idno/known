<?php
if (empty($vars['name'])) {
    $vars['name'] = 'user-search';
}

global $input_id;
if (!isset($vars['id'])) {
    $input_id ++;
    $vars['id'] = $vars['name'] . "_$input_id";
}

if (empty($vars['source-url'])) {
    $vars['source-url'] = \Idno\Core\Idno::site()->config()->getDisplayURL() . 'search/users/';
}

if (empty($vars['render-template'])) {
    $vars['render-template'] = 'forms/components/usersearch/user';
}
?>
<div id="<?= $vars['id'] ?>" class="users-search <?php if (!empty($vars['class'])) echo $vars['class']; ?>">
    <form action="<?= $vars['source-url'] ?>">
        <div style="display: flex; gap: 0.5rem; margin-bottom: 1rem;">
            <input name="query" type="text" class="idno-input" style="flex: 1;"
                   placeholder="<?= \Idno\Core\Idno::site()->language()->_('Search by name, email address, or username') ?>"
                   aria-describedby="search">
            <button type="submit" class="idno-btn-primary">
                <?= $this->__(['icon' => 'search'])->draw('shell/icon') ?>
            </button>
            <?= $this->__(['name' => 'template', 'value' => $vars['render-template']])->draw('forms/input/hidden') ?>
            <?= $this->__(['name' => 'sort', 'value' => 'created'])->draw('forms/input/hidden') ?>
            <?= $this->__(['name' => 'order', 'value' => 'desc'])->draw('forms/input/hidden') ?>
            <?= $this->__(['name' => 'offset', 'value' => 0])->draw('forms/input/hidden') ?>
            <?= $this->__(['name' => 'limit', 'value' => 100])->draw('forms/input/hidden') ?>
            <?= $this->__(['name' => 'count'])->draw('forms/input/hidden') ?>
        </div>
    </form>

    <div class="results pane"></div>

    <div class="pager" style="display: flex; gap: 1rem; margin-top: 0.75rem;">
        <span class="newer pagination-disabled"><a href="#" title="Previous" rel="prev" style="font-size: var(--font-size-sm); color: var(--color-text-secondary); text-decoration: none;">&laquo; <?= \Idno\Core\Idno::site()->language()->_('Prev') ?></a></span>
        <span class="older pagination-disabled"><a href="#" title="Next" rel="next" style="font-size: var(--font-size-sm); color: var(--color-text-secondary); text-decoration: none;"><?= \Idno\Core\Idno::site()->language()->_('Next') ?> &raquo;</a></span>
    </div>
</div>
<?php
foreach (['source-url', 'control-id', 'render-template', 'name', 'id'] as $variable) {
    unset($this->vars[$variable]);
}
?>

<script>
    var form = $('#<?= $vars['id'] ?>');
    var form_actual = form.find('form');
    var query = form.find("input[name='query']");

    function executeSearch(form) {
        var query = form.find("input[name='query']");
        $.ajax({
            type: "GET",
            data: form.serialize(),
            url: form.attr('action'),
            success: function (data) {
                var count = form.find("input[name='count']");
                var offset = parseInt(form.find("input[name='offset']").val());
                var limit = parseInt(form.find("input[name='limit']").val());

                count.val(data.count);
                form.closest('div').find('.results').html(data.rendered);

                // Handle pagination
                form.closest('div').find('.pager span').addClass('pagination-disabled');
                if (offset > 0)
                    form.closest('div').find('.pager span.newer').removeClass('pagination-disabled');
                if (offset + limit <= data.count) {
                    form.closest('div').find('.pager span.older').removeClass('pagination-disabled');
                }
            }
        });
    }

    executeSearch(form_actual); // Load initial

    // Pagination
    $('.pager a').click(function (e) {
        e.preventDefault();

        var offset = parseInt(form.find("input[name='offset']").val());
        var limit = parseInt(form.find("input[name='limit']").val());
        var count = parseInt(form.find("input[name='count']").val());

        if ($(this).attr('rel') == 'prev') {
            if (offset > 0)
                form.find("input[name='offset']").val(offset - limit);
        } else {
            if (offset + limit <= count) {
                form.find("input[name='offset']").val(offset + limit);
            }
        }

        executeSearch(form_actual);
    });

    query.change(function () {
        form_actual.submit();
    });

    form_actual.submit(function(e) {
        e.preventDefault();
        executeSearch(form_actual);
    });
</script>
