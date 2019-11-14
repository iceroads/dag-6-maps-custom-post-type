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


function set_stores_markers_to_map(map) {
    var endpoint = store_plugin.wp_store_endpoint;

    jQuery.ajax({
        url: endpoint,
        success: function(response) {
            // Time variable. Makes so one store drop down at the time
            var time = 0;
            // Foreach store we get from the endpoint
            response.forEach(function(store) {
                // Save the cords to a object
                var cords = {
                    lat: parseFloat(store.latitude),
                    lng: parseFloat(store.longitude)
                };
                // Make a setTimeOut to make use of the time variable
                setTimeout(function() {
                    // Create a marker on the map
                    new google.maps.Marker({
                        position: cords,
                        map: map,
                        animation: google.maps.Animation.DROP,
                    });
                }, time);
                // Add 400ms to the time to make the next marker
                // wait 400ms before it executes create marker
                time += 400;
            });
        },
        // Output error in console
        error: function(error) {
            console.log(error);
        },
        // Add JW-token in header before send
        beforeSend: function(xhr) {
            xhr.setRequestHeader("Authorization", "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczpcL1wvc2VjdXJld3AudGVzdCIsImlhdCI6MTU3Mzc0MDYxNiwibmJmIjoxNTczNzQwNjE2LCJleHAiOjE1NzQzNDU0MTYsImRhdGEiOnsidXNlciI6eyJpZCI6IjEifX19.R1RnAPwANuThjLzNIQVv6xk6e1mtJvBxn1wsY5dB41g");
        }
    });
}