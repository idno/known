<?php

namespace IdnoPlugins\StandardSiteSync;

use Idno\Common\Plugin;
use Idno\Entities\User;

/**
 * StandardSiteSync plugin for Known.
 *
 * Syncs content to standard.site records on an AT Protocol PDS.
 * Supports OAuth authentication, automatic sync of new/edited content,
 * and one-click backfill of all existing posts.
 */
class Main extends Plugin
{

    function registerTranslations()
    {
    }

    function registerPages()
    {
        // Admin settings
        \Idno\Core\Idno::site()->routes()->addRoute('/admin/standardsitesync/?', '\IdnoPlugins\StandardSiteSync\Pages\Admin');

        // OAuth callback
        \Idno\Core\Idno::site()->routes()->addRoute('/admin/standardsitesync/callback/?', '\IdnoPlugins\StandardSiteSync\Pages\OAuthCallback');

        // OAuth disconnect
        \Idno\Core\Idno::site()->routes()->addRoute('/admin/standardsitesync/disconnect/?', '\IdnoPlugins\StandardSiteSync\Pages\Disconnect');

        // Backfill action
        \Idno\Core\Idno::site()->routes()->addRoute('/admin/standardsitesync/backfill/?', '\IdnoPlugins\StandardSiteSync\Pages\Backfill');

        // Client metadata endpoint (public, required by AT Protocol OAuth)
        \Idno\Core\Idno::site()->routes()->addRoute('/standardsitesync/client-metadata\\.json', '\IdnoPlugins\StandardSiteSync\Pages\ClientMetadata', true);

        // .well-known/site.standard.publication endpoint (public)
        \Idno\Core\Idno::site()->routes()->addRoute('/.well-known/site\\.standard\\.publication', '\IdnoPlugins\StandardSiteSync\Pages\WellKnownPublication', true);

        // Admin menu item
        \Idno\Core\Idno::site()->template()->extendTemplate('admin/menu/items', 'standardsitesync/admin/menu');
    }

    function registerEventHooks()
    {

        // -------------------------------------------------------
        // Sync new content to PDS on save
        // -------------------------------------------------------
        \Idno\Core\Idno::site()->events()->addListener('saved', function (\Idno\Core\Event $event) {

            $eventdata = $event->data();
            $object = $eventdata['object'];

            if (!$this->shouldSync($object)) return;

            $user = $object->getOwner();
            if (!$user || !($user instanceof User)) return;

            $session = $this->getATProtoSession();
            if (empty($session)) return;

            $this->syncEntity($object, $session);
        });

        // -------------------------------------------------------
        // Sync updates to PDS
        // -------------------------------------------------------
        \Idno\Core\Idno::site()->events()->addListener('updated', function (\Idno\Core\Event $event) {

            $eventdata = $event->data();
            $object = $eventdata['object'];

            if (!$this->shouldSync($object)) return;

            $user = $object->getOwner();
            if (!$user || !($user instanceof User)) return;

            $session = $this->getATProtoSession();
            if (empty($session)) return;

            $this->syncEntity($object, $session);
        });

        // -------------------------------------------------------
        // Delete from PDS when content is deleted
        // -------------------------------------------------------
        \Idno\Core\Idno::site()->events()->addListener('delete', function (\Idno\Core\Event $event) {

            $eventdata = $event->data();
            $object = $eventdata['object'];

            if (!$this->shouldSync($object)) return;

            $user = $object->getOwner();
            if (!$user || !($user instanceof User)) return;

            $session = $this->getATProtoSession();
            if (empty($session)) return;

            try {
                $client = new ATProtoClient($session);
                $client->deleteDocument($object);
                $this->saveATProtoSession($client->getSession());
                \Idno\Core\Idno::site()->logging()->debug('StandardSiteSync: Deleted document for entity ' . $object->getID());
            } catch (\Exception $e) {
                \Idno\Core\Idno::site()->logging()->error('StandardSiteSync: Error deleting document: ' . $e->getMessage());
            }
        });

        // -------------------------------------------------------
        // Handle queued backfill events
        // -------------------------------------------------------
        \Idno\Core\Idno::site()->events()->addListener('standardsitesync/backfill', function (\Idno\Core\Event $event) {

            $data = $event->data();

            if (empty($data['entity_id'])) {
                \Idno\Core\Idno::site()->logging()->warning('StandardSiteSync: Backfill event missing entity_id');
                return;
            }

            $entity = \Idno\Common\Entity::getByID($data['entity_id']);
            if (!$entity) {
                \Idno\Core\Idno::site()->logging()->warning('StandardSiteSync: Entity not found for backfill: ' . $data['entity_id']);
                return;
            }

            $session = $this->getATProtoSession();
            if (empty($session)) {
                \Idno\Core\Idno::site()->logging()->warning('StandardSiteSync: No AT Protocol session for backfill');
                return;
            }

            $this->syncEntity($entity, $session, true);
        });
    }

    /**
     * Determine whether an entity should be synced to the PDS.
     */
    private function shouldSync($object): bool
    {
        // Skip non-content entities
        if ($object instanceof User) return false;
        if ($object instanceof \Idno\Entities\AsynchronousQueuedEvent) return false;

        // Must be a content entity
        if (!($object instanceof \Idno\Common\Entity)) return false;

        // Only sync public, published content
        if (method_exists($object, 'isPublic') && !$object->isPublic()) return false;
        if (method_exists($object, 'getPublishStatus') && $object->getPublishStatus() !== 'published') return false;

        // Must have a real content type
        $type = $object->getActivityStreamsObjectType();
        if (empty($type) || $type === 'entity') return false;

        // Check if sync is enabled
        if (empty(\Idno\Core\Idno::site()->config()->standardsitesync_enabled)) return false;

        return true;
    }

    /**
     * Sync a single entity to the PDS.
     *
     * @param \Idno\Common\Entity $entity
     * @param array               $session AT Protocol session data
     * @param bool                $skipExisting If true, skip entities already synced
     */
    private function syncEntity(\Idno\Common\Entity $entity, array $session, bool $skipExisting = false): void
    {
        try {
            $client = new ATProtoClient($session);

            if ($skipExisting && $client->documentExists($entity)) {
                \Idno\Core\Idno::site()->logging()->debug('StandardSiteSync: Skipping already-synced entity ' . $entity->getID());
                $this->saveATProtoSession($client->getSession());
                return;
            }

            $result = $client->putDocument($entity);
            $this->saveATProtoSession($client->getSession());

            \Idno\Core\Idno::site()->logging()->debug('StandardSiteSync: Synced entity ' . $entity->getID() . ' -> ' . ($result['uri'] ?? 'unknown'));
        } catch (\Exception $e) {
            \Idno\Core\Idno::site()->logging()->error('StandardSiteSync: Error syncing entity ' . $entity->getID() . ': ' . $e->getMessage());
        }
    }

    /**
     * Get the stored AT Protocol session from site config.
     */
    public function getATProtoSession(): array
    {
        return \Idno\Core\Idno::site()->config()->standardsitesync_session ?? [];
    }

    /**
     * Save the AT Protocol session to site config.
     */
    public function saveATProtoSession(array $session): void
    {
        \Idno\Core\Idno::site()->config()->standardsitesync_session = $session;
        \Idno\Core\Idno::site()->config()->save();
    }

    /**
     * Check if we have an active AT Protocol connection.
     */
    public function isConnected(): bool
    {
        $session = $this->getATProtoSession();
        return !empty($session['access_token']) && !empty($session['did']);
    }
}
