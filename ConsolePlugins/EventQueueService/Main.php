<?php

namespace ConsolePlugins\EventQueueService {

    class Main extends \Idno\Common\ConsolePlugin
    {

        public static $run = true;

        function registerTranslations()
        {

            \Idno\Core\Idno::site()->language()->register(
                new \Idno\Core\GetTextTranslation(
                    'eventqueueservice', dirname(__FILE__) . '/languages/'
                )
            );
        }

        public function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output)
        {

            $queue = $input->getArgument('queue');
            $pollperiod = (int)$input->getArgument('pollperiod');

            define("KNOWN_EVENT_QUEUE_SERVICE", true);

            // Set up shutdown listener

            pcntl_signal(SIGTERM, function($signo) {
                \Idno\Core\Idno::site()->logging()->debug(\Idno\Core\Idno::site()->language()->_('SIGTERM received, shutting down.'));
                \ConsolePlugins\EventQueueService\Main::$run = false;
                \Idno\Core\Idno::site()->logging()->info(\Idno\Core\Idno::site()->language()->_('Shutting down, this may take a little while...'));
            });

            if (!\Idno\Core\Service::isFunctionAvailable('system'))
                throw new \RuntimeException(\Idno\Core\Idno::site()->language()->_('Sorry, your hosting environment does not support functionality (the "system" function) necessary to support this action.'));

            try {
                $pid = pcntl_fork();
                if ($pid == -1) {
                     throw new \RuntimeException(\Idno\Core\Idno::site()->language()->_('Could not fork a new process'));
                } else if ($pid) {
                    \Idno\Core\Idno::site()->logging()->info(\Idno\Core\Idno::site()->language()->_('Starting GC thread for %s', [$queue]));

                    try {
                        while(self::$run) {
                            sleep(300);

                            \Idno\Core\Service::call('/service/queue/gc/');

                        }
                    } catch (\Error $e) {
                        \Idno\Core\Idno::site()->logging()->error($e->getMessage());
                    }

                } else {
                    \Idno\Core\Idno::site()->logging()->info(\Idno\Core\Idno::site()->language()->_('Starting Asynchronous event processor on queue: %s, polling every %d seconds', [$queue, $pollperiod]));

                    while (self::$run) {

                        try {

                            while(self::$run) {

                                \Idno\Core\Idno::site()->logging()->debug(\Idno\Core\Idno::site()->language()->_('Polling queue...'));

                                if ($events = \Idno\Core\Service::call('/service/queue/list/')) {
                                    foreach ($events->queue as $event) {
                                        try {
                                            \Idno\Core\Idno::site()->logging()->info(\Idno\Core\Idno::site()->language()->_('Dispatching event %s', [$event]));
                                            //\Idno\Core\Service::call('/service/queue/dispatch/' . $event);

                                            system(escapeshellcmd("./known.php event-queue-manage $queue dispatch $event"));
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

        public function getCommand()
        {
            return 'service-event-queue';
        }

        public function getDescription()
        {
            return \Idno\Core\Idno::site()->language()->_('Begin the Asynchronous event queue dispatcher service');
        }

        public function getParameters()
        {
            return [
                new \Symfony\Component\Console\Input\InputArgument('queue', \Symfony\Component\Console\Input\InputArgument::OPTIONAL, \Idno\Core\Idno::site()->language()->_('Queue to process'), 'default'),
                new \Symfony\Component\Console\Input\InputArgument('pollperiod', \Symfony\Component\Console\Input\InputArgument::OPTIONAL, \Idno\Core\Idno::site()->language()->_('How often should the service poll the queue'), 20),
            ];
        }

    }

}
