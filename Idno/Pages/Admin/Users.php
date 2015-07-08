<?php

    /**
     * User administration
     */

    namespace Idno\Pages\Admin {

        use Idno\Entities\Invitation;
        use Idno\Entities\RemoteUser;
        use Idno\Entities\User;

        class Users extends \Idno\Common\Page
        {

            function getContent()
            {
                $this->adminGatekeeper(); // Admins only

                $users       = User::get(array(), array(), 99999, 0); // TODO: make this more complete / efficient
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
                    case 'add_user':
                        $name       = $this->getInput('name');
                        $handle     = trim($this->getInput('handle'));
                        $email      = trim($this->getInput('email'));

                        $password = "";
                        //Initialize a random desired length
                        $desired_length = rand(8, 12);
                        for($length = 0; $length < $desired_length; $length++) {
                            //Append a random ASCII character (including symbols)
                            $password .= chr(rand(32, 126));
                        }

                        $user = new \Idno\Entities\User();

                        if (empty($handle) && empty($email)) {
                            \Idno\Core\site()->session()->addMessage("Please enter a username and email address.");
                        } else if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            if (
                                !($emailuser = \Idno\Entities\User::getByEmail($email)) &&
                                !($handleuser = \Idno\Entities\User::getByHandle($handle)) &&
                                !empty($handle) && strlen($handle) <= 32 &&
                                !substr_count($handle, '/')
                            ) {
                                $user         = new \Idno\Entities\User();
                                $user->email  = $email;
                                $user->handle = strtolower(trim($handle)); // Trim the handle and set it to lowercase
                                $user->setPassword($password);
                                if (empty($name)) {
                                    $name = $user->handle;
                                }
                                $user->setTitle($name);
                                $user->save();
                            } else {
                                if (empty($handle)) {
                                    \Idno\Core\site()->session()->addMessage("Please create a username.");
                                }
                                if (strlen($handle) > 32) {
                                    \Idno\Core\site()->session()->addMessage("Your username is too long.");
                                }
                                if (substr_count($handle, '/')) {
                                    \Idno\Core\site()->session()->addMessage("Usernames can't contain a slash ('/') character.");
                                }
                                if (!empty($handleuser)) {
                                    \Idno\Core\site()->session()->addMessage("Unfortunately, someone is already using that username. Please choose another.");
                                }
                                if (!empty($emailuser)) {
                                    \Idno\Core\site()->session()->addMessage("Hey, it looks like there's already an account with that email address. Did you forget your login?");
                                }
                            }
                        } else {
                            \Idno\Core\site()->session()->addMessage("That doesn't seem like it's a valid email address.");
                        }

                        if (!empty($user->_id)) {
                            \Idno\Core\site()->session()->addMessage("User registered with password $password - better mail that to them now!");
                        } else {
                            \Idno\Core\site()->session()->addMessageAtStart("We couldn't register that user.");
                        }
                        break;
                }

                $this->forward(\Idno\Core\site()->config()->getURL() . 'admin/users');

            }

        }
    }
?>
