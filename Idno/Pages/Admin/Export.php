<?php

    namespace Idno\Pages\Admin {

        use Idno\Common\Page;

        class Export extends Page
        {

            function getContent()
            {

                $this->adminGatekeeper();

                $t = \Idno\Core\Idno::site()->template();
                $t->__(array(
                    'title' => 'Export data',
                    'body'  => $t->__(array(
                        'export_last_requested' => \Idno\Core\Idno::site()->config()->export_last_requested,
                        'export_in_progress'    => \Idno\Core\Idno::site()->config()->export_in_progress,
                        'export_filename'       => \Idno\Core\Idno::site()->config()->export_filename,
                        'export_file_id'        => \Idno\Core\Idno::site()->config()->export_file_id
                    ))->draw('admin/export'),
                ))->drawPage();

            }

        }

    }