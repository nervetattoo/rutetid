$(function()
{
    window.setInitialFromValueFromGeolocation = function()
    {
        $.ajax(
    	{
    	    url: 'stops.php',
    	    data: {
    	        lat: $('body').data('lat'),
    	        long: $('body').data('long')
    	    },
    	    success: function(data)
    	    {
    	        $('#from').val(data.stops[0].name);
    	    },
    	    dataType: 'json'
    	});
    }

    $('#from, #to')
        .autocomplete(
        {
            source: function(req, add)
            {				
				$.ajax(
				{
				    url: 'stops.php',
				    data: {
				        term: req.term,
				        lat: $('body').data('lat'),
				        long: $('body').data('long')
				    },
				    success: function(data)
				    {
				        fetchStops(data, add);
				    },
				    dataType: 'jsonp'
				});
			}
        });
        
    function fetchStops(data, add)
    {
        var stops = [];
        $(data.stops)
            .each(function(i, stop)
            {
                stops.push(stop.name);
            });
            
        add(stops);
    }
});