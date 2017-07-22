<?php

namespace ConsolePlugins\PeriodicExecutionService {

    class Main extends \Idno\Common\ConsolePlugin {
        
        public static $run = true;
        
        
        
        public function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output) {
            
            define("KNOWN_EVENT_QUEUE_SERVICE", true);
            
            $eventqueue = \Idno\Core\Idno::site()->queue();
            if (!$eventqueue instanceof \Idno\Core\AsynchronousQueue) throw new \RuntimeException("Service can't run unless Known's queue is Asynchronous!");
        
            // Set up shutdown listener
            
            pcntl_signal(SIGTERM, function($signo) {
                \Idno\Core\Idno::site()->logging()->debug('SIGTERM received, shutting down.');
                \ConsolePlugins\EventQueueService\Main::$run = false;
                $output->writeln('Shutting down, this may take a little while...'); 
            });
            
            $output->writeln('Starting Periodic Execution Service');
            
            
            foreach (\Idno\Core\Cron::$events as $queue => $period) {
                
                $pid = pcntl_fork();
                if ($pid == -1) {
                     throw new \RuntimeException("Could not fork a new process");
                } else if ($pid) {
                    
                } else {
                    // Child
                    $output->writeln("Starting $queue queue processor.");
                    
                    while (self::$run) {
                    
                        $output->writeln("Triggering any events on the $queue queue...");
                        if ($events = \Idno\Entities\AsynchronousQueuedEvent::getPendingFromQueue($queue)) {
                            foreach ($events as $evnt) {
                                try {
                                    $eventqueue->dispatch($evnt);
                                } catch (\Exception $ex) {
                                    \Idno\Core\Idno::site()->logging()->error($ex->getMessage());
                                }
                            }
                        }

                        sleep($period);
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