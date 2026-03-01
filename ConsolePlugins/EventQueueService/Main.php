<?php

namespace ConsolePlugins\EventQueueService {

    use Idno\Core\Idno;
    use Idno\Core\Service;
    use Idno\Entities\AsynchronousQueuedEvent;

    class Main extends \Idno\Common\ConsolePlugin
    {

        public function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output)
        {
            $queue = $input->getOption('queue');
            $interval = (int) $input->getOption('interval');

            $eventqueue = Idno::site()->queue();
            if (!$eventqueue instanceof \Idno\Core\AsynchronousQueue) {
                $output->writeln('<error>The event queue is not set to AsynchronousQueue. Set event_queue = \'AsynchronousQueue\' in config.ini.</error>');
                return 1;
            }

            $once = $input->getOption('once');

            if ($once) {
                $output->writeln("Processing pending events from queue '$queue' (single run)...");
            } else {
                $output->writeln("Starting event queue worker for queue '$queue' (polling every {$interval}s)...");
                $output->writeln('Press Ctrl+C to stop.');
            }

            do {
                // Long-running workers can outlive MySQL's wait_timeout,
                // causing "MySQL server has gone away" on the next query.
                // Re-establish the connection if it has dropped.
                Idno::site()->db()->reconnectIfNeeded();

                $pending = AsynchronousQueuedEvent::getPendingFromQueue($queue, 50, 0);

                if (!empty($pending)) {
                    foreach ($pending as $event) {
                        $eventId = $event->getID();
                        $output->writeln("[" . date('r') . "] Dispatching event $eventId: {$event->event}");

                        try {
                            $result = Service::call("/service/queue/dispatch/$eventId");

                            if (!empty($result->complete)) {
                                $output->writeln("[" . date('r') . "] Event $eventId completed.");
                            } else {
                                $output->writeln("<comment>[" . date('r') . "] Event $eventId dispatched but may not have completed.</comment>");
                            }
                        } catch (\Exception $e) {
                            $output->writeln("<error>[" . date('r') . "] Error dispatching event $eventId: " . $e->getMessage() . "</error>");
                        }
                    }

                    // Run garbage collection after processing a batch
                    try {
                        Service::call('/service/queue/gc', ['queue' => $queue]);
                    } catch (\Exception $e) {
                        $output->writeln("<comment>[" . date('r') . "] GC warning: " . $e->getMessage() . "</comment>");
                    }
                }

                if (!$once) {
                    sleep($interval);
                }
            } while (!$once);
        }

        public function getCommand()
        {
            return 'service-event-queue';
        }

        public function getDescription()
        {
            return 'Runs the asynchronous event queue dispatch worker';
        }

        public function getParameters()
        {
            return [
                new \Symfony\Component\Console\Input\InputOption('queue', null, \Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL, 'The named queue to process', 'default'),
                new \Symfony\Component\Console\Input\InputOption('interval', null, \Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL, 'Polling interval in seconds', 1),
                new \Symfony\Component\Console\Input\InputOption('once', null, \Symfony\Component\Console\Input\InputOption::VALUE_NONE, 'Process one batch of pending events and exit (for use with cron)'),
            ];
        }

    }
}
