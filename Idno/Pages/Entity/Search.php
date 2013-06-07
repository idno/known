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
                $subject = trim($subject);
                $offset = (int) $this->getInput('offset');
                if (!empty($subject)) {
                    $regexObj = new \MongoRegex("/".addslashes($subject)."/i");
                    $items = \Idno\Common\Entity::getFromAll(['$or' => [['body' => $regexObj], ['title' => $regexObj]]],[],10,$offset);
                    $count = \Idno\Common\Entity::countFromAll(['$or' => [['body' => $regexObj], ['title' => $regexObj]]]);
                } else {
                    $items = [];
                    $subject = 'Search';
                }

                $t = \Idno\Core\site()->template();
                $t->__(array(

                    'title' => $subject,
                    'body' => $t->__(array('subject' => $subject, 'items' => $items, 'count' => $count, 'offset' => $offset))->draw('entity/search')

                ))->drawPage();
            }

        }

    }