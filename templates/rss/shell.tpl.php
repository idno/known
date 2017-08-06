<?php

    $template = $this->formatShellVariables($vars);
    $vars = $template->vars;
    header('Content-type: application/rss+xml');
    unset($vars['body']);

    if (empty($vars['title'])) {
        if (!empty($vars['description'])) {
            $vars['title'] = implode(' ',array_slice(explode(' ', strip_tags($vars['description'])),0,10));
        } else {
            $vars['title'] = 'Known site';
        }
    }

    if (empty($vars['base_url'])) {
        $base_url = $this->getCurrentURLWithoutVar('_t');
    } else {
        $base_url = $this->getURLWithoutVar($vars['base_url'], '_t');
    }

    $page = new DOMDocument();
    $page->formatOutput = true;
    $rss = $page->createElement('rss');
    $rss->setAttribute('version', '2.0');
    $rss->setAttribute('xmlns:g', 'http://base.google.com/ns/1.0');
    $rss->setAttribute('xmlns:atom', 'http://www.w3.org/2005/Atom');
    $rss->setAttribute('xmlns:geo', 'http://www.w3.org/2003/01/geo/wgs84_pos#');
    $rss->setAttribute('xmlns:dc', 'http://purl.org/dc/elements/1.1/');
    $rss->setAttribute('xmlns:itunes', 'http://www.itunes.com/dtds/podcast-1.0.dtd');
    $rss->setAttribute('xmlns:wp', 'http://wordpress.org/export/1.2/');
    $channel = $page->createElement('channel');
    $channel->appendChild($page->createElement('title',  htmlspecialchars($vars['title'])));
    $channel->appendChild($page->createElement('itunes:author', htmlspecialchars($vars['title'])));
    if (!empty(\Idno\Core\Idno::site()->config()->description)) {
        $site_description = $page->createElement('description');
        if (empty($vars['nocdata'])) {
            $site_description->appendChild($page->createCDATASection(\Idno\Core\Idno::site()->config()->description));
        } else {
            //$site_description->appendChild((\Idno\Core\Idno::site()->config()->description));
            $site_description->textContent = \Idno\Core\Idno::site()->config()->getDescription();
        }
        $channel->appendChild($site_description);
        $site_description = $page->createElement('itunes:summary');
        if (empty($vars['nocdata'])) {
            $site_description->appendChild($page->createCDATASection(\Idno\Core\Idno::site()->config()->description));
        } else {
            //$site_description->appendChild((\Idno\Core\Idno::site()->config()->description));
            $site_description->textContent = \Idno\Core\Idno::site()->config()->getDescription();
        }
        $channel->appendChild($site_description);
    }
    $channel->appendChild($page->createElement('link', htmlspecialchars($base_url)));
    $channel->appendChild($page->createElement('language', $vars['lang']));
    
    if (!empty(\Idno\Core\Idno::site()->config()->itunes_category)) {
        $category = $page->createElement('itunes:category');
        $category->setAttribute('text', \Idno\Core\Idno::site()->config()->itunes_category);
        $channel->appendChild($category);
    } 
    if (!empty(\Idno\Core\Idno::site()->config()->itunes_explicit)) {
        $channel->appendChild($page->createElement('itunes:explicit', \Idno\Core\Idno::site()->config()->itunes_explicit));
    } else {
        $channel->appendChild($page->createElement('itunes:explicit', 'No'));
    }
    
    if (!empty(\Idno\Core\Idno::site()->config()->hub)) {
        $pubsub = $page->createElement('atom:link');
        $pubsub->setAttribute('href',\Idno\Core\Idno::site()->config()->hub);
        $pubsub->setAttribute('rel', 'hub');
        $channel->appendChild($pubsub);
    }
    $self = $page->createElement('atom:link');
    $self->setAttribute('href', $this->getCurrentURL());
    $self->setAttribute('rel','self');
    $self->setAttribute('type', 'application/rss+xml');
    $channel->appendChild($self);
    $channel->appendChild($page->createElement('generator','Known https://withknown.com'));

    // In case this isn't a feed page, find any objects
    if (empty($vars['items']) && !empty($vars['object'])) {
        $vars['items'] = array($vars['object']);
    }

    // If we have a feed, add the items
    if (!empty($vars['items'])) { 
        foreach($vars['items'] as $item) {
            if (!($item instanceof \Idno\Common\Entity)) {
                continue;
            }
            $title = $item->getTitle();
            if (empty($title)) {
                if ($description = $item->getShortDescription(5)) {
                    $title = $description;
                } else {
                    $title = 'New ' . $item->getContentTypeTitle();
                }
            }
            $rssItem = $page->createElement('item');
            $rssItem->appendChild($page->createElement('title', htmlspecialchars($title)));
            $rssItem->appendChild($page->createElement('link',$item->getSyndicationURL()));
            $rssItem->appendChild($page->createElement('guid',$item->getUUID()));
            $rssItem->appendChild($page->createElement('pubDate',date(DATE_RSS,$item->created)));

            // Needed for WP import into Known
            $rssItem->appendChild($page->createElement('wp:post_type', 'post'));
            $rssItem->appendChild($page->createElement('wp:status', 'publish'));
            
            $owner = $item->getOwner();
            if (!empty($owner)) {
                $rssItem->appendChild($page->createElement('dc:creator', "{$owner->title}"));
            } else {
                $rssItem->appendChild($page->createElement('dc:creator', "Deleted User"));
            }
            //$rssItem->appendChild($page->createElement('dc:creator', $owner->title));

            $description = $page->createElement('description');
            if (empty($vars['nocdata'])) {
                $description->appendChild($page->createCDATASection($item->draw(true)));
            } else {
                //$description->appendChild($page->create($item->draw(true)));
                //$description->textContent = $item->draw(true);
                $tpl = new \DOMDocument;
                $tpl->loadHtml($item->draw(true), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
                //$body->appendChild($dom->importNode($tpl->documentElement, TRUE));
                $description->appendChild($page->importNode($tpl->documentElement, true));
            }
            $rssItem->appendChild($description);
            if (!empty($item->lat) && !empty($item->long)) {
                $rssItem->appendChild($page->createElement('geo:lat', $item->lat));
                $rssItem->appendChild($page->createElement('geo:long', $item->long));
            }
            /*
             * Some feed readers choke on references to webmention, so this is removed for now
             *
                $webmentionItem = $page->createElement('atom:link');
                $webmentionItem->setAttribute('rel', 'webmention');
                $webmentionItem->setAttribute('href', \Idno\Core\Idno::site()->config()->getDisplayURL() . 'webmention/');
                $rssItem->appendChild($webmentionItem);
            */
            if ($attachments = $item->getAttachments()) {
                foreach($attachments as $attachment) {
                    $enclosureItem = $page->createElement('enclosure');
                    $enclosureItem->setAttribute('url', $attachment['url']);
                    $enclosureItem->setAttribute('type', $attachment['mime-type']);
                    $enclosureItem->setAttribute('length', $attachment['length']);
                    $rssItem->appendChild($enclosureItem);
                }
            }
            if ($tags = $item->getTags()) {
                foreach($tags as $tag) {
                    $tagItem = $page->createElement('category', $tag);
                    $rssItem->appendChild($tagItem);
                }
            }
            $channel->appendChild($rssItem);
        }
    } 
    
    // See if we have any annotations 
    else if (!empty($vars['annotations'])) {
        
        foreach ($vars['annotations'] as $annotation) {
            $title = "By: " . $annotation['owner_name'];
            
            $rssItem = $page->createElement('item');
            $rssItem->appendChild($page->createElement('title', htmlspecialchars($title)));
            $rssItem->appendChild($page->createElement('link', $annotation['permalink']));
            $rssItem->appendChild($page->createElement('guid', $annotation['permalink']));
            $rssItem->appendChild($page->createElement('pubDate',date(DATE_RSS, $annotation['time'])));

            $rssItem->appendChild($page->createElement('dc:creator', $annotation['owner_name']));
            
            //$rssItem->appendChild($page->createElement('dc:creator', $owner->title));

            $description = $page->createElement('description');
            if (empty($vars['nocdata'])) {
                $description->appendChild($page->createCDATASection(empty($annotation['content']) ? '' : $annotation['content']));
            } 
            $rssItem->appendChild($description);
            
            $channel->appendChild($rssItem);
        }
    }

    $rss->appendChild($channel);
    $page->appendChild($rss);
    echo $page->saveXML();
