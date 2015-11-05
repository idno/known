<?php

    namespace IdnoPlugins\StaticPages\Pages\Admin {

        use Idno\Common\Page;

        class ReorderPage extends Page
        {

            function post()
            {
                $this->adminGatekeeper();

                $page     = \IdnoPlugins\StaticPages\StaticPage::getByID($this->getInput('page'));
                $position = intval($this->getInput('position'));

                if (!$page) {
                    // Not Found
                    $this->setResponse(404);

                    return;
                }

                if ($staticpages = \Idno\Core\Idno::site()->plugins()->get('StaticPages')) {

                    $pages        = $staticpages->getPagesByCategory($page->category);
                    $old_position = array_search($page, $pages);

                    if ($old_position === false ||
                        $position < 0 ||
                        $position >= count($pages)
                    ) {

                        // Invalid Request
                        $this->setResponse(400);

                    } else {

                        $page->priority = $pages[$position]->getPriority() + 1;
                        $page->save();
                        for ($i = $position > $old_position ? $position : $position - 1; $i >= 0; $i--) {
                            if ($i != $old_position) {
                                $pages[$i]->priority = $pages[$i]->getPriority() + 2;
                                $pages[$i]->save();
                            }
                        }

                        // Accepted
                        $this->setResponse(202);

                    }

                }

            }

        }

    }
