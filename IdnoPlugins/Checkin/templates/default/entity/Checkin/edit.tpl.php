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

    if (navigator.geolocation) {

        // If so, get the current position and feed it to exportPosition
        // (or errorPosition if there was a problem)
        navigator.geolocation.getCurrentPosition(exportPosition, errorPosition, {enableHighAccuracy: true});

    } else {

        // If the browser isn't geo-capable, tell the user.
        $('#geoplaceholder').html('<p>Your browser does not support geolocation.</p>');

    }

</script>
<form action="<?= $vars['object']->getURL() ?>" method="post">

    <div class="row">

        <div class="col-md-8 col-md-offset-2 edit-pane">
			<h4>
                <?php

                    if (empty($vars['object']->_id)) {
                        ?>New Check-in<?php
                    } else {
                        ?>Edit Check-in<?php
                    }
                  ?>
			</h4>
            <div id="geoplaceholder">
                <p class="well">
                    Hold tight ... searching for your location.
                </p>
            </div>
            <div id="geofields" class="map" style="display:none">
                <div class="geolocation">
                    <div class="content-form">
                        <label for="placename">
                            Location</label>
                            <input type="text" name="placename" id="placename" class="form-control" placeholder="Where are you?"/>
                            <input type="hidden" name="lat" id="lat"/>
                            <input type="hidden" name="long" id="long"/>

                    </div>

                    <div class="content-form">
                        <label for="user_address">Address</label>
                        <small>You can edit the address if it's wrong.</small>
                        <input type="text" name="user_address" id="user_address" class="form-control"/>
                        <input type="hidden" name="address" id="address" class="form-control"/>
                       
                       
                    </div>

                    <div id="checkinMap" style="height: 250px" ></div>
                </div>
            </div>
            
            <div class="content-form">
                <label for="body">
                    Comments</label>
                    <input type="text" name="body" id="body" placeholder="What are you up to?" value="<?= htmlspecialchars($vars['object']->body) ?>"
                           class="form-control"/>

            </div>
            <?=$this->draw('entity/tags/input');?>
            <?php if (empty($vars['object']->_id)) echo $this->drawSyndication('place'); ?>
            <p class="button-bar ">
               <input type="button" class="btn btn-cancel" value="Cancel" onclick="hideContentCreateForm();"/>
                <?= \Idno\Core\site()->actions()->signForm('/checkin/edit') ?>
                <input type="submit" class="btn btn-primary" value="<?php if (empty($vars['object']->_id)) { ?>Check in<?php } else { ?>Save<?php } ?>"/>
                <?= $this->draw('content/access'); ?>
            </p>
        </div>

    </div>
</form>
<?=$this->draw('entity/edit/footer');?>