<?php

    /**
     * User administration
     */

    namespace Idno\Pages\Admin {

        use Idno\Entities\Invitation;
        use Idno\Entities\User;

        class Users extends \Idno\Common\Page
        {

            function getContent()
            {
                $this->adminGatekeeper(); // Admins only
                $t        = \Idno\Core\site()->template();
                $t->body  = $t->draw('admin/users');
                $t->title = 'User Management';
                $t->drawPage();

            }

            function postContent()
            {

                $this->adminGatekeeper(); // Admins only
                $emails = $this->getInput('invitation_emails');

                preg_match_all('/[a-z\d._%+-]+@[a-z\d.-]+\.[a-z]{2,4}\b/i',$emails,$matches);

                $invitation_count = 0;

                if (!empty($matches[0])) {
                    if (is_array($matches[0])) {
                        foreach($matches[0] as $email) {
                            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                if (!($user = User::getByEmail($email))) {
                                    (new Invitation())->sendToEmail($email);
                                    $invitation_count++;
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

                $this->forward(\Idno\Core\site()->config()->getURL() . 'admin/users');

            }

        }
    }
?>