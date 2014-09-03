<?php

    $username = \Idno\Core\site()->session()->currentUser()->getHandle();
    switch (\Idno\Core\site()->session()->currentUser()->robot_state) {

        case '1':
            echo $this->__([
                'body' => "Howdy {$username}! Welcome to your new Known site. I'm XXYX, your very own welcome robot. Let's get started by adding a status update about what you did today. Just select the <strong>Status update</strong> button above."
            ])->draw('robot/post');
            break;
        case '2a':
            echo $this->__([
                'body' => "Beep! That's a great update. Did you see that your site address is " . \Idno\Core\site()->config()->getURL() . "? Be sure and bookmark this so you can find it again.\n\nYour Known site is really coming together now. I bet you've got some great pictures. Why not upload a photo that you took recently?"
            ])->draw('robot/post');
            break;
        case '2b':
            echo $this->__([
                'body' => "Boop! That's a great update. Did you see that your site address is " . \Idno\Core\site()->config()->getURL() . "? Be sure and bookmark this so you can find it again.\n\nYour Known site is really coming together now. Why don't you try posting something else?"
            ])->draw('robot/post');
            break;
        case '2c':
            echo $this->__([
                'body' => "Beep boop! That's a great update. Did you see that your site address is " . \Idno\Core\site()->config()->getURL() . "? Be sure and bookmark this so you can find it again."
            ])->draw('robot/post');
            break;
        case "3a":
            echo $this->__([
                'body' => "Beepity boop! That's a great picture. Did you see that you can also update your profile?"
            ])->draw('robot/post');
            break;
        case "3b":
            echo $this->__([
                'body' => "Boopity beep! Did you see that you can also update your profile?"
            ])->draw('robot/post');
            break;
        case '4':
            echo $this->__([
                'body' => "01011001 01101111 00100000 01111001 01101111 00100000 01111001 01101111 \n\nThat's how you say hello where I come from. I wanted to remind you that you can also change the theme of your site. If you ever have feedback, you can send a message to my human creators."
            ])->draw('robot/post');
            break;

    }