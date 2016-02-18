<?=$this->draw('entity/edit/header');?>

<form action="<?= $vars['object']->getURL() ?>" method="post">
    <div class="row">
        <div class="col-md-8 col-md-offset-2 edit-pane">
	    <h4>
                <?php

                    if (empty($vars['object']->_id)) {
                        ?>New Location<?php
                    } else {
                        ?>Edit Location<?php
                    }
                  ?>
	    </h4>
            <div id="geoplaceholder">
                <p style="text-align: center; color: #4c93cb;">
                    Hang tight ... searching for your location.
                </p>

                <div class="geospinner">
		    <div class="rect1"></div>
		    <div class="rect2"></div>
		    <div class="rect3"></div>
		    <div class="rect4"></div>
		    <div class="rect5"></div>
		</div>
            </div>
            <div id="geofields" class="map" style="display:none">
                <div class="geolocation content-form">

                    <p>
                        <label for="placename">
                            Location<br>
                        </label>
                        <input type="text" name="placename" id="placename" class="form-control" placeholder="Where are you?" value="<?= htmlspecialchars($vars['object']->placename) ?>" />
                        <input type="hidden" name="lat" id="lat" value="<?= $vars['object']->lat ?>"/>
                        <input type="hidden" name="long" id="long" value="<?= $vars['object']->long ?>"/>
                    </p>

                    <p>
                        <label for="user_address">Address<br>
                            <small>You can edit the address if it's wrong.</small>
                        </label>
                        <input type="text" name="user_address" id="user_address" class="form-control" value="<?= htmlspecialchars($vars['object']->address) ?>"/>
                        <input type="hidden" name="address" id="address" />
                    </p>

                    <div id="checkinMap" style="height: 250px" ></div>
                </div>
            </div>

            <?= $this->__([
                'name' => 'body',
                'value' => $vars['object']->body,
                'wordcount' => false,
                'class' => 'wysiwyg-short',
                'height' => 100,
                'placeholder' => '',
                'label' => 'Description'
            ])->draw('forms/input/richtext')?>
            <?php if (empty($vars['object']->_id)) { ?><input type="hidden" name="forward-to" value="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() . 'content/all/'; ?>" /><?php } ?>
            <?=$this->draw('entity/tags/input');?>
            <?php echo $this->drawSyndication('place', $vars['object']->getPosseLinks()); ?>
            <?= $this->draw('content/access'); ?>
            <p class="button-bar ">
               <input type="button" class="btn btn-cancel" value="Cancel" onclick="hideContentCreateForm();"/>
                <?= \Idno\Core\Idno::site()->actions()->signForm('/checkin/edit') ?>
                <input type="submit" class="btn btn-primary" value="<?php if (empty($vars['object']->_id)) { ?>Publish<?php } else { ?>Save<?php } ?>"/>

            </p>
        </div>

    </div>
</form>

<?=$this->draw('entity/edit/footer');?>

<script src="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>IdnoPlugins/Checkin/checkin.js"></script>
