<?php

    /**
     * Search for entities
     */

    namespace Idno\Pages\Entity {

        /**
         * Default class to serve the homepage
         */
        class Search extends \Idno\Common\Page
        {

            // Handle GET requests to the entity

            function getContent()
            {

                $subject = $this->getInput('q');
                $types = $this->getInput('types');
                $subject = trim($subject);
                $offset = (int) $this->getInput('offset');
                if (empty($types)) {
                    $types = '!Idno\Entities\ActivityStreamPost';
                } else {
                    if (!is_array($types)) $types = [$types];
                    $types[] = '!Idno\Entities\ActivityStreamPost';
                }
                if (!empty($subject)) {
                    $regexObj = new \MongoRegex("/".addslashes($subject)."/i");
                    $items = \Idno\Common\Entity::getFromX($types,['$or' => [['body' => $regexObj], ['title' => $regexObj]]],[],10,$offset);
                    $count = \Idno\Common\Entity::countFromX($types,['$or' => [['body' => $regexObj], ['title' => $regexObj]]]);
                } else {
                    $items = [];
                    $subject = 'Search';
                    $count = 0;
                }
                $t = \Idno\Core\site()->template();
                $t->__(array(

                    'title' => $subject,
                    'body' => $t->__(array('subject' => $subject, 'items' => $items, 'count' => $count, 'offset' => $offset))->draw('entity/search')

                ))->drawPage();
            }

        }

    }