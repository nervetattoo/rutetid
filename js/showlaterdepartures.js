var AddDepartures = function()
{
    this.init();
}

AddDepartures.prototype = {
    oddEven: ['odd', 'even'],
    
    init: function()
    {
        var self = this;
        
        var data = '/?';
        data += $('#route-search').serialize();
        data += '&format=json';
        data += '&limit=10';
        data += '&offset=' + (self.getNumRows() - 1);
        
        $('#show-more-routes')
            .click(function(e)
            {
                e.preventDefault();
                
                $.post(
				    data,
				    function(data)
				    {
				        self.addDepartures(self.formatDepartures(data));
    				},
    				'json'
				);
            });
    },
        
    formatDepartures: function(data)
    {
        var self = this;
        
        if(this.getNumRows() % 2 != 0)
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
    },
    
    getNumRows: function()
    {
        return $('#routes tbody tr:not(.shadow)').length;
    }
};



$(function()
{
    if($('#route-search').length && $('#show-more-routes').length)
    {
        var RUTETID_AddDepartures = new AddDepartures();
    }
});