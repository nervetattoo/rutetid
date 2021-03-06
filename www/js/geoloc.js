/*
 *  @author     Hein Haraldson Berg
 *  @email      hein@haraldsonberg.net
 */

var Geolocation = function()
{
    this.init();
}

Geolocation.prototype = {
    locationWatchId: 0,
    maximumLocationAge: 30000, // ms
    zoomLevel: 15,
    lat: 0,
    long: 0,
    
    init: function()
    {
        if(navigator.geolocation)
        {
            this.showLocation();
        }
        else
            return false;
    },
    
    showLocation: function()
    {
        var self = this;
        navigator.geolocation.getCurrentPosition(
            function(position)
            {
                self.lat = position.coords.latitude;
                self.long = position.coords.longitude;
                
                $('body')
                    .data(
                    {
                        'lat': position.coords.latitude,
                        'long': position.coords.longitude
                    });
                
                self.updateLocation();
                window.setInitialFromValueFromGeolocation();
            },
            function(error)
            {
                return false;
                var errorMsg = self.errorHandler(error, 'get');
            },
            { maximumAge: self.maximumLocationAge }
        );
    },
    
    updateLocation: function()
    {
        var self = this;
        this.locationWatchId = navigator.geolocation.watchPosition(
            function(position)
            {
                self.lat = position.coords.latitude;
                self.long = position.coords.longitude;
                $('body')
                    .data(
                    {
                        'lat': position.coords.latitude,
                        'long': position.coords.longitude
                    });
                    
                    window.setInitialFromValueFromGeolocation();
            },
            function(error)
            {
                return false;
                var errorMsg = self.errorHandler(error, 'update');
            },
            { maximumAge: self.maximumLocationAge }
        );
    },
    
    getNearbyBusStops: function(lat, long)
    {
        // Dummy data...
        var busStops = '';
        busStops += '&markers=icon:http://hein.raymond.raw.no/gfx/icon-bus.png|shadow:false|' + 60.361811 + ',' + 5.347316;
        busStops += '&markers=icon:http://hein.raymond.raw.no/gfx/icon-bus.png|shadow:false|' + 60.359965 + ',' + 5.344956;
        busStops += '&markers=icon:http://hein.raymond.raw.no/gfx/icon-bus.png|shadow:false|' + 60.361832 + ',' + 5.343475;

        return busStops;
    },
    
    errorHandler: function(error, type)
    {
        switch(error.code)
        {
            case 1:
                return 'Permission denied.';
            case 2:
                return 'Failed to get your location.';
            case 3:
                return 'The server took too long to respond.';
            default:
                return 'Something went wrong...';
        }
    },
    
    clearWatchLocation: function()
    {
        navigator.geolocation.clearWatch(this.locationWatchId);
    }
}


// INITIALIZE
$(function()
{
    var RUTETID_Geolocation = new Geolocation();
});