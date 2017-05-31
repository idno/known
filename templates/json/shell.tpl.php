<?php

    header('Content-type: application/json');
    header("Access-Control-Allow-Origin: *");

    unset($vars['body']);

    $json = array();
    $json['version'] = "https://jsonfeed.org/version/1";

    if (empty($vars['title'])) {
        if (!empty($vars['description'])) {
            $json['title'] = implode(' ',array_slice(explode(' ', strip_tags($vars['description'])),0,10));
        } else {
            $json['title'] = 'Known site';
        }
    }

    if (empty($vars['base_url'])) {
        $json['home_page_url'] = $this->getCurrentURLWithoutVar('_t');
    } else {
        $json['home_page_url'] = $this->getURLWithoutVar($vars['base_url'], '_t');
    }

    $json['feed_url'] = $this->getCurrentURL();

    if (!empty(\Idno\Core\Idno::site()->config()->description)) {
        $json['description'] = \Idno\Core\Idno::site()->config()->getDescription();
    }

    if (!empty(\Idno\Core\Idno::site()->config()->hub)) {
        $json['hubs'] = array();
        $hub = array();
        $hub['type'] = 'WebSub';
        $hub['url'] = \Idno\Core\Idno::site()->config()->hub;
        array_push($json['hubs'], $hub);
    }

    // In case this isn't a feed page, find any objects
    if (empty($vars['items']) && !empty($vars['object'])) {
        $vars['items'] = array($vars['object']);
    }

    // If we have a feed, add the items
    $json['items'] = array();
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
            $feedItem = array();
            $feedItem['title'] = strip_tags($title);
            $feedItem['url'] = $item->getSyndicationURL();
            $feedItem['id'] = $item->getUUID();
            $feedItem['date_published'] = date('c', $item->created);

            $owner = $item->getOwner();
            if (!empty($owner)) {
                $feedItem['author'] = array();
                $feedItem['author']['name'] = $item->getAuthorName();
                $feedItem['author']['url'] = $item->getAuthorURL();
                $feedItem['author']['avatar'] = $owner->getIcon();
            }

            $feedItem['content_html'] = $item->getBody();

            $feedItem['_indieweb'] = array();
            if (method_exists($item, 'getMetadataForFeed')) {
                $feedItem['_indieweb'] = $item->getMetadataForFeed();
            } else if ($item instanceof \IdnoPlugins\Like\Like) {
                $feedItem['external_url'] = $item->getBody();
                unset($feedItem['content_text']);
                if (!empty($item->repostof)) {
                    $feedItem['_indieweb']['repost-of'] = $item->repostof;
                    $feedItem['_indieweb']['type'] = 'repost';
                } else if (!empty($item->likeof)) {
                    $feedItem['_indieweb']['like-of'] = $item->likeof;
                    $feedItem['_indieweb']['type'] = 'like';
                } else if (!empty($item->bookmarkof)) {
                    $feedItem['_indieweb']['bookmark-of'] = $item->bookmarkof;
                    $feedItem['_indieweb']['type'] = 'bookmark';
                } else {
                    $feedItem['_indieweb']['bookmark-of'] = $item->getBody();
                    $feedItem['_indieweb']['type'] = 'bookmark';
                }
            } else if ($item instanceof \IdnoPlugins\Status\Reply) {
                $feedItem['external_url'] = $item->inreplyto;
                $feedItem['_indieweb']['type'] = 'reply';
                $feedItem['_indieweb']['in-reply-to'] = $item->inreplyto;
            } else if ($item instanceof \IdnoPlugins\Status\Status) {
                $feedItem['_indieweb']['type'] = 'status';
            } else if ($item instanceof \IdnoPlugins\Checkin\Checkin) {
                $feedItem['_indieweb']['type'] = 'checkin';
                $feedItem['_indieweb']['lat'] = $item->lat;
                $feedItem['_indieweb']['long'] = $item->long;
                $feedItem['_indieweb']['placename'] = $item->placename;
                $feedItem['_indieweb']['address'] = $item->address;
                $feedItem['content_text'] = $item->getTitle();
                unset($feedItem['content_html']);
            } else if ($item instanceof \IdnoPlugins\Photo\Photo) {
                $feedItem['_indieweb']['type'] = 'photo';
            } else if ($item instanceof \IdnoPlugins\Text\Entry) {
                $feedItem['_indieweb']['type'] = 'entry';
            } else {
                $type = $item->getContentTypeTitle();
                $feedItem['_indieweb']['type'] = strtolower($type);
                $feedItem['title'] = $type . ": " . $feedItem['title'];
                $feedItem['content_html'] = $item->draw();
            }

            if ($attachments = $item->getAttachments()) {
                $feedItem['attachments'] = array();
                foreach($attachments as $attachment) {
                    $attachmentItem = array();
                    $attachmentItem['url'] = $attachment['url'];
                    $attachmentItem['mime_type'] = $attachment['mime-type'];
                    $attachmentItem['size_in_bytes'] = $attachment['length'];
                    array_push($feedItem['attachments'], $attachmentItem);
                }
            }

            if ($tags = $item->getTags()) {
                $feedItem['tags'] = $tags;
            }

            array_push($json['items'], $feedItem);
        }
    }

    echo json_encode($json, JSON_UNESCAPED_SLASHES);
