<?php

namespace ConsolePlugins\EventQueueService {

    class Main extends \Idno\Common\ConsolePlugin {
        
        public static $run = true;
        
        /**
         * Each fork needs its own connection to the DB, otherwise it shares the parent's ... which lies madness.
         */
        private function reinitialiseDB() {
            switch (trim(strtolower(\Idno\Core\Idno::site()->config()->database))) {
                case 'mongo':
                case 'mongodb':
                    \Idno\Core\Idno::site()->db = new \Idno\Data\Mongo();
                    break;
                case 'mysql':
                    \Idno\Core\Idno::site()->db = new \Idno\Data\MySQL();
                    break;
                case 'beanstalk-mysql': // A special instance of MYSQL designed for use with Amazon Elastic Beanstalk
                    \Idno\Core\Idno::site()->db = new \Idno\Data\MySQL();
                    break;
                default:
                    \Idno\Core\Idno::site()->db = $this->componentFactory(\Idno\Core\Idno::site()->config()->database, "Idno\\Core\\DataConcierge", "Idno\\Data\\", "Idno\\Data\\MySQL");
                    break;
            }
        }
        
        public function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output) {
            
            $queue = $input->getArgument('queue');
            $pollperiod = (int)$input->getArgument('pollperiod');
            
            define("KNOWN_EVENT_QUEUE_SERVICE", true);
            
            $eventqueue = \Idno\Core\Idno::site()->queue();
            if (!$eventqueue instanceof \Idno\Core\AsynchronousQueue) throw new \RuntimeException("Service can't run unless Known's queue is Asynchronous!");
        
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
                            $eventqueue->gc(300, $queue);
                        }
                    } catch (\Error $e) {
                        \Idno\Core\Idno::site()->logging()->error($e->getMessage());
                    }

                } else {
                    \Idno\Core\Idno::site()->logging()->info('Starting Asynchronous event processor on queue: ' . $queue. ", polling every $pollperiod seconds");

                    while (self::$run) {
                        
                        try {
                            // Reinitialise DB
                            $this->reinitialiseDB();

                            while(self::$run) {

                                \Idno\Core\Idno::site()->logging()->debug('Polling queue...');

                                if ($events = \Idno\Entities\AsynchronousQueuedEvent::getPendingFromQueue($queue)) {
                                    foreach ($events as $evnt) {
                                        try {
                                            $eventqueue->dispatch($evnt);
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