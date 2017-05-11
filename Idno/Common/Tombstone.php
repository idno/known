<?php

/**
 * Tombstone for all entities.
 *
 * Stub to represent a tombstone for all entities (users, data, or otherwise). This prevents UUIDs from being reused.
 *
 * @package idno
 * @subpackage core
 */

namespace Idno\Common {

    class Tombstone extends Component {
        
        public $uuid;
        public $id;
        public $slug;
                        
        public function __construct($uuid, $id, $slug) {
            $this->uuid = $uuid;
            $this->id = $id;
            $this->slug = $slug;
            
            parent::__construct();
        }
    }

}