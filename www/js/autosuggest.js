/*
 *  @author     Hein Haraldson Berg
 *  @email      hein@keyteq.no
 */

$(function()
{
    window.setInitialFromValueFromGeolocation = function()
    {
        if($('#from').length && $('#from').val() == '')
        {
            $.ajax(
        	{
        	    url: '/service_stops.php',
        	    data: {
        	        lat: $('body').data('lat'),
        	        long: $('body').data('long')
        	    },
        	    success: function(data)
        	    {
        	        $('#from')
        	           .val(data.stops[0].name);
        	    },
        	    dataType: 'json'
        	});
    	}
    }

    $('#from, #to')
        .autocomplete(
        {
            source: function(req, add)
            {
                var from = ($(this.element).is('#to')) ? $('#from').val() : '';

				$.ajax(
				{
				    url: '/service_stops.php',
				    data: {
				        term: req.term,
				        from: from,
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
