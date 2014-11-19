<?php

    $buttons = '';
    if (!empty($vars['services'])) {
        foreach($vars['services'] as $service) {

            if (\Idno\Core\site()->syndication()->has($service)) {

                $button = $this->draw('content/syndication/' . $service);
                if (empty($button)) {
                    if ($accounts = \Idno\Core\site()->syndication()->getServiceAccounts($service)) {
                        foreach($accounts as $account) {
                            $button .= $this->__(array('service' => $service, 'username' => $account['username'], 'name' => $account['name']))->draw('content/syndication/account');
                        }
                    } else {
                        $button = $this->__(array('service' => $service))->draw('content/syndication/button');
                    }
                }
                $buttons .= $button;

            }

        }
    }
    $buttons .= $this->draw('content/syndication/buttons');
    if (!empty($buttons)) {
        echo '<p class="syndication">' . $buttons . '</p>';
    }