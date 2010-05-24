var initMap = function(conf, node) {
    var latlng = new google.maps.LatLng(conf.lat, conf.lon);
    var myOptions = {
        zoom: conf.zoom,
        center: latlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    var map = new google.maps.Map(node, myOptions);
}
$(function()
{
});
