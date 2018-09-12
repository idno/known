<?=$this->draw('entity/edit/header');?>

<form action="<?= $vars['object']->getURL() ?>" method="post">
    <div class="row">
        <div class="col-md-8 col-md-offset-2 edit-pane">
	    <h4>
                <?php

                    if (empty($vars['object']->_id)) {
                        ?><?= \Idno\Core\Idno::site()->language()->_('New Location'); ?><?php
                    } else {
                        ?><?= \Idno\Core\Idno::site()->language()->_('Edit Location'); ?><?php
                    }
                  ?>
	    </h4>
            <div id="geoplaceholder">
                <p style="text-align: center; color: #4c93cb;">
                    <?= \Idno\Core\Idno::site()->language()->_('Hang tight ... searching for your location.'); ?>
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
                            <?= \Idno\Core\Idno::site()->language()->_('Location'); ?><br>
                        </label>
                        <?= $this->__([
                            'name' => 'placename', 
                            'id' => 'placename', 
                            'placeholder' => \Idno\Core\Idno::site()->language()->_("Where are you?"),
                            'value' => $vars['object']->placename, 
                            'class' => 'form-control'])->draw('forms/input/input'); ?>
                        <?= $this->__([
                            'name' => 'lat', 
                            'id' => 'lat',
                            'value' => $vars['object']->lat])->draw('forms/input/hidden'); ?>
                        <?= $this->__([
                            'name' => 'long', 
                            'id' => 'long',
                            'value' => $vars['object']->long])->draw('forms/input/hidden'); ?>
                    </p>

                    <p>
                        <label for="user_address"><?= \Idno\Core\Idno::site()->language()->_('Address'); ?><br>
                            <small><?= \Idno\Core\Idno::site()->language()->_("You can edit the address if it's wrong."); ?></small>
                        </label>
                        <?= $this->__([
                            'name' => 'user_address', 
                            'id' => 'user_address', 
                            'value' => $vars['object']->address, 
                            'class' => 'form-control'])->draw('forms/input/input'); ?>
                        <?= $this->__([
                            'name' => 'address', 
                            'id' => 'address'])->draw('forms/input/hidden'); ?>
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
                'label' => \Idno\Core\Idno::site()->language()->_('Description')
            ])->draw('forms/input/richtext')?>
            <?php if (empty($vars['object']->_id)) { 
                echo $this->__(['name' => 'forward-to', 'value' => \Idno\Core\Idno::site()->config()->getDisplayURL() . 'content/all/'])->draw('forms/input/hidden');
            } ?>
            <?=$this->draw('entity/tags/input');?>
            <?php echo $this->drawSyndication('place', $vars['object']->getPosseLinks()); ?>
            <?= $this->draw('content/extra'); ?>
            <?= $this->draw('content/access'); ?>
            <p class="button-bar ">
               <input type="button" class="btn btn-cancel" value="Cancel" onclick="hideContentCreateForm();"/>
                <?= \Idno\Core\Idno::site()->actions()->signForm('/checkin/edit') ?>
                <input type="submit" class="btn btn-primary" value="<?php if (empty($vars['object']->_id)) { ?><?= \Idno\Core\Idno::site()->language()->_('Publish'); ?><?php } else { ?><?= \Idno\Core\Idno::site()->language()->_('Save'); ?><?php } ?>"/>

            </p>
        </div>

    </div>
</form>

<?=$this->draw('entity/edit/footer');?>

<script src="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>IdnoPlugins/Checkin/checkin.min.js"></script>
