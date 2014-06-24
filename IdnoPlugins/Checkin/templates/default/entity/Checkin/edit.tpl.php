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

        <div class="span10 offset1">

            <div id="geoplaceholder">
                <p>
                    <span class="label">Loading location ...</span>
                </p>
            </div>
            <div id="geofields" style="display:none">
                <div class="well">
                    <p>
                        <label>
                            Where are you?<br/>
                            <input type="text" name="placename" id="placename" class="span9"/>
                            <input type="hidden" name="lat" id="lat"/>
                            <input type="hidden" name="long" id="long"/>
                        </label>
                    </p>

                    <p>
                        Address (edit this if we got it wrong!)<br/>
                        <input type="text" name="user_address" id="user_address" class="span9"/>
                        <input type="hidden" name="address" id="address"/>
                    </p>

                    <div id="checkinMap" style="height: 250px" ></div>
                </div>
            </div>
            <p>
                <label>
                    What are you up to?<br/>
                    <input type="text" name="body" id="body" value="<?= htmlspecialchars($vars['object']->body) ?>"
                           class="span9 mentionable"/>
                </label>
            </p>
            <?php if (empty($vars['object']->_id)) echo $this->drawSyndication('place'); ?>
            <p>
                <?= \Idno\Core\site()->actions()->signForm('/checkin/edit') ?>
                <input type="submit" class="btn btn-primary" value="<?php if (!$vars['object']->getUUID()) { ?>Check in<?php } else { ?>Save<?php } ?>"/>
                <input type="button" class="btn" value="Cancel" onclick="hideContentCreateForm();"/>
                <?= $this->draw('content/access'); ?>
            </p>
        </div>

    </div>
</form>
<?=$this->draw('entity/edit/footer');?>