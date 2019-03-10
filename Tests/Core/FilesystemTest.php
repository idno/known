<?php

namespace Tests\Core {

    class FilesystemTest extends \Tests\KnownTestCase {
        
        
        function testStoreContent() {
            
            $content = "this is a test content";
            $filename = get_class($this) . '_' . time();
            
            $filesystem = \Idno\Core\Idno::site()->filesystem();
            
            $id = $filesystem->storeContent($content, [
                'filename' => $filename,
                'meta_type' => 'text/plain'
            ]);
            
            
            $loaded = $filesystem->findOne($id);
            
            $this->assertNotEmpty($loaded);
            $this->assertEquals($content, $loaded->getBytes());
            
        }
    }

}
