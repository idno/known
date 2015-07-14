<?php

    namespace IdnoPlugins\StaticPages {

        use Idno\Common\Entity;
        use Idno\Entities\User;

        class StaticPage extends Entity
        {

            function getTitle()
            {
                if (!empty($this->title)) {
                    return $this->title;
                }

                return 'Untitled';
            }

            function getDescription()
            {
                if (!empty($this->body)) {
                    return $this->body;
                }

                return '';
            }

            function getActivityStreamsObjectType()
            {
                return 'article';
            }

            function getURL()
            {

                // If we have a URL override, use it
                if (!empty($this->url)) {
                    return $this->url;
                }

                if (!empty($this->canonical)) {
                    return $this->canonical;
                }

                // If a slug has been set, use it
                if ($slug = $this->getSlug()) {
                    return \Idno\Core\site()->config()->getURL() . 'pages/' . $slug;
                }

                $new = false;
                if ($args = func_get_args()) {
                    if ($args[0] === true) {
                        $new = true;
                    }
                }

                $id = $this->getID();
                if (!$new && !empty($id)) {
                    $uuid = $this->getUUID();
                    if (!empty($uuid)) {
                        return $uuid;
                    }
                }

                return \Idno\Core\site()->config()->url . $this->getClassSelector() . '/edit';

            }

            function canEdit($user_id = '')
            {
                if (empty($user_id)) {
                    $user = \Idno\Core\site()->session()->currentUser();
                } else {
                    $user = User::getByUUID($user_id);
                }
                if (!($user instanceof User)) {
                    return false;
                }
                if (!$user->isAdmin()) {
                    return false;
                }

                return true;
            }

            function saveDataFromInput()
            {

                $body        = \Idno\Core\site()->currentPage()->getInput('body');
                $title       = \Idno\Core\site()->currentPage()->getInput('title');
                $category    = \Idno\Core\site()->currentPage()->getInput('category');
                $forward_url = \Idno\Core\site()->currentPage()->getInput('forward_url');
                $hide_title  = \Idno\Core\site()->currentPage()->getInput('hide_title');
                $access      = \Idno\Core\site()->currentPage()->getInput('access');

                if ($staticpages = \Idno\Core\site()->plugins()->get('StaticPages')) {
                    /* @var IdnoPlugins\StaticPages\Main $staticpages */
                    $categories = $staticpages->getCategories();
                        if (in_array($category, $categories) || $category == 'No Category') {

                            $this->title       = $title;
                            $this->body        = $body;
                            $this->category    = $category;
                            $this->forward_url = $forward_url;
                            $this->hide_title  = $hide_title;
                            $this->setAccess($access);

                            if ($result = $this->save()) {
                                return true;
                            }

                        } else {
                            \Idno\Core\site()->session()->addMessage("Your selected category wasn't found in the list.");
                        }
                    //}
                }

                return false;

            }

            function deleteData()
            {
            }

        }

    }