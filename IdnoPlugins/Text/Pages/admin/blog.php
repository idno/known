<?php

namespace IdnoPlugins\Text\Pages\Admin {

    class Blog extends \Idno\Common\Page {

        function getContent() {
            $this->adminGatekeeper(); // Admins only
            $t = \Idno\Core\Idno::site()->template();
            $t->body = $t->draw('admin/blog');
            $t->title = 'Blog';
            $t->drawPage();
        }

        function postContent() {
            $this->adminGatekeeper(); // Admins only
            $truncate = $this->getInput('truncate');
            $character = $this->getInput('character');
            if ($truncate == 'true') {
                $truncate = true;
            } else {
                $truncate = false;
            }
            \Idno\Core\Idno::site()->config->truncate = $truncate;
            \Idno\Core\Idno::site()->config->truncate_character = $character;
            \Idno\Core\Idno::site()->config()->save();
            $this->forward(\Idno\Core\Idno::site()->config()->getURL() . 'admin/blog/');
        }

    }

}
