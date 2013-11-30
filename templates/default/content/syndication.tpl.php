<?php

    $buttons = '';
    if (!empty($vars['services'])) {
        foreach($vars['services'] as $service) {

            $button = $this->draw('content/syndication/' . $service);
            if (empty($button)) {
                $button = $this->__(['service' => $service])->draw('content/syndication/button');
            }
            $buttons .= $button;

        }
    }
    if (!empty($buttons)) {
        echo '<p class="syndication">' . $buttons . '</p>';
    }