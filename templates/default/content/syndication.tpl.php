<?php

    $buttons = '';
    if (!empty($vars['services'])) {
        
        // Preserve service details for API
        $service_details = [];
        
        foreach($vars['services'] as $service) {

            if (\Idno\Core\site()->syndication()->has($service)) {
                
                $service_details[$service] = [];

                $button = $this->draw('content/syndication/' . $service);
                if (empty($button)) {
                    if ($accounts = \Idno\Core\site()->syndication()->getServiceAccounts($service)) {
                        foreach($accounts as $account) {
                            
                            $service_details[$service][] = ['username' => $account['username'], 'name' => $account['name']];
                            
                            $button .= $this->__(array('service' => $service, 'username' => $account['username'], 'name' => $account['name']))->draw('content/syndication/account');
                        }
                    } else {
                        $button = $this->__(array('service' => $service))->draw('content/syndication/button');
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