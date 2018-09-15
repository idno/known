<?php

namespace ConsolePlugins\PeriodicExecutionService {

    class Main extends \Idno\Common\ConsolePlugin {
        
        public static $run = true;
        
        
        public function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output) {
            
            // Set up shutdown listener
            
            pcntl_signal(SIGTERM, function($signo) {
                \Idno\Core\Idno::site()->logging()->debug('SIGTERM received, shutting down.');
                \ConsolePlugins\EventQueueService\Main::$run = false;
                $output->writeln('Shutting down, this may take a little while...'); 
            });
            
            $output->writeln('Starting Periodic Execution Service');
            
            
            foreach (\Idno\Core\Cron::$events as $period => $time) {
                
                $pid = pcntl_fork();
                if ($pid == -1) {
                     throw new \RuntimeException("Could not fork a new process");
                } else if ($pid) {
                    
                } else {
                    // Child
                    $output->writeln("Starting $period queue processor.");
                    
                    try {
                        while (self::$run) {
        
                            while (self::$run) {

                                $output->writeln("Triggering cron/$period");
                                
                                \Idno\Core\Service::call('service/cron/' . $period);

                                sleep($time);
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