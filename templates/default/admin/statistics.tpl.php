<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <?= $this->draw('admin/menu') ?>
        <h1><?= \Idno\Core\Idno::site()->language()->_('Statistics'); ?></h1>


        <div class="explanation">
            <p>
                <?= \Idno\Core\Idno::site()->language()->_('This page provides you with information and statistics about your Known site.'); ?>
            </p>
        </div>


        <ul class="nav nav-tabs" role="tablist">

            <?php
            foreach ($vars['statistics'] as $tab => $report) {

                $sanitised_tab = strtolower(str_replace(' ', '', $tab));

                ?>

                <li role="presentation" id="tab-stats-<?= $sanitised_tab; ?>">
                    <a href="#stats-<?= $sanitised_tab; ?>" aria-controls="stats-<?= $sanitised_tab; ?>" role="tab"
                       data-toggle="tab"><?= $tab; ?></a>
                </li>

                <?php
            }
            ?>
        </ul>
        <div class="tab-content">
            <?php
            foreach ($vars['statistics'] as $tab => $report) {

                $sanitised_tab = strtolower(str_replace(' ', '', $tab));

                ?>
                <div role="tabpanel1"
                     class="tab-pane <?= (empty($vars['tab']) || $vars['tab'] == 'Basic') ? 'active' : '' ?>"
                     id="stats-<?= $sanitised_tab; ?>">
                    <?= $this->__([
                        'report' => $report
                    ])->draw('admin/statistics/report'); ?>
                </div>
                <?php
            }
            ?>
        </div>

    </div>

</div>

<script>
    $(document).ready(function () {
        $('#statistics-report-run').click(function () {
            $(this).html("Generating...").attr('disabled', 'true');

            $('#statistics-report pre').load('<?= \Idno\Core\Idno::site()->currentPage()->currentUrl(); ?>', function () {
                $('#statistics-report-run').hide();
                $('#statistics-report').fadeIn();
            });
        });
    });
</script>