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
            $types = $this->getInput('types');

            if (!empty($this->arguments[0])) {  // If we're on the friendly content-specific URL
                if ($friendly_types = explode('/',$this->arguments[0])) {
                    $types = [];
                    // Run through the URL parameters and set content types appropriately
                    foreach($friendly_types as $friendly_type) {
                        if ($content_type_class =  \Idno\Common\ContentType::categoryTitleToClass($friendly_type)) {
                            $types[] = $content_type_class;
                        }
                    }
                }
            }

            if (empty($types)) {
                $types = 'Idno\Entities\ActivityStreamPost';
                $search = ['verb' => 'post'];
            } else {
                if (!is_array($types)) $types = [$types];
                $types[] = '!Idno\Entities\ActivityStreamPost';
                $search = [];
            }

            $count = \Idno\Entities\ActivityStreamPost::countFromX($types,[]);
            $feed = \Idno\Entities\ActivityStreamPost::getFromX($types,$search,[],\Idno\Core\site()->config()->items_per_page,$offset);
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