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
    w: $('body').width(),
    h: $('body').height(),
    
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
                $('<img>',
                {
                    'css': { border: '10px solid green' },
                    'src': 'http://maps.google.com/maps/api/staticmap?zoom=14&size=' + self.w + 'x' + self.h + '&maptype=roadmap&markers=color:green|label:S|' + position.coords.latitude + ',' + position.coords.longitude + '&sensor=false',
                    'alt': 'Ze map'
                }).appendTo('body');
            },
            function(error)
            {
                var errorMsg = self.errorHandler(error, 'get');
                console.warn(errorMsg);
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
                $('<img>',
                {
                    'css': { border: '10px solid red' },
                    'src': 'http://maps.google.com/maps/api/staticmap?zoom=14&size=300x300&maptype=roadmap&markers=color:green|label:S|' + position.coords.latitude + ',' + position.coords.longitude + '&sensor=false',
                    'alt': 'Ze map'
                }).appendTo('body');
            },
            function(error)
            {
                var errorMsg = self.errorHandler(error, 'update');
                console.warn(errorMsg);
            },
            { maximumAge: self.maximumLocationAge }
        );
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