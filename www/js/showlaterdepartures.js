/*
 *  @author     Hein Haraldson Berg
 *  @email      hein@keyteq.no
 */

var AddDepartures = function()
{
    this.init();
}

AddDepartures.prototype = {
    maxRows: 10,
    rowsRemoved: 0,
    
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
            	        time: $('#time').val(),
            	        format: 'json',
            	        limit: 5,
            	        offset: ($('#routes tbody tr').length - 1) + self.rowsRemoved
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
        
        var rowCount = data.length + $('#routes tbody tr').length;
        var rowsToRemove = rowCount - this.maxRows;
        if(rowsToRemove > 0)
        {
            this.rowsRemoved += rowsToRemove;
            $('#routes tbody tr:lt(' + rowsToRemove + ')')
                .remove();
        }
        
        var departuresHtml = '';
        $(data)
            .each(function(i, bus)
            {
                departuresHtml += '<tr>';
                departuresHtml += '<td class="no">Rutebil <strong>' + bus.id + '</strong></td>';
                departuresHtml += '<td class="here">' + bus.wait + ' minutter <span class="dim">(' + bus.startTime + ')</span></td>';
                departuresHtml += '<td class="there">' + bus.arrivalSpan + ' minutter <span class="dim">(' + bus.arrivalTime + ')</span></td>';
                departuresHtml += '</tr>';
            });
        
        return departuresHtml;
    },
    
    addDepartures: function(html)
    {
        $('#routes tbody')
            .append(html)
        .find('tr:even')
            .addClass('odd')
            .removeClass('even')
        .end().find('tr:odd')
            .addClass('even')
            .removeClass('odd');
    }
};


$(function()
{
    if($('#route-search').length && $('#show-more-routes').length)
    {
        var RUTETID_AddDepartures = new AddDepartures();
    }
});
