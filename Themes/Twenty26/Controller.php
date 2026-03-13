<?php

namespace Themes\Twenty26 {

    class Controller extends \Idno\Common\Theme
    {

        function init()
        {
            // Set the modern template type for this theme
            \Idno\Core\Idno::site()->template()->setTemplateType('modern');

            // Register drafts page
            \Idno\Core\Idno::site()->routes()->addRoute('/drafts/?', 'Themes\Twenty26\Pages\Drafts');
        }

    }

}
