<?php

namespace Idno\Pages\File;

use Idno\Core\Idno;
use Idno\Entities\File;

class Mint extends \Idno\Common\Page {
    
    /**
     * Mint a new file, and if successful, return the new ID
     */
    public function postContent() {
        
        Idno::site()->template()->setTemplateType('json');
        
        $this->createGatekeeper();
        
        $name = $this->getInput('name'); // Name
        $type = $this->getInput('type'); // File type
        $size = $this->getInput('size'); // Size in bytes
        
        if (empty($name)) {
            throw new \RuntimeException(\Idno\Core\Idno::site()->language()->_('"name" (name of file) field needs to be supplied'));
        }
        
        if (empty($type)) {
            throw new \RuntimeException(\Idno\Core\Idno::site()->language()->_('"type" (mime type) field needs to be supplied'));
        }
        
        if (empty($size)) {
            throw new \RuntimeException(\Idno\Core\Idno::site()->language()->_('"size" (size in bytes) field needs to be supplied'));
        }
      
        // Get an upload URL
        $upload_url = Idno::site()->events()->triggerEvent('file/upload/getuploadurl', [
            'name' => $name,
            'type' => $type,
            'size' => $size
        ], Idno::site()->config()->getDisplayURL() . '/file/upload/' . $file->getID());
        
        // If no url, then remove file and error
        if (empty($upload_url)) {
            $file->delete();
            
            throw new \RuntimeException(\Idno\Core\Idno::site()->language()->_('There was a problem getting an upload URL for the newly minted file'));
        }
        
        // Mint an ID
        
        
        
        
        
        
        
        
        
        
        // Lets tell people about where to stick the data
        echo json_encode([
            'uploadUrl' => $upload_url
        ]);
    }
}
