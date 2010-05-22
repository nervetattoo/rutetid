$(function()
{    
    $('#from')
        .autocomplete(
        {
            source: function(req, add)
            {				
				$.ajax(
				{
				    url: 'stops.php',
				    data: {
				        term: req.term
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