<?php

namespace Idno\Pages\Service\Notifications {

    use Idno\Core\Idno;
    use Idno\Entities\Notification;

    class NewNotifications extends \Idno\Common\Page
    {

        function getContent($params = array())
        {
            $this->gatekeeper();

            $this->setNoCache();

            $user = Idno::site()->session()->currentUser();

            $last_time = $user->last_notification_time;
            if (!$last_time) {
                $last_time = 0;
            }

            $notifs = Notification::getFromX('Idno\Entities\Notification', [
                        'owner' => $user->getUUID(),
                        'created' => ['$gt' => $last_time]
            ]);

            if ($notifs) {
                $notifs = array_filter($notifs, function ($notif) use ($last_time) {
                    return $notif->created > $last_time;
                });

                $user->last_notification_time = $notifs[0]->created;
                $user->save();

                $arr = array_filter(array_map(function ($notif) {
                    
                    $target = $notif->getTarget();
                    
                    if (!empty($target)) { // Ensure that notifications on unavailable targets are not rendered
                        
                        Idno::site()->template()->setTemplateType('email-text');
                        $body = Idno::site()->template()->__(['notification' => $notif])->draw($notif->getMessageTemplate());
                        $annotation = $notif->getObject();

                        return [
                            'title' => $notif->getMessage(),
                            'body' => $body,
                            'icon' => $annotation['owner_image'],
                            'created' => date('c', $notif->created),
                            'link' => (empty($notif->url)) ? \Idno\Core\Idno::site()->config()->getDisplayURL() . 'account/notifications' : $notif->link
                        ];
                        
                    }
                }, $notifs));
            } else {
                $arr = [];
            }

            Idno::site()->template()->setTemplateType('json');
            Idno::site()->template()->__([
                'notifications' => $arr,
            ])->drawPage();
        }

    }

}
