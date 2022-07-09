<?php

namespace Idno\Pages\Admin {

    use Idno\Common\Page;

    class Homepage extends Page
    {

        function getContent()
        {
            $this->adminGatekeeper(); // Admins only
            $t                     = \Idno\Core\Idno::site()->template();
            $t->content_types      = \Idno\Common\ContentType::getRegistered();
            $default_content_types = \Idno\Core\Idno::site()->config()->getHomepageContentTypes(); //\Idno\Core\Idno::site()->session()->currentUser()->settings['default_feed_content'];

            if (empty($default_content_types)) {
                foreach ($t->content_types as $content_type) {
                    $default_content_types[] = $content_type->getEntityClass();
                }
            }

            $t->default_content_types = $default_content_types;
            $t->body                  = $t->draw('admin/homepage');
            $t->title                 = \Idno\Core\Idno::site()->language()->_('Homepage');
            $t->drawPage();
        }

        function postContent()
        {
            $this->adminGatekeeper(); // Admins only
            $user = \Idno\Core\Idno::site()->session()->currentUser();

            $default_feed_content = $this->getInput('default_feed_content');
            if (empty($default_feed_content) || !is_array($default_feed_content)) {
                $default_feed_content = false;
            }

            \Idno\Core\Idno::site()->config()->default_feed_content = $default_feed_content;

            if (\Idno\Core\Idno::site()->config()->save()) {
                \Idno\Core\Idno::site()->session()->addMessage(\Idno\Core\Idno::site()->language()->_("The default homepage content types were saved."));
            }
            $this->forward(\Idno\Core\Idno::site()->config()->getURL() . 'admin/homepage/');
        }

    }

}

