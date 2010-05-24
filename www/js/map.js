var map,
    markers = [],
    latlng;
function placeMarker(loc, title) {
    var clickedLocation = new google.maps.LatLng(loc);
    // Clear out previous markers
    for (var i = 0; i < markers.length; i++) {
        var mark = markers[i];
        mark.setMap(null);
    }
    markers = [];
    var marker = new google.maps.Marker({
        position: loc, 
        map: map,
        title: title
    });
    map.setCenter(loc);
    latlng = clickedLocation;
    markers.push(marker);
}
var initMap = function(conf, node) {
    var latlng = new google.maps.LatLng(conf.lat, conf.lon);
    var myOptions = {
        zoom: conf.zoom,
        center: latlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    map = new google.maps.Map(node, myOptions);

    google.maps.event.addListener(map, 'click', function(event) {
        placeMarker(event.latLng, conf.title);
    });


}
$(function()
{
    $("#save").click(function(e) {
        e.preventDefault();
        var pos = markers[0].getPosition();
        var data = {
            update: true,
            lat: pos.lat(),
            lng: pos.lng(),
            stop: stopId
        };
        $.getJSON("/map.php", data, function(resp) {
            console.log(data);
        });
    });
});
