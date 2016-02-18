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
                $users       = array_merge($users, $remoteusers);
                $invitations = Invitation::get();

                $t        = \Idno\Core\Idno::site()->template();
                $t->body  = $t->__(array('users' => $users, 'invitations' => $invitations))->draw('admin/users');
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
                            \Idno\Core\Idno::site()->session()->addMessage($user->getTitle() . " was given administration rights.");
                        }
                        break;
                    case 'remove_rights':
                        $uuid = $this->getInput('user');
                        if ($user = User::getByUUID($uuid)) {
                            $user->setAdmin(false);
                            $user->save();
                            \Idno\Core\Idno::site()->session()->addMessage($user->getTitle() . " was stripped of their administration rights.");
                        }
                        break;
                    case 'delete':
                        $uuid = $this->getInput('user');
                        if ($user = User::getByUUID($uuid)) {
                            if ($user->delete()) {
                                \Idno\Core\Idno::site()->session()->addMessage($user->getTitle() . " was removed from your site.");
                            }
                        }
                        break;
                    case 'invite_users':
                        $emails = $this->getInput('invitation_emails');

                        preg_match_all('/[a-z\d._%\+\-]+@[a-z\d.-]+\.[a-z]{2,4}\b/i', $emails, $matches);

                        $invitation_count = 0;
                        $invitations_sent = 0;
                        if (!empty($matches[0])) {
                            if (is_array($matches[0])) {
                                foreach ($matches[0] as $email) {
                                    if (!($user = User::getByEmail($email))) {
                                        $invitation = new Invitation();
                                        if ($invitation->sendToEmail($email, \Idno\Core\Idno::site()->session()->currentUser()->email) !== 0) {
                                            $invitations_sent++;
                                        }
                                        $invitation_count++;
                                    }
                                }
                            }
                        }

                        if ($invitation_count > 1) {
                            \Idno\Core\Idno::site()->session()->addMessage("{$invitation_count} invitations were sent.");
                        } else if ($invitation_count == 1) {
                            \Idno\Core\Idno::site()->session()->addMessage("Your invitation was sent.");
                        } else if ($invitations_sent == 0 && $invitation_count > 0) {
                            \Idno\Core\Idno::site()->session()->addMessage("Something went wrong and we couldn't send emails to your recipients.");
                        } else {
                            \Idno\Core\Idno::site()->session()->addMessage("No email addresses were found or all the people you invited are already members of this site.");
                        }
                        break;
                    case 'remove_invitation':
                        $invitation_id = $this->getInput('invitation_id');

                        if ($invitation = Invitation::getByID($invitation_id)) {
                            if ($invitation->delete()) {
                                \Idno\Core\Idno::site()->session()->addMessage("The invitation was removed.");
                            }
                        }

                        break;
                    case 'resend_invitation':

                        $invitation_id = $this->getInput('invitation_id');

                        if ($invitation = Invitation::getByID($invitation_id)) {
                            $email = $invitation->email;
                            if ($invitation->delete()) {
                                $new_invitation = new Invitation();
                                if ($new_invitation->sendToEmail($email)) {
                                    \Idno\Core\Idno::site()->session()->addMessage("The invitation was resent.");
                                }
                            }
                        }

                        break;
                    case 'add_user':

                        if (!\Idno\Core\Idno::site()->config()->canAddUsers()) {
                            \Idno\Core\Idno::site()->session()->addMessage("You can't add any more users to your site.");
                            break;
                        }

                        $name      = $this->getInput('name');
                        $handle    = trim($this->getInput('handle'));
                        $email     = trim($this->getInput('email'));
                        $password  = trim($this->getInput('password1'));
                        $password2 = trim($this->getInput('password2'));

                        $user = new \Idno\Entities\User();

                        if (empty($password) || $password != $password2) {
                            \Idno\Core\Idno::site()->session()->addMessage("Please make sure your passwords match and aren't empty.");
                        } else if (empty($handle) && empty($email)) {
                            \Idno\Core\Idno::site()->session()->addMessage("Please enter a username and email address.");
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
                                    \Idno\Core\Idno::site()->session()->addMessage("Please create a username.");
                                }
                                if (strlen($handle) > 32) {
                                    \Idno\Core\Idno::site()->session()->addMessage("Your username is too long.");
                                }
                                if (substr_count($handle, '/')) {
                                    \Idno\Core\Idno::site()->session()->addMessage("Usernames can't contain a slash ('/') character.");
                                }
                                if (!empty($handleuser)) {
                                    \Idno\Core\Idno::site()->session()->addMessage("Unfortunately, someone is already using that username. Please choose another.");
                                }
                                if (!empty($emailuser)) {
                                    \Idno\Core\Idno::site()->session()->addMessage("Hey, it looks like there's already an account with that email address. Did you forget your login?");
                                }
                            }
                        } else {
                            \Idno\Core\Idno::site()->session()->addMessage("That doesn't seem like it's a valid email address.");
                        }

                        if (!empty($user->_id)) {
                            \Idno\Core\Idno::site()->session()->addMessage("User " . $user->getHandle() . " was created. You may wish to email them to let them know.");
                        } else {
                            \Idno\Core\Idno::site()->session()->addMessageAtStart("We couldn't register that user.");
                        }
                        break;
                    case 'block_emails':
                        $emails = $this->getInput('blocked_emails');
                        preg_match_all('/[a-z\d._%+-]+@[a-z\d.-]+\.[a-z]{2,4}\b/i', $emails, $matches);

                        $block_count = 0;

                        if (!empty($matches[0])) {
                            if (is_array($matches[0])) {
                                foreach ($matches[0] as $email) {
                                    if (\Idno\Core\Idno::site()->config()->addBlockedEmail($email)) {
                                        $block_count++;
                                    }
                                }
                                \Idno\Core\Idno::site()->config()->save();
                            }
                        }

                        if ($block_count > 1) {
                            \Idno\Core\Idno::site()->session()->addMessage("{$block_count} emails were blocked.");
                        } else if ($block_count == 1) {
                            \Idno\Core\Idno::site()->session()->addMessage("The email address was blocked.");
                        } else {
                            \Idno\Core\Idno::site()->session()->addMessage("No email addresses were found.");
                        }
                        break;
                    case 'unblock_emails':
                        $emails = $this->getInput('blocked_emails');
                        preg_match_all('/[a-z\d._%+-]+@[a-z\d.-]+\.[a-z]{2,4}\b/i', $emails, $matches);

                        $block_count = 0;

                        if (!empty($matches[0])) {
                            if (is_array($matches[0])) {
                                foreach ($matches[0] as $email) {
                                    if (\Idno\Core\Idno::site()->config()->removeBlockedEmail($email)) {
                                        $block_count++;
                                    }
                                }
                                \Idno\Core\Idno::site()->config()->save();
                            }
                        }

                        if ($block_count > 1) {
                            \Idno\Core\Idno::site()->session()->addMessage("{$block_count} emails were unblocked.");
                        } else if ($block_count == 1) {
                            \Idno\Core\Idno::site()->session()->addMessage("The email address was unblocked.");
                        } else {
                            \Idno\Core\Idno::site()->session()->addMessage("No email addresses were found.");
                        }
                        break;
                }

                $this->forward(\Idno\Core\Idno::site()->config()->getURL() . 'admin/users');

            }

        }
    }
