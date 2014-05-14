<?php

    $buttons = '';
    if (!empty($vars['services'])) {
        foreach($vars['services'] as $service) {

            if (\known\Core\site()->syndication()->has($service)) {
                $button = $this->draw('content/syndication/' . $service);
                if (empty($button)) {
                    $button = $this->__(['service' => $service])->draw('content/syndication/button');
                }
                $buttons .= $button;
            }

        }
    }
    if (!empty($buttons)) {
        echo '<p class="syndication"><span class="field-description">Choose the services you want to syndicate this content to:</span><br />' . $buttons . '</p>';
    }