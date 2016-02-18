<?php

    namespace Idno\Pages\Account {

        use Idno\Core\Idno;
        use Idno\Entities\Notification;

        class NewNotifications extends \Idno\Common\Page
        {

            function getContent($params = array())
            {
                $this->gatekeeper();

                $user = Idno::site()->session()->currentUser();

                $last_time = $user->last_notification_time;
                if (!$last_time) {
                    $last_time = 0;
                }

                $notifs = Notification::getFromX('Idno\Entities\Notification', [
                    'owner' => $user->getUUID(),
                ]);

                $notifs = array_filter($notifs, function ($notif) use ($last_time) {
                    return $notif->created > $last_time;
                });

                if ($notifs) {
                    $user->last_notification_time = $notifs[0]->created;
                    $user->save();
                }


                $arr = array_map(function ($notif) {
                    Idno::site()->template()->setTemplateType('email-text');
                    $body = Idno::site()->template()->__(['notification' => $notif])->draw($notif->getMessageTemplate());

                    return [
                        'title'   => $notif->getMessage(),
                        'body'    => $body,
                        'created' => date('c', $notif->created),
                    ];
                }, $notifs);

                Idno::site()->template()->setTemplateType('json');
                Idno::site()->template()->__([
                    'notifications' => $arr,
                ])->drawPage();
            }

        }

    }