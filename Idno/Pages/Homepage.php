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
            $offset = (int) $this->getInput('offset');
            $count = \Idno\Entities\ActivityStreamPost::count([]);
            $feed = \Idno\Entities\ActivityStreamPost::get([],[],\Idno\Core\site()->config()->items_per_page,$offset);
            if (\Idno\Core\site()->session()->isLoggedIn()) {
                $create = \Idno\Common\ContentType::getRegistered();
            } else {
                $create = false;
            }

            if (!empty(\Idno\Core\site()->config()->description)) {
                $description = \Idno\Core\site()->description;
            } else {
                $description = 'An independent social website, powered by idno.';
            }

            $t = \Idno\Core\site()->template();
            $t->__(array(

                'title' => \Idno\Core\site()->config()->title,
                'description' => $description,
                'body' => $t->__(array(
                    'items' => $feed,
                    'contentTypes' => $create,
                    'offset' => $offset,
                    'count' => $count
                ))->draw('pages/home'),

            ))->drawPage();
        }

    }

}