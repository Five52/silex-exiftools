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
function getGeoCodeCity(title, desc, path, loc) {
    geocoder.geocode({
        'address': loc
    }, function(res, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            putMarker(res[0].geometry.location, title, desc, path);
        }
    });
}

// Places the marker on the map
function putMarker(location, title, desc, path) {
    let marker = new google.maps.Marker({
         position: location,
         map: map,
         title: name,
         draggable: false,
         animation: google.maps.Animation.DROP
    });

    bounds.extend(location);
    map.fitBounds(bounds);
    
    // Info tooltip on click on marker
    marker.infowindow = new google.maps.InfoWindow({
        content: '<figure class="map-figure">'+
            '<img src="' + path + '" alt="' + title + '">' +
            '<figcaption><span>' + 
            title + 
            '</span><p>' + desc + '</p>' +
            '</figcaption>' +
            '</figure>'
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

