<?php

    header('Content-type: application/rss+xml');
    unset($vars['body']);

    if (empty($vars['title']) && !empty($vars['description'])) {
        $vars['title'] = implode(' ',array_slice(explode(' ', strip_tags($vars['description'])),0,10));
    }

    $page = new DOMDocument();
    $page->formatOutput = true;
    $rss = $page->createElement('rss');
    $rss->setAttribute('version', '2.0');
    $rss->setAttribute('xmlns:g', 'http://base.google.com/ns/1.0');
    $rss->setAttribute('xmlns:atom', 'http://www.w3.org/2005/Atom');
    $rss->setAttribute('xmlns:geo', 'http://www.w3.org/2003/01/geo/wgs84_pos#');
    $channel = $page->createElement('channel');
    $channel->appendChild($page->createElement('title',$vars['title']));
    $channel->appendChild($page->createElement('description',$vars['description']));
    $channel->appendChild($page->createElement('link',$this->getCurrentURLWithoutVar('_t')));
    if (!empty(\Idno\Core\site()->config()->hub)) {
        $pubsub = $page->createElement('atom:link');
        $pubsub->setAttribute('href',\Idno\Core\site()->config()->hub);
        $pubsub->setAttribute('rel', 'hub');
        $channel->appendChild($pubsub);
    }
    $self = $page->createElement('atom:link');
    $self->setAttribute('href', $this->getCurrentURL());
    $self->setAttribute('rel','self');
    $self->setAttribute('type', 'application/rss+xml');
    $channel->appendChild($self);
    $channel->appendChild($page->createElement('generator','Known http://withknown.com'));

    // In case this isn't a feed page, find any objects
    if (empty($vars['items']) && !empty($vars['object'])) {
        $vars['items'] = array($vars['object']);
    }

    // If we have a feed, add the items
    if (!empty($vars['items'])) {
        foreach($vars['items'] as $item) {
            if ($item instanceof \Idno\Entities\ActivityStreamPost) {
                $item = $item->getObject();
            }
            $rssItem = $page->createElement('item');
            if ($title = $item->getTitle()) {
                $rssItem->appendChild($page->createElement('title',$item->getTitle()));
            }
            $rssItem->appendChild($page->createElement('link',$item->getURL()));
            $rssItem->appendChild($page->createElement('guid',$item->getUUID()));
            $rssItem->appendChild($page->createElement('pubDate',date(DATE_RSS,$item->created)));
            $description = $page->createElement('description');
            $description->appendChild($page->createCDATASection($item->draw()));
            $rssItem->appendChild($description);
            if (!empty($item->lat) && !empty($item->long)) {
                $rssItem->appendChild($page->createElement('geo:lat', $item->lat));
                $rssItem->appendChild($page->createElement('geo:long', $item->long));
            }
            $webmentionItem = $page->createElement('atom:link');
            $webmentionItem->setAttribute('rel', 'webmention');
            $webmentionItem->setAttribute('href', \Idno\Core\site()->config()->getURL() . 'webmention/');
            $rssItem->appendChild($webmentionItem);
            if ($attachments = $item->getAttachments()) {
                foreach($attachments as $attachment) {
                    $enclosureItem = $page->createElement('enclosure');
                    $enclosureItem->setAttribute('url', $attachment['url']);
                    $enclosureItem->setAttribute('type', $attachment['mime-type']);
                    $enclosureItem->setAttribute('length', $attachment['length']);
                    $rssItem->appendChild($enclosureItem);
                }
            }
            $channel->appendChild($rssItem);
        }
    }

    $rss->appendChild($channel);
    $page->appendChild($rss);
    echo $page->saveXML();
