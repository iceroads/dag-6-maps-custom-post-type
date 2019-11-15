// Funktion som kallas av Google för att skapa vår karta
// Denna function anger vi i en callback parameter i script
var map;
function initMap() {
    // Sätt latitude och longitud i en variabel
    var malmo = {lat: 55.5915048, lng: 13.00016};

    // Instansiera en ny Google Maps com är centrerad på ovanstående kordinater
    map = new google.maps.Map(
        document.getElementById('map'), {
            zoom: 11,
            center: malmo,
            disableDefaultUI: true,
        }
    );

    set_stores_markers_to_map(map);

    // Try HTML5 geolocation.
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            var pos = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            };

            // Sätt ut en markering på kartan med positionen från vår variabel
            new google.maps.Marker(
                {
                    position: pos,
                    map: map,
                    animation: google.maps.Animation.DROP,
                    icon: 'https://maps.google.com/mapfiles/kml/shapes/parking_lot_maps.png'
                }
            );

            map.setCenter(pos);
            map.setZoom(11);
        }, function() {
            handleLocationError(true, infoWindow, map.getCenter());
        });
    } else {
        // Browser doesn't support Geolocation
        handleLocationError(false, infoWindow, map.getCenter());
    }
}

function handleLocationError(browserHasGeolocation, infoWindow, pos) {
    infoWindow.setPosition(pos);
    infoWindow.setContent(browserHasGeolocation ?
        'Error: The Geolocation service failed.' :
        'Error: Your browser doesn\'t support geolocation.');
    infoWindow.open(map);
}

/**
 * Function that is loaded together with Google Maps Init function
 * @param Google Maps Instantiation from line 9 - map
 */
function set_stores_markers_to_map(map) {
    // Get endpoint and nonce from the wp_localize_script
    // defined in stores_api plugin
    var endpoint = store_plugin.wp_store_endpoint;
    var nonce = store_plugin.wp_rest_nonce;

    jQuery.ajax({
        url: endpoint,
        success: function(response) {
            console.log(response)
        },
        // Output error in console
        error: function(error) {
            console.log(error);
        },
        // Add JW-token in header before send
        beforeSend: function(xhr) {
            xhr.setRequestHeader('X-WP-Nonce', nonce);
        }
    });
}