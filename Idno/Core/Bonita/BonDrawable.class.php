<?php

/**

  The BonDrawable interface and supporting functions.

  Optionally, classes can implement this interface in order to use
  the default object drawing functionality.

  @package Bonita
  @subpackage Templating

 */
/**
 * BonDrawable interface for objects to be automatically drawable.
 */

namespace Idno\Core\Bonita {

    interface Drawable {

        public function getTitle();

        public function getDescription();

        public function getURI();
    }

}
