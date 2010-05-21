/*
 *  @author     Hein Haraldson Berg
 *  @email      hein@keyteq.no
 */

var Geolocation = function()
{
    this.init();
}

Geolocation.prototype = {
    locationWatchId: 0,
    maximumLocationAge: 30000, // 30 secs // 600000 10 mins
    
    init: function()
    {
        if(navigator.geolocation)
        {
            this.showLocation();
            this.updateLocation();
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
                $('<p>',
                {
                    'css': { color: 'green' },
                    'text': 'Latitude ' + position.coords.latitude + '; Longitude ' + position.coords.longitude
                }).appendTo('body');
            },
            function(error)
            {
                self.errorHandler(error);
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
                $('<p>',
                {
                    'css': { color: 'red' },
                    'text': 'Latitude ' + position.coords.latitude + '; Longitude ' + position.coords.longitude
                }).appendTo('body');
            },
            function(error)
            {
                self.errorHandler(error);
            },
            { maximumAge: self.maximumLocationAge }
        );
    },
    
    errorHandler: function(error)
    {
        switch(error.code)
        {
            case 1:
                alert('Permission denied.');
                break;
            case 2:
                alert('Failed to get your location.');
                break;
            case 3:
                alert('The server took too long to respond.');
                break;
            default:
                alert('Something went wrong...');
                break;
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