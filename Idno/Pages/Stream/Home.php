<?php

    namespace Idno\Pages\Stream {

        use Idno\Entities\Reader\FeedItem;

        class Home extends \Idno\Common\Page
        {

            function getContent()
            {

                if ($items = FeedItem::get()) {

                    $t = \Idno\Core\Idno::site()->template();
                    $t->__(array(
                        'title' => 'Stream',
                        'body'  => $t->__(array(
                            'items' => $items
                        ))->draw('stream/home')
                    ))->drawPage();

                }

            }

        }

    }