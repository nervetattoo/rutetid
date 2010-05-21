var Geolocation = function()
{
    this.init();
}

Geolocation.prototype = {
    locationWatchId: 0,
    
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
        this.locationWatchId = navigator.geolocation.getCurrentPosition(function(position)
        {
            $('<p>',
            {
                'css': { color: 'green' },
                'text': 'Latitude ' + position.coords.latitude + '; Longitude ' + position.coords.longitude
            }).appendTo('body');
        });
    },
    
    updateLocation: function()
    {
        this.locationWatchId = navigator.geolocation.watchPosition(function(position)
        {
            $('<p>',
            {
                'css': { color: 'red' },
                'text': 'Latitude ' + position.coords.latitude + '; Longitude ' + position.coords.longitude
            }).appendTo('body');
        });
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
    setInterval(function()
    {
        console.log(RUTETID_Geolocation.locationWatchId);
    },2000);
});