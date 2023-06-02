<?php

if ($coil = \Idno\Core\Idno::site()->config()->coil) {

    ?>

        <meta name="monetization" content="<?php echo htmlspecialchars($coil); ?>">
    <?php

}
