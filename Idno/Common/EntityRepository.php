<?php

/**
 * Entity Repository
 * 
 * Extracts database operations from Entity.php for better separation of concerns
 *
 * @package    idno
 * @subpackage common
 */

namespace Idno\Common {

    use Idno\Core\Idno;

    class EntityRepository
    {
        private $collection;
        private $entityClass;

        public function __construct(string $collection, string $entityClass)
        {
            $this->collection = $collection;
            $this->entityClass = $entityClass;
        }

        /**
         * Find entity by UUID
         */
        public function findByUUID(string $uuid): ?Entity
        {
            if (empty($uuid)) {
                return null;
            }

            $result = Idno::site()->db()->getObject($this->collection, $uuid);
            if ($result) {
                return $this->hydrateEntity($result);
            }

            return null;
        }

        /**
         * Find one entity by criteria
         */
        public function findOne(array $criteria = []): ?Entity
        {
            $result = Idno::site()->db()->getAnyRecord($this->collection, $criteria);
            if ($result) {
                return $this->hydrateEntity($result);
            }

            return null;
        }

        /**
         * Find multiple entities by criteria
         */
        public function find(array $criteria = [], array $options = []): array
        {
            $results = Idno::site()->db()->getRecords($this->collection, $criteria, $options);
            
            $entities = [];
            if (!empty($results['items'])) {
                foreach ($results['items'] as $result) {
                    $entity = $this->hydrateEntity($result);
                    if ($entity) {
                        $entities[] = $entity;
                    }
                }
            }

            return $entities;
        }

        /**
         * Find entity by slug
         */
        public function findBySlug(string $slug): ?Entity
        {
            if (empty($slug)) {
                return null;
            }

            return $this->findOne(['slug' => $slug]);
        }

        /**
         * Find entity by URL
         */
        public function findByUrl(string $url): ?Entity
        {
            if (empty($url)) {
                return null;
            }

            // Try exact URL match first
            $entity = $this->findOne(['url' => $url]);
            if ($entity) {
                return $entity;
            }

            // Try canonical URL
            $entity = $this->findOne(['canonical' => $url]);
            if ($entity) {
                return $entity;
            }

            // Try slug from URL
            $bits = explode('/', $url);
            $slug = end($bits);
            return $this->findBySlug($slug);
        }

        /**
         * Save entity
         */
        public function save(Entity $entity): bool
        {
            $data = $entity->toArray();
            
            // Set timestamps
            if (empty($data['created'])) {
                $data['created'] = time();
            }
            $data['updated'] = time();

            $result = Idno::site()->db()->saveRecord($this->collection, $data);
            
            if ($result) {
                // Update entity with new data
                if (isset($result['_id'])) {
                    $entity->_id = $result['_id'];
                }
                return true;
            }

            return false;
        }

        /**
         * Delete entity
         */
        public function delete(Entity $entity): bool
        {
            if (empty($entity->getUUID())) {
                return false;
            }

            return Idno::site()->db()->removeRecord($this->collection, $entity->getUUID());
        }

        /**
         * Count entities matching criteria
         */
        public function count(array $criteria = []): int
        {
            return Idno::site()->db()->countRecords($this->collection, $criteria);
        }

        /**
         * Find entities with pagination
         */
        public function findWithPagination(array $criteria = [], int $limit = 10, int $offset = 0): array
        {
            $options = [
                'limit' => $limit,
                'offset' => $offset
            ];

            $results = $this->find($criteria, $options);
            $total = $this->count($criteria);

            return [
                'items' => $results,
                'total' => $total,
                'limit' => $limit,
                'offset' => $offset,
                'has_more' => ($offset + $limit) < $total
            ];
        }

        /**
         * Find entities by owner
         */
        public function findByOwner(string $ownerUuid, array $additionalCriteria = []): array
        {
            $criteria = array_merge(['owner' => $ownerUuid], $additionalCriteria);
            return $this->find($criteria);
        }

        /**
         * Find public entities
         */
        public function findPublic(array $additionalCriteria = []): array
        {
            $criteria = array_merge(['access' => 'PUBLIC'], $additionalCriteria);
            return $this->find($criteria);
        }

        /**
         * Hydrate array data into entity object
         */
        private function hydrateEntity(array $data): ?Entity
        {
            try {
                $entity = new $this->entityClass();
                
                // Set all properties from data
                foreach ($data as $key => $value) {
                    $entity->$key = $value;
                }

                return $entity;
            } catch (\Exception $e) {
                Idno::site()->logging()->error("Failed to hydrate entity: " . $e->getMessage());
                return null;
            }
        }

        /**
         * Get collection name
         */
        public function getCollection(): string
        {
            return $this->collection;
        }

        /**
         * Set collection name
         */
        public function setCollection(string $collection): void
        {
            $this->collection = $collection;
        }
    }
}