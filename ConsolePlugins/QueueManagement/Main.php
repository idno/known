<?php

namespace ConsolePlugins\QueueManagement {

    class Main extends \Idno\Common\ConsolePlugin
    {

        public static $run = true;

        function registerTranslations()
        {

            \Idno\Core\Idno::site()->language()->register(
                new \Idno\Core\GetTextTranslation(
                    'queuemanagement', dirname(__FILE__) . '/languages/'
                )
            );
        }

        public function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output)
        {

            $queue = $input->getArgument('queue');
            $operation = $input->getArgument('operation');
            $id = $input->getArgument('id');

            switch ($operation) {
                case 'dispatch':

                    $eventqueue = \Idno\Core\Idno::site()->queue();
                    if (!$eventqueue instanceof \Idno\Core\AsynchronousQueue)
                        throw new \RuntimeException(\Idno\Core\Idno::site()->language()->_('Your message queue is not asynchronous and so does not need managing'));

                    if (empty($id))
                        throw new \RuntimeException(\Idno\Core\Idno::site()->language()->_('You need to specify an Event ID in order to dispatch.'));

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

                        $output->writeln(\Idno\Core\Idno::site()->language()->_("Contents of '%s' queue", [$queue]));

                        foreach ($events->queue as $event) {
                            $output->writeln("\t $event");
                        }
                    }

                    break;
            }

        }

        public function getCommand()
        {
            return 'event-queue-manage';
        }

        public function getDescription()
        {
            return \Idno\Core\Idno::site()->language()->_('Asynchronous event queue management tool');
        }

        public function getParameters()
        {
            return [
                new \Symfony\Component\Console\Input\InputArgument('queue', \Symfony\Component\Console\Input\InputArgument::REQUIRED, \Idno\Core\Idno::site()->language()->_("Queue to process - usually 'default'")),
                new \Symfony\Component\Console\Input\InputArgument('operation', \Symfony\Component\Console\Input\InputArgument::OPTIONAL, \Idno\Core\Idno::site()->language()->_("Operation to export - 'list', 'dispatch'"), 'list'),
                new \Symfony\Component\Console\Input\InputArgument('id', \Symfony\Component\Console\Input\InputArgument::OPTIONAL, \Idno\Core\Idno::site()->language()->_('ID of an event')),
            ];
        }

    }

}
