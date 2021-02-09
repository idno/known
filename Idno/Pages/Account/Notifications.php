<?php

namespace Idno\Pages\Account {

    use Idno\Core\Idno;
    use Idno\Entities\Notification;

    class Notifications extends \Idno\Common\Page
    {

        function getContent($params = array())
        {
            $this->gatekeeper();

            $user = Idno::site()->session()->currentUser();

            $limit  = 25;
            $offset = $this->getInput('offset', 0);

            $notifs = Notification::getFromX(
                'Idno\Entities\Notification', [
                'owner' => $user->getUUID(),
                ], [], $limit, $offset
            );

            $count = Notification::countFromX(
                'Idno\Entities\Notification', [
                'owner' => $user->getUUID(),
                ]
            );

            $body = Idno::site()->template()->__(
                [
                'user'          => $user,
                'items'         => $notifs,
                'count'         => $count,
                'items_per_page'  => $limit
                ]
            )->draw('account/notifications');

            $page = Idno::site()->template()->__(
                [
                'title' => \Idno\Core\Idno::site()->language()->_('Notifications'),
                'body'  => $body,
                ]
            )->drawPage(false);

            // mark all notifications as seen
            foreach ($notifs as $notif) {
                $notif->markRead();
                $notif->save();
            }

            echo $page;
        }

    }

}

