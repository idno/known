<?php

namespace ConsolePlugins\QueueManagement {

    class Main extends \Idno\Common\ConsolePlugin {
        
        public static $run = true;
                
        public function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output) {
            
            $queue = $input->getArgument('queue');
            $operation = $input->getArgument('operation');
            $id = $input->getArgument('id');
            
            
            switch ($operation) {
                case 'dispatch':
                    
                    $eventqueue = \Idno\Core\Idno::site()->queue();
                    if (!$eventqueue instanceof \Idno\Core\AsynchronousQueue) 
                        throw new \RuntimeException("Service can't run unless Known's queue is Asynchronous!");

                    if (empty($id))
                        throw new \RuntimeException("You need to specify an Event ID in order to dispatch.");
                    
                    $object = \Idno\Entities\AsynchronousQueuedEvent::getByID($id);
                    
                    try {
                                                
                        $result = $eventqueue->dispatch($object);
                    } catch (\Error $e) {
                        \Idno\Core\Idno::site()->logging()->error($e->getMessage());
                    }
                    
                    break;
                
                case 'list':
                default:
                    if ($events = \Idno\Core\Service::call('/service/queue/list/', [
                        'queue' => $queue
                    ])) {
                        
                        $output->writeln("Contents of '$queue' queue");
                        
                        foreach ($events->queue as $event) {
                            $output->writeln("\t $event");
                        }
                    }

                break;
            }
            
       }

        public function getCommand() {
            return 'event-queue-manage';
        }

        public function getDescription() {
            return 'Asynchronous event queue management tool';
        }

        public function getParameters() {
            return [
                new \Symfony\Component\Console\Input\InputArgument('queue', \Symfony\Component\Console\Input\InputArgument::REQUIRED, 'Queue to process - usually \'default\''),
                new \Symfony\Component\Console\Input\InputArgument('operation', \Symfony\Component\Console\Input\InputArgument::OPTIONAL, 'Operation to export - \'list\', \'dispatch\'', 'list'),
                new \Symfony\Component\Console\Input\InputArgument('id', \Symfony\Component\Console\Input\InputArgument::OPTIONAL, 'ID of an event'),
            ];
        }

    }

}