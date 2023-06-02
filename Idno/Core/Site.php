<?php

namespace Idno\Core;

use Idno\Common\Entity;
use Idno\Core\Idno;

class Site extends Entity
{

    private $collection = 'site';
    static $retrieve_collection = 'site';

    function getCollection()
    {
        return $this->collection;
    }

    public function uuid() : ? string
    {

        if (!empty($this->_id)) {
            return $this->_id;
        }

        return null;
    }
}
