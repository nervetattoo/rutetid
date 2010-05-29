/*
 *  @author     Hein Haraldson Berg
 *  @email      hein@haraldsonberg.net
 */

$(function()
{
    window.setInitialFromValueFromGeolocation = function()
    {
        if($('#from').length && $('#from').val() == '' && !$('#from').data('user'))
        {
            $.ajax(
        	{
        	    url: '/stopp/',
        	    data: {
                    module: "Stops",
                    service: "suggest",
        	        lat: $('body').data('lat'),
        	        long: $('body').data('long')
        	    },
        	    success: function(data)
        	    {
                    var closestStop = data.stops[0].name;
                    if(closestStop)
                    {
            	        $('#from')
            	           .val(closestStop);
                    }
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
				    url: '/stopp',
				    data: {
				        module: "Stops",
				        service: "suggest",
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
        })
        .focus(function()
        {
            $(this).data('user', true);
        })
        .blur(function()
        {
            $(this).data('user', false);
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
