<?php

    /**
     * User administration
     */

    namespace Idno\Pages\Admin {

        use Idno\Entities\Invitation;
        use Idno\Entities\User;
        use Idno\Entities\RemoteUser;

        class Users extends \Idno\Common\Page
        {

            function getContent()
            {
                $this->adminGatekeeper(); // Admins only

                $users = User::get(array(), array(), 99999, 0); // TODO: make this more complete / efficient
                $remoteusers = RemoteUser::get(array(), array(), 99999, 0);
                
                $users = array_merge($users, $remoteusers);

                $t        = \Idno\Core\site()->template();
                $t->body  = $t->__(array('users' => $users))->draw('admin/users');
                $t->title = 'User Management';
                $t->drawPage();

            }

            function postContent()
            {

                $this->adminGatekeeper(); // Admins only

                $action = $this->getInput('action');

                switch ($action) {
                    case 'add_rights':
                        $uuid = $this->getInput('user');
                        if ($user = User::getByUUID($uuid)) {
                            $user->setAdmin(true);
                            $user->save();
                            \Idno\Core\site()->session()->addMessage($user->getTitle() . " was given administration rights.");
                        }
                        break;
                    case 'remove_rights':
                        $uuid = $this->getInput('user');
                        if ($user = User::getByUUID($uuid)) {
                            $user->setAdmin(false);
                            $user->save();
                            \Idno\Core\site()->session()->addMessage($user->getTitle() . " was stripped of their administration rights.");
                        }
                        break;
                    case 'delete':
                        $uuid = $this->getInput('user');
                        if ($user = User::getByUUID($uuid)) {
                            if ($user->delete()) {
                                \Idno\Core\site()->session()->addMessage($user->getTitle() . " was removed from your site.");
                            }
                        }
                        break;
                    case 'invite_users':
                        $emails = $this->getInput('invitation_emails');

                        preg_match_all('/[a-z\d._%+-]+@[a-z\d.-]+\.[a-z]{2,4}\b/i', $emails, $matches);

                        $invitation_count = 0;

                        if (!empty($matches[0])) {
                            if (is_array($matches[0])) {
                                foreach ($matches[0] as $email) {
                                    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                        if (!($user = User::getByEmail($email))) {
                                            if ((new Invitation())->sendToEmail($email) !== 0) {
                                                $invitation_count++;
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        if ($invitation_count > 1) {
                            \Idno\Core\site()->session()->addMessage("{$invitation_count} invitations were sent.");
                        } else if ($invitation_count == 1) {
                            \Idno\Core\site()->session()->addMessage("Your invitation was sent.");
                        } else {
                            \Idno\Core\site()->session()->addMessage("No email addresses were found or all the people you invited are already members of this site.");
                        }
                        break;
                }

                $this->forward(\Idno\Core\site()->config()->getURL() . 'admin/users');

            }

        }
    }
?>