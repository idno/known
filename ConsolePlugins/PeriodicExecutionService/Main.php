<?php

namespace ConsolePlugins\PeriodicExecutionService {

    class Main extends \Idno\Common\ConsolePlugin {
        
        public static $run = true;
        
        public $cron;
        
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
            
            define("KNOWN_EVENT_QUEUE_SERVICE", true);
            
            // Initialise cron
            $this->cron = new Cron();
            
            $eventqueue = \Idno\Core\Idno::site()->queue();
            if (!$eventqueue instanceof \Idno\Core\AsynchronousQueue) throw new \RuntimeException("Service can't run unless Known's queue is Asynchronous!");
        
            // Set up shutdown listener
            
            pcntl_signal(SIGTERM, function($signo) {
                \Idno\Core\Idno::site()->logging()->debug('SIGTERM received, shutting down.');
                \ConsolePlugins\EventQueueService\Main::$run = false;
                $output->writeln('Shutting down, this may take a little while...'); 
            });
            
            $output->writeln('Starting Periodic Execution Service');
            
            
            foreach (Cron::$events as $queue => $period) {
                
                $pid = pcntl_fork();
                if ($pid == -1) {
                     throw new \RuntimeException("Could not fork a new process");
                } else if ($pid) {
                    
                } else {
                    // Child
                    $output->writeln("Starting $queue queue processor.");
                    
                    
                    try {
                        while (self::$run) {
                        
                            $output->writeln("Opening new DB connection");
                            $this->reinitialiseDB();

                            while (self::$run) {

                                $output->writeln("Triggering any events on the $queue queue...");
                                if ($events = \Idno\Entities\AsynchronousQueuedEvent::getPendingFromQueue($queue)) {

                                    // Dispatch one, delete the rest (avoid duplicates)
                                    try {
                                        $eventqueue->dispatch($events[0]);
                                    } catch (\Exception $ex) {
                                        \Idno\Core\Idno::site()->logging()->error($ex->getMessage());
                                    }

                                    foreach ($events as $evnt) {
                                        try {
                                            if (!empty($evnt))
                                                $evnt->delete();
                                        } catch (\Exception $ex) {
                                            \Idno\Core\Idno::site()->logging()->error($ex->getMessage());
                                        }
                                    }
                                }

                                sleep($period);
                                $eventqueue->gc(300, $queue);
                            }
                        }
                    } catch (\Error $e) {
                        \Idno\Core\Idno::site()->logging()->error($e->getMessage());
                    }
                }
            
            }
            
            pcntl_wait($status);
            
       }

        public function getCommand() {
            return 'service-cron';
        }

        public function getDescription() {
            return 'Begin the cron service';
        }

        public function getParameters() {
            return [
            ];
        }

    }

}