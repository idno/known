<?php

    $username = \Idno\Core\site()->session()->currentUser()->getHandle();
    $url = \Idno\Core\site()->session()->currentUser()->getDisplayURL();
    switch (\Idno\Core\site()->session()->currentUser()->robot_state) {

        case '1':
            echo $this->__(array(
                'body' => "Hey there <a href=\"{$url}\">{$username}</a>! Welcome to your new Known site. I'm Aleph, your very own welcome robot. Let's get started by adding a status update about what you did today. Just select the icon above."
            ))->draw('robot/post');
            break;
        case '2a':
            echo $this->__(array(
                'body' => "Beep! That was a great update. Did you see that your site address is <a href=\"".\Idno\Core\site()->config()->getDisplayURL()."\">" . \Idno\Core\site()->config()->getDisplayURL() . "</a>? Be sure and bookmark this so you can find it again.\n\nYour Known site is really coming together now. I bet you've got some great pictures. Why not upload a photo that you took recently?"
            ))->draw('robot/post');
            break;
        case '2b':
            echo $this->__(array(
                'body' => "Zeep zeep! That was a great update. Did you see that your site address is <a href=\"".\Idno\Core\site()->config()->getDisplayURL()."\">" . \Idno\Core\site()->config()->getDisplayURL() . "</a>? Be sure and bookmark this so you can find it again.\n\nYour Known site is really coming together now. Why don't you try posting something else?"
            ))->draw('robot/post');
            break;
        case '2c':
            echo $this->__(array(
                'body' => "Beep boop! That was a great update. Did you see that your site address is " . \Idno\Core\site()->config()->getDisplayURL() . "? Be sure and bookmark this so you can find it again."
            ))->draw('robot/post');
            break;
        case "3a":
            echo $this->__(array(
                'body' => "Beepity boop! That was a great picture. Did you see that you can also <a href=\"".\Idno\Core\site()->session()->currentUser()->getDisplayURL()."/edit\">update your profile</a>?"
            ))->draw('robot/post');
            break;
        case "3b":
            echo $this->__(array(
                'body' => "Boopity beep! Did you see that you can also <a href=\"".\Idno\Core\site()->session()->currentUser()->getDisplayURL()."/edit\">update your profile</a>?"
            ))->draw('robot/post');
            break;
        case '4':
            echo $this->__(array(
                'body' => "01011001 01101111 00100000 01111001 01101111 00100000 01111001 01101111 \n\nThat's how you say hello where I come from. I wanted to remind you that you can also <a href=\"".\Idno\Core\site()->config()->getDisplayURL()."admin/themes/\">change the theme of your site</a>. If you ever have feedback, you can <a href=\"".\Idno\Core\site()->config()->getDisplayURL()."account/settings/feedback/\">send a message to my human creators</a>."
            ))->draw('robot/post');
            break;

    }
    if (\Idno\Core\site()->currentPage() instanceof \Idno\Pages\Homepage) {
        if (in_array(\Idno\Core\site()->session()->currentUser()->robot_state,['3a','3b','2c','4'])) {
            $user = \Idno\Core\site()->session()->currentUser();
            //if (!empty(\Idno\Core\HelperRobot::$changed_state)) {
                switch($user->robot_state) {
                    case '3a':
                    case '3b':
                        $user->robot_state = '4';
                        break;
                    case '2c':
                        $user->robot_state = '3b';
                        break;
                    case '4':
                        $user->robot_state = 0;
                        break;
                }
                $user->save();
            //}
        }
    }