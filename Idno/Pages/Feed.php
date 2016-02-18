<?php

    /**
     * Default feed
     */

    namespace Idno\Pages {

        use Idno\Common\Page;

        class Feed extends Page
        {

            function postContent()
            {
                $this->getContent();
            }

            function getContent()
            {

                $page = new Homepage();
                $page->setInput('_t', 'rss');
                $page->get();

            }

        }

    }