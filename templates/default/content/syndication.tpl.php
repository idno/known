<?php

    $buttons = '';
    if (!empty($vars['services'])) {
        foreach($vars['services'] as $service) {

            if (\Idno\Core\site()->syndication()->has($service)) {
                $button = $this->draw('content/syndication/' . $service);
                if (empty($button)) {
                    $button = $this->__(['service' => $service])->draw('content/syndication/button');
                }
                $buttons .= $button;
            }

        }
    }
    $buttons .= $this->draw('content/syndication/buttons');
    if (!empty($buttons)) {
        echo '<p class="syndication">' . $buttons . '</p>';
    }