<p class="pronoun small">
    <?php

        $pronoun_list = [];
        foreach($vars['pronouns'] as $type => $pronoun) {
            $pronoun_list[] = '<span class="p-x-pronoun-' . $type . '">' . $pronoun . '</span>';
        }
        echo implode(' / ', $pronoun_list);

    ?>
</p>