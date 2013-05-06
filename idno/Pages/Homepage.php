<?php

/**
 * Defines the site homepage
 */

namespace Idno\Pages {

    /**
     * Default class to serve the homepage
     */
    class Homepage extends \Idno\Common\Page
    {

        // Handle GET requests to the homepage

        function getContent()
        {
            $feed = \Idno\Entities\ActivityStreamPost::get();
            if (\Idno\Core\site()->session()->isLoggedIn()) {
                $create = \Idno\Common\ContentType::getRegistered();
            } else {
                $create = false;
            }
            $t = \Idno\Core\site()->template();
            $t->__(array(

                'title' => \Idno\Core\site()->config()->title,
                'body' => $t->__(array(
                    'feed' => $feed,
                    'contentTypes' => $create
                ))->draw('pages/home'),

            ))->drawPage();
        }

    }

}