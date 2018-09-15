<?php

namespace ConsolePlugins\EventQueueService {

    class Main extends \Idno\Common\ConsolePlugin {
        
        public static $run = true;
        
        public function call($endpoint) {
            
            if (empty($endpoint))
                throw new \RuntimeException('No endpoint given');
            
            \Idno\Core\Idno::site()->logging()->debug("Calling $endpoint");
            
            $signature = \Idno\Core\Service::generateToken($endpoint);
                            
            if ($result = \Idno\Core\Webservice::get($endpoint, [], [
                'X-KNOWN-SERVICE-SIGNATURE: ' . $signature
            ])) {

                $error = $result['response'];
                $content = json_decode($result['content']);
                
                if ($error != 200) {
                                    
                    if (empty($content))
                        throw new \RuntimeException('Response from service endpoint was not json');
                    
                    if (!empty($content->exception->message))
                        throw new \RuntimeException($content->exception->message);
                    
                } else {
                    
                    // Response is ok
                    return $content;
                }
                
            } else {
                throw new \RuntimeException('No result from endpoint.');
            }

            return false;
            
        }
        
        public function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output) {
            
            $queue = $input->getArgument('queue');
            $pollperiod = (int)$input->getArgument('pollperiod');
            
            define("KNOWN_EVENT_QUEUE_SERVICE", true);
                    
            // Set up shutdown listener
            
            pcntl_signal(SIGTERM, function($signo) {
                \Idno\Core\Idno::site()->logging()->debug('SIGTERM received, shutting down.');
                \ConsolePlugins\EventQueueService\Main::$run = false;
                \Idno\Core\Idno::site()->logging()->info('Shutting down, this may take a little while...');
            });
            
            try {
                $pid = pcntl_fork();
                if ($pid == -1) {
                     throw new \RuntimeException("Could not fork a new process");
                } else if ($pid) {
                    \Idno\Core\Idno::site()->logging()->info('Starting GC thread for ' . $queue);

                    try {
                        while(self::$run) {
                            sleep(300);
                            
                            $this->call(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'service/queue/gc/');
                            
                        }
                    } catch (\Error $e) {
                        \Idno\Core\Idno::site()->logging()->error($e->getMessage());
                    }

                } else {
                    \Idno\Core\Idno::site()->logging()->info('Starting Asynchronous event processor on queue: ' . $queue. ", polling every $pollperiod seconds");

                    while (self::$run) {
                        
                        try {

                            while(self::$run) {

                                \Idno\Core\Idno::site()->logging()->debug('Polling queue...');
                                
                                if ($events = $this->call(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'service/queue/list/')) {
                                    foreach ($events->queue as $event) {
                                        try {
                                            \Idno\Core\Idno::site()->logging()->info("Dispatching event $event");
                                            $this->call(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'service/queue/dispatch/' . $event);
                                        } catch (\Exception $ex) {
                                            \Idno\Core\Idno::site()->logging()->error($ex->getMessage());
                                        }
                                    }
                                }

                                sleep($pollperiod);
                            }
                        
                        } catch (\Error $e) {
                            \Idno\Core\Idno::site()->logging()->error($e->getMessage());
                        }                    
                    }
                } 
            } catch (\Exception $e) {
                \Idno\Core\Idno::site()->logging()->error($e->getMessage());
            }
       }

        public function getCommand() {
            return 'service-event-queue';
        }

        public function getDescription() {
            return 'Begin the Asynchronous event queue dispatcher service';
        }

        public function getParameters() {
            return [
                new \Symfony\Component\Console\Input\InputArgument('queue', \Symfony\Component\Console\Input\InputArgument::OPTIONAL, 'Queue to process', 'default'),
                new \Symfony\Component\Console\Input\InputArgument('pollperiod', \Symfony\Component\Console\Input\InputArgument::OPTIONAL, 'How often should the service poll the queue', 20),
            ];
        }

    }

}