<?php

    $buttons = '';
    if (!empty($vars['services'])) {
        
        // Preserve service details for API
        $service_details = [];
        
        foreach($vars['services'] as $service) {

            if (\Idno\Core\Idno::site()->syndication()->has($service)) {
                
                $service_details[$service] = [];

                $button = $this->draw('content/syndication/' . $service);
                if (empty($button)) {
                    $disabled = '';
                    if ($accounts = \Idno\Core\Idno::site()->syndication()->getServiceAccounts($service)) {
                        foreach($accounts as $account) {
                            
                            // should be disabled?
                            $posse_links = $vars['posseLinks'];
                            $posse_service = $posse_links[$service];
                            foreach ($posse_service as $key => $posse_account) {
                                \Idno\Core\Idno::site()->logging()->log("mwe syndication.tpl.php posse: " . $posse_account['account_id'] . "===" . $account['name']);
                                if ($posse_account['account_id'] === $account['name']) {
                                    $disabled = 'disabled';
                                }
                            }
                            $service_details[$service][] = ['username' => $account['username'], 'name' => $account['name']];
                            
                            $button .= $this->__(array('service' => $service, 'disabled' => $disabled, 'username' => $account['username'], 'name' => $account['name'], 'selected' => \Idno\Core\Idno::site()->triggerEvent('syndication/selected/' . $service, [
                                'service' => $service,
                                'username' => $account['username'],
                                'reply-to' => \Idno\Core\Idno::site()->currentPage()->getInput('share_url')
                            ], false)))->draw('content/syndication/account');
                        }
                    } else {
                        // should be disabled?
                        $posse_links = $vars['posseLinks'];
                        // TODO: setting $disabled = 'disabled'; when appropiate
                        $button = $this->__(array('service' => $service, 'disabled' => $disabled, 'selected' => \Idno\Core\Idno::site()->triggerEvent('syndication/selected/' . $service, [
                                'service' => $service,
                                'username' => $account['username'],
                                'reply-to' => \Idno\Core\Idno::site()->currentPage()->getInput('share_url')
                            ], false)))->draw('content/syndication/button');
                    }
                }
                $buttons .= $button;

            }

        }
        
        // Since template vars aren't scoped, reset vars to avoid them appearing in API views
        unset($this->vars['service']);
        unset($this->vars['username']);
        unset($this->vars['name']);
        
        // Output service details for API
        $this->vars['services'] = $service_details;
    }
    $buttons .= $this->draw('content/syndication/buttons');
    if (!empty($buttons)) {
        echo '<p class="syndication">' . $buttons . '</p>';
    }