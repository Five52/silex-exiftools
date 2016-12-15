let map = new google.maps.Map(document.getElementById("map"), {
    center : {
        lat: 0,
        lng: 0
    },
    zoom: 2,
    draggable: true,
    disableDoubleClickZoom: false
});
let geocoder = new google.maps.Geocoder();
let bounds = new google.maps.LatLngBounds();
let currInfo = null;

// Gives the geo code of the city
function getGeoCodeCity(name, desc, path, loc) {
    geocoder.geocode({
        'address': loc
    }, function(res, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            putMarker(res[0].geometry.location, name, desc, path);
        }
    });
}

// Places the marker on the map
function putMarker(location, desc, name, path) {
    var marker = new google.maps.Marker({
         position: location,
         map: map,
         title: name,
         draggable: false,
         animation: google.maps.Animation.DROP
    });

    bounds.extend(location);
    map.fitBounds(bounds);

    marker.infowindow = new google.maps.InfoWindow({
        content: '<figure>'+
            '<img class="map-img" src="' + path + '" alt="' + name + '">' +
            '<figcaption>' + name + '</figcaption>' +
            '</figure>' +
            '<p>' + desc + '</p>'
    });

    google.maps.event.addListener(marker, 'click', function() {
        // Handling of tooltip current info on marker
        if (currInfo !== null) {
            currInfo.close();
        }
        currInfo = this.infowindow;
        
        this.infowindow.open(map, marker);
    });
}

