<?=$this->draw('entity/edit/header');?>
<script>

    function replenish(latitude, longitude) {
        console.log('Hello! ' + latitude + ', ' + longitude);
        $.ajax({
            url: '/checkin/callback',
            type: 'post',
            data: { lat: latitude.toString(), long: longitude.toString() }
        }).done(function (data) {
                console.log(data);
                $('#lat').val(latitude);
                $('#long').val(longitude);
                $('#placename').val(data.name);
                $('#address').val(data.display_name);
                $('#user_address').val(data.display_name);
            });
    }

    function exportPosition(position) {
        $.ajax({
            url: '/checkin/callback',
            type: 'post',
            data: { lat: position.coords.latitude.toString(), long: position.coords.longitude.toString() }
        }).done(function (data) {
                $('#geoplaceholder').hide();
                $('#lat').val(position.coords.latitude.toString());
                $('#long').val(position.coords.longitude.toString());
                $('#placename').val(data.name);
                $('#address').val(data.display_name);
                $('#user_address').val(data.display_name);
                $('#geofields').slideDown();
                if (typeof map === 'undefined') {
                    var map = L.map('checkinMap').setView([position.coords.latitude, position.coords.longitude], 15);
                    var layer = new L.StamenTileLayer("toner-lite");
                    map.addLayer(layer);
                    var marker = L.marker([position.coords.latitude, position.coords.longitude],{dragging: true});
                    marker.addTo(map);
                    marker.dragging.enable();
                    marker.on("dragend", function(e) {
                        var coords = e.target.getLatLng();
                        console.log(coords);
                        $('#lat').val(coords.lat.toString());
                        $('#long').val(coords.lng.toString());
                        replenish(coords.lat, coords.lng);
                    });
                }
            });
    }

    function errorPosition() {
    }

    <?php if (empty($vars['object']->_id)) { ?>
    if (navigator.geolocation) {

        // If so, get the current position and feed it to exportPosition
        // (or errorPosition if there was a problem)
        navigator.geolocation.getCurrentPosition(exportPosition, errorPosition, {enableHighAccuracy: true});

    } else {

        // If the browser isn't geo-capable, tell the user.
        $('#geoplaceholder').html('<p>Oh no! It looks like your browser does not support geolocation.</p>');

    }
    <?php } else { ?>
    
    $(document).ready(function(){
        $('#geoplaceholder').hide();
        $('#lat').val('<?= $vars['object']->lat; ?>');
        $('#long').val('<?= $vars['object']->long; ?>');
        $('#geofields').slideDown();
        if (typeof map === 'undefined') {
            var map = L.map('checkinMap').setView([<?= $vars['object']->lat; ?>, <?= $vars['object']->long; ?>], 15);
            var layer = new L.StamenTileLayer("toner-lite");
            map.addLayer(layer);
            var marker = L.marker([<?= $vars['object']->lat; ?>, <?= $vars['object']->long; ?>],{dragging: true});
            marker.addTo(map);
            marker.dragging.enable();
            marker.on("dragend", function(e) {
                var coords = e.target.getLatLng();
                console.log(coords);
                $('#lat').val(coords.lat.toString());
                $('#long').val(coords.lng.toString());
                replenish(coords.lat, coords.lng);
            });
        }
        });
    <?php } ?>
</script>
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
                        <input type="hidden" name="lat" id="lat"/>
                        <input type="hidden" name="long" id="long"/>
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
            
            <div class="content-form">
                <label for="body">
                    Comments</label>
                    <input type="text" name="body" id="body" placeholder="" value="<?= htmlspecialchars($vars['object']->body) ?>"
                           class="form-control"/>
                </label>
            </div>
            <?php if (empty($vars['object']->_id)) { ?><input type="hidden" name="forward-to" value="<?= \Idno\Core\site()->config()->getDisplayURL() . 'content/all/'; ?>" /><?php } ?>
            <?=$this->draw('entity/tags/input');?>
            <?php if (empty($vars['object']->_id)) echo $this->drawSyndication('place'); ?>
            <?= $this->draw('content/access'); ?>
            <p class="button-bar ">
               <input type="button" class="btn btn-cancel" value="Cancel" onclick="hideContentCreateForm();"/>
                <?= \Idno\Core\site()->actions()->signForm('/checkin/edit') ?>
                <input type="submit" class="btn btn-primary" value="<?php if (empty($vars['object']->_id)) { ?>Publish<?php } else { ?>Save<?php } ?>"/>

            </p>
        </div>

    </div>
</form>
<?=$this->draw('entity/edit/footer');?>