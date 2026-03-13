<?php

namespace Themes\Twenty26 {

    class Controller extends \Idno\Common\Theme
    {

        function init()
        {
            // Set the modern template type for this theme
            \Idno\Core\Idno::site()->template()->setTemplateType('modern');

            // Route admin and account pages to dedicated shell templates
            \Idno\Core\Idno::site()->template()->addUrlShellOverride('admin', 'admin-shell');
            \Idno\Core\Idno::site()->template()->addUrlShellOverride('account', 'account-shell');

            // Register drafts page
            \Idno\Core\Idno::site()->routes()->addRoute('/drafts/?', 'Themes\Twenty26\Pages\Drafts');
        }

    }

}
