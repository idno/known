<?php

namespace IdnoPlugins\StandardSiteSync\Pages;

use Idno\Common\Page;
use Idno\Common\Entity;
use IdnoPlugins\StandardSiteSync\ATProtoClient;

/**
 * Backfill all existing content to the AT Protocol PDS.
 * Route: /admin/standardsitesync/backfill
 */
class Backfill extends Page
{

    function postContent()
    {
        $this->adminGatekeeper();

        $session = \Idno\Core\Idno::site()->config()->standardsitesync_session ?? [];
        if (empty($session['access_token']) || empty($session['did'])) {
            \Idno\Core\Idno::site()->session()->addErrorMessage('Not connected to an AT Protocol PDS.');
            $this->forward(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'admin/standardsitesync/');
            return;
        }

        // Ensure the publication record exists
        try {
            $client = new ATProtoClient($session);
            $client->putPublication();
            \Idno\Core\Idno::site()->config()->standardsitesync_session = $client->getSession();
            \Idno\Core\Idno::site()->config()->save();
        } catch (\Exception $e) {
            \Idno\Core\Idno::site()->logging()->error('StandardSiteSync: Failed to create publication for backfill: ' . $e->getMessage());
            \Idno\Core\Idno::site()->session()->addErrorMessage('Failed to create publication record: ' . $e->getMessage());
            $this->forward(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'admin/standardsitesync/');
            return;
        }

        // Fetch all published, public content entities
        // Exclude non-content entity types
        $entities = Entity::get(
            [
                'publish_status' => 'published',
            ],
            [],
            PHP_INT_MAX,
            0
        );

        if (empty($entities)) {
            \Idno\Core\Idno::site()->session()->addMessage('No content found to backfill.');
            $this->forward(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'admin/standardsitesync/');
            return;
        }

        $queue = \Idno\Core\Idno::site()->queue();
        $useAsync = ($queue instanceof \Idno\Core\AsynchronousQueue);
        $count = 0;

        foreach ($entities as $entity) {
            // Skip non-content entities
            if ($entity instanceof \Idno\Entities\User) continue;
            if ($entity instanceof \Idno\Entities\AsynchronousQueuedEvent) continue;

            $type = $entity->getActivityStreamsObjectType();
            if (empty($type) || $type === 'entity') continue;

            // Only sync public content
            if (method_exists($entity, 'isPublic') && !$entity->isPublic()) continue;

            if ($useAsync) {
                // Queue each entity for async backfill
                $queue->enqueue('default', 'standardsitesync/backfill', [
                    'entity_id' => $entity->getID(),
                ]);
            } else {
                // Synchronous fallback: sync directly
                try {
                    $client = new ATProtoClient(
                        \Idno\Core\Idno::site()->config()->standardsitesync_session ?? []
                    );

                    if (!$client->documentExists($entity)) {
                        $client->putDocument($entity);
                        \Idno\Core\Idno::site()->config()->standardsitesync_session = $client->getSession();
                        \Idno\Core\Idno::site()->config()->save();
                    }
                } catch (\Exception $e) {
                    \Idno\Core\Idno::site()->logging()->error(
                        'StandardSiteSync: Backfill error for entity ' . $entity->getID() . ': ' . $e->getMessage()
                    );
                }
            }

            $count++;
        }

        if ($useAsync) {
            \Idno\Core\Idno::site()->session()->addMessage(
                'Backfill started: ' . $count . ' posts queued for sync to your PDS. They will be processed in the background.'
            );
        } else {
            \Idno\Core\Idno::site()->session()->addMessage(
                'Backfill complete: ' . $count . ' posts processed.'
            );
        }

        $this->forward(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'admin/standardsitesync/');
    }
}
