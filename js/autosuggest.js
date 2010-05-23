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
        	    url: 'stops.php',
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
				    url: 'stops.php',
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
    
    // Auto fill time field
    if($('#time').length)
    {
        function setNowTime()
        {
            var d = new Date();
            var hours = twoDigitize(d.getHours());
            var minutes = twoDigitize(d.getMinutes());
            
            $('#time').val(hours + ':' + minutes);
        }
        function twoDigitize(num)
        {
            num = num.toString();
            if(num.length == 1)
                num = '0' + num;
            
            return num;
        }
        setNowTime();
        setTimeout(setNowTime, 15000);
    }
});