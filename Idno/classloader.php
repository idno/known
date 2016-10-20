<?php

namespace Symfony\Component\ClassLoader {
    
    /**
     * Wrapper around ClassLoader.
     * Backwards compatibility shim for ClassLoader, implementing methods from UniversalClassLoader()
     */
    class UniversalClassLoader extends ClassLoader {
        
        public function registerNamespace($prefix, $paths) {
            return $this->addPrefix($prefix, $paths);
        }
    }
}

