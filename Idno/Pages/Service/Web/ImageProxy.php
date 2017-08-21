<?php

namespace Idno\Pages\Service\Web {

    class ImageProxy extends \Idno\Common\Page {
        
        protected function getCache() {
            
            // TODO: Is this the best cache to use? Perhaps file instead?
            return new \Idno\Caching\FilesystemCache();
            
        }
        
        /**
         * Delete any cached content 
         */
        public function deleteContent() {
            
            try {
                $cache = $this->getCache();

                if ($url = $this->getInput('url')) {

                    if ($url = \Idno\Core\Webservice::base64UrlDecode($url)) {

                        $cache->delete(sha1($url));
                        $cache->delete(sha1($url).'_meta');

                    } else {
                        throw new \RuntimeException("There was a problem decoding the url");
                    }
                    
                } else {
                    throw new \RuntimeException("No url specified");
                }
                
            } catch (\Exception $e) {
                
                \Idno\Core\Idno::site()->logging()->error($e->getMessage());
                $this->setResponse(500);
            }
        }
        
        public function getContent() {
            
            try {
                
                $cache = $this->getCache();
                //$url = $this->getInput('url')
                $url = $this->arguments[0];        
                
                if (!empty($url)) {

                    if ($url = \Idno\Core\Webservice::base64UrlDecode($url)) {

                        $meta = unserialize($cache->load(sha1($url).'_meta'));
                        if (!empty($meta)) {
                            // Found metadata
                            
                            $now = time();
                            $stored_ts = $meta['stored_ts'];
                            $this->lastModifiedGatekeeper($stored_ts);
                            header('Expires: ' . \Idno\Core\Time::timestampToRFC2616($meta['expires_ts']));
                            
                            // See if this has expired
                            if ($meta['expires_ts'] >= $now) {
                            
                                $this->setLastModifiedHeader($stored_ts);
                                
                                if ($meta['status'] == 200) {
                                    
                                    \Idno\Core\Idno::site()->logging()->debug("Returning cached image $url");
                                    
                                    $content = $cache->load(sha1($url));
                                    header('Content-Length: ' . strlen($content));
                                    header("Pragma: cache");
                                    header("Cache-Control: max-age=" . ($meta['expires_ts'] - time()));

                                    // Break long output to avoid an Apache performance bug
                                    $split_output = str_split($content, 1024);
                                    
                                    foreach ($split_output as $chunk)
                                        echo $chunk;
                                    
                                } else {
                                    $this->setResponse($meta['status']); // Previously there was a problem getting this image, lets return that status code and try again when the error expires
                                }
                            
                                exit;
                            } else {
                                \Idno\Core\Idno::site()->logging()->debug("Image $url has expired");
                            }
                        } 
                            
                        // Not found, or expired
                        \Idno\Core\Idno::site()->logging()->debug("Attempting to fetch $url");
                        
                        $meta = [];
                        $content = "";
                        $result = \Idno\Core\Webservice::file_get_contents_ex($url);
                        if ($result !== false) {
                        
                            \Idno\Core\Idno::site()->logging()->debug("Got something back, status code: {$result['response']}");
                            
                            // See if we have an expiry
                            $expires_ts = time() + (60*60*24); // Default: try again in one day.
                            if (preg_match('/Expires: (.*)/', $result['header'], $matches)) {
                                if (!empty($matches[1])) {

                                    \Idno\Core\Idno::site()->logging()->debug("Found upstream Expires time of " . $matches[1]);

                                    $expires_ts = strtotime($matches[1]);
                                    if (empty($expires_ts) || $expires_ts < time()) {
                                        \Idno\Core\Idno::site()->logging()->debug("Invalid Expires or expires in the past");
                                        $expires_ts = time() + (60*60*24); // Error (no valid time or time in the past), reverting back to default
                                    }
                                }
                            }
                            
                            $meta['stored_ts'] = time();
                            $meta['expires_ts'] = $expires_ts;
                            $meta['status'] = $result['response'];
                            
                            if ($result['response'] == 200) {
                                
                                \Idno\Core\Idno::site()->logging()->debug("Result should be valid content, so saving it.");
                                $content = $result['content'];
                                
                            }
                            
                        } else {
                            // We got absolutely nothing back, lets save nothing
                            \Idno\Core\Idno::site()->logging()->debug("Got absolutely nothing back from $url, faking it.");
                            $meta['status'] = 404;
                            $meta['stored_ts'] = time();
                            $meta['expires_ts'] = time() + (60*60*24); // Try again in one day.
                        }
                            
                        \Idno\Core\Idno::site()->logging()->debug("Storing " . strlen($content) . ' bytes of content.');
                        \Idno\Core\Idno::site()->logging()->debug('Meta: ' . print_r($meta, true));
                        
                        $cache->store(sha1($url), $content);
                        $cache->store(sha1($url).'_meta', serialize($meta));
                            
                        \Idno\Core\Idno::site()->logging()->debug("Returning image $url");
                                    
                        header('Content-Length: ' . strlen($content));
                        $this->setLastModifiedHeader($meta['stored_ts']);
                        header('Expires: ' . \Idno\Core\Time::timestampToRFC2616($meta['expires_ts']));
                        header("Pragma: cache");
                        header("Cache-Control: max-age=" . ($meta['expires_ts'] - time()));

                        // Break long output to avoid an Apache performance bug
                        $split_output = str_split($content, 1024);

                        foreach ($split_output as $chunk)
                            echo $chunk;
            
                        exit;
                        
                    } else {
                        throw new \RuntimeException("There was a problem decoding the url");
                    }
                    
                } else {
                    throw new \RuntimeException("No url specified");
                }
            
            } catch (\Exception $e) {
                
                \Idno\Core\Idno::site()->logging()->error($e->getMessage());
                $this->setResponse(500);
            }
            
        }
        
    }

}