var AddDepartures = function()
{
    this.init();
}

AddDepartures.prototype = {
    oddEven: ['odd', 'even'],
    
    init: function()
    {
        var self = this;
        
        $('#show-more-routes')
            .click(function(e)
            {
                e.preventDefault();
                
                $.ajax(
            	{
            	    url: '/',
            	    data: {
            	        from: $('#from').val(),
            	        to: $('#to').val(),
            	        time: $('#route-search').find('input[name="time"]').val(),
            	        format: 'json',
            	        limit: 10,
            	        offset: ($('#routes tbody tr:not(.shadow)').length - 1)
            	    },
            	    success: function(data)
            	    {
            	        self.addDepartures(self.formatDepartures(data));
            	    },
            	    dataType: 'json'
            	});
            });
    },
        
    formatDepartures: function(data)
    {
        var self = this;
        
        if($('#routes tbody tr:not(.shadow)').length % 2 != 0)
            this.oddEven.reverse();
        
        var departuresHtml = '';
        $(data)
            .each(function(i, bus)
            {
                departuresHtml += '<tr class="' + self.oddEven[(i % 2)] + '">';
                departuresHtml += '<td class="no">Rutebil <strong>' + bus.id + '</strong></td>';
                departuresHtml += '<td class="here">' + bus.wait + ' minutter (' + bus.startTime + ')</td>';
                departuresHtml += '<td class="there">' + bus.arrivalSpan + ' minutter (' + bus.arrivalTime + ')</td>';
                departuresHtml += '</tr>';
            });
        
        return departuresHtml;
    },
    
    addDepartures: function(html)
    {
        $('#routes tbody')
            .append(html);
    }
};


$(function()
{
    if($('#route-search').length && $('#show-more-routes').length)
    {
        var RUTETID_AddDepartures = new AddDepartures();
    }
});