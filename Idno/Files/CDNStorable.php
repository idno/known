<?php


namespace Idno\Files;

/**
 * Interface defining a file that can be retrieved from a CDN.
 */
interface CDNStorable {
    
    /**
     * Get a URL for a direct link to the CDN stored file.
     */
    public function getCDNStoredURL();
}
