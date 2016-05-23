<div id="page-messages">
    <?php

        if (!empty($vars['messages'])) {
            foreach ($vars['messages'] as $message) {
                echo \Idno\Core\Idno::site()->session()->drawStructuredMessage($message);
            }
        }

    ?>
</div>