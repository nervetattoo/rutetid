$(function()
{
    /*
    function fetchBusStops(lat, long, term)
    {
        this.query = '';
        this.coords = (lat && long) ? 'lat=' + lat + '&long=' + long : false;
        this.term = (term) ? 'q=' + term : false;
        
        if(this.coords || this.term)
        {
            this.query = '?';
            this.query += (this.coords) ? this.coords : '';
            this.query += (this.term) ? this.term : '';
        }
    }
    */
    
    $('#from')
        .autocomplete(
        {
            source: function(req, add)
            {
				$.getJSON(
				    'stops.php?callback=?',
				    req,
				    function(data)
				    {
                        var stops = [];
                        $(data.stops)
                            .each(function(i, stop)
                            {
                                stops.push(stop.name);
                            });
                            
                        add(stops);
                    });
			}
        });
        
    function fetchStops()
    {
        
    }
});