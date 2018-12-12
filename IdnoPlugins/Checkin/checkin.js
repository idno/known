var CheckinMap, CheckinMarker;

$(function () {
    function initMap(latitude, longitude) {
	$('#geoplaceholder').hide();
	$('#geofields').slideDown();

	var map = L.map('checkinMap').setView([latitude, longitude], 15);
	var layer = new L.StamenTileLayer("toner-lite");
	map.addLayer(layer);
	var marker = L.marker([latitude, longitude], {dragging: true});
	CheckinMap = map;
	CheckinMarker = marker;
	marker.addTo(map);
	marker.dragging.enable();

	// Set base values
	$('#lat').val(latitude.toString());
	$('#long').val(longitude.toString());

	marker.on("dragend", function (e) {
	    var coords = e.target.getLatLng();
	    console.log(coords);
	    $('#lat').val(coords.lat.toString());
	    $('#long').val(coords.lng.toString());
	    queryLocation(coords.lat, coords.lng);
	});

	$('#user_address').change(function () {
	    queryAddress($('#user_address').val(), function () {
		map.remove();
		initMap($('#lat').val(), $('#long').val());
	    });

	});
    }

    function queryLocation(latitude, longitude, cb) {
	$.ajax({
	    url: wwwroot() + 'service/geo/geocoder/',
	    data: {
		lat: latitude.toString(), 
		long: longitude.toString()
	    }
	}).done(function (data) {
	    console.log(data);
	    $('#lat').val(latitude);
	    $('#long').val(longitude);
	    $('#placename').val(data.name);
	    $('#address').val(data.display_name);
	    $('#user_address').val(data.display_name);
	    if (cb) {
		cb();
	    }
	});
    }

    function queryAddress(address, cb) {
	$.ajax({
	    url: wwwroot() + 'service/geo/geocoder/',
	    data: {
		address: address.toString()
	    }
	}).done(function (data) {
	    console.log(data);
	    $('#lat').val(data.latitude);
	    $('#long').val(data.longitude);
	    $('#placename').val(data.name);
	    $('#address').val(data.display_name);
	    $('#user_address').val(data.display_name);
	    if (cb) {
		cb();
	    }
	});
    }

    function handlePositionFromNavigator(position) {
	var latitude = position.coords.latitude,
		longitude = position.coords.longitude;
	queryLocation(latitude, longitude, initMap.bind(this, latitude, longitude));
    }

    function handleErrorFromNavigator(err) {
	addErrorMessage(err.message);
	//$('#geoplaceholder').html('<p>Could not find your location: '+err.message +'</p>');
	initMap("51.478791", "-0.01068"); // Allow failures to allow manual entry. Default to the center of the universe.
    }

    var latitude = $('#lat').val();
    var longitude = $('#long').val();

    if (latitude && longitude) {
	initMap(latitude, longitude);
    } else {
	if (navigator.geolocation) {
	    // If so, get the current position and feed it to handlePositionFromNavigator
	    // (or handleErrorFromNavigator if there was a problem)
	    navigator.geolocation.getCurrentPosition(handlePositionFromNavigator, handleErrorFromNavigator, {enableHighAccuracy: true, timeout: 10000});
	} else {
	    // If the browser isn't geo-capable, tell the user.
	    $('#geoplaceholder').html('<p>Oh no! It looks like your browser does not support geolocation.</p>');
	}
    }
});