<?php

namespace Idno\Common;

/**
 * JSON+LD Serialisable interface
 */
interface JSONLDSerialisable {

    /**
     * Serialise a object to a Structured Data schema.
     * @param array $params Optional params
     * @return array
     */
    public function jsonLDSerialise(array $params = []);
}
