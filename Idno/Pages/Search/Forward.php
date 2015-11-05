<?php

    /**
     * Legacy search
     */

    namespace Idno\Pages\Search {

        class Forward extends \Idno\Common\Page
        {

            function getContent()
            {
                $query = $this->getInput('q');
                $this->forward(\Idno\Core\Idno::site()->config()->url . '?q=' . urlencode($query));
            }

            function postContent()
            {
                $query = $this->getInput('q');
                $this->forward(\Idno\Core\Idno::site()->config()->url . '?q=' . urlencode($query));
            }

        }

    }