$.urlParam = function(name){
	var results = new RegExp('[\\?&]' + name + '=([^&#]*)').exec(window.location.href);
	return results[1] || 0;
}

$(function()
{
    if($('#edit-route').length)
    {
        // Build form DOM
        var addForm = $('<form>',
        {
            'method': 'get',
            'action': '',
            'class': 'rel hid clear',
            'html': $('<fieldset>',
            {
                'html': '<input type="hidden" name="stopIndex" value="" />'
                    + '<select name="stopName"></select>'
                    + '<input type="text" name="stopTime" />'
            })
        });
        
        // Build li/a DOM
        var addLi = $('<li>',
        {
            'class': 'add',
            'html': $('<a>',
            {
                'href': '#',
                'class': 'add-form',
                'text': 'Legg til nytt stopp her'
            })
        });


        // Select
        var addSelect = $('<select>',
        {
            'html': ''
        });

        
        // Add
        $('#edit-route #stops')
            .prepend($(addLi).clone())
            .click(function(e)
            {
                if($(e.target).is('.add-form'))
                {
                    e.preventDefault();

                    var stopIndex = $(e.target).parent().prevAll('.stop').length;
                    var stops = $.getJSON('/insert.php?route_json=' + $.urlParam('route'), function(data) {
                        $.each(data.stops, function(i, stop) {


                        });
                    });
                    
                    $(e.target).closest('li').siblings('.active')
                        .removeClass('active')
                    .find('form')
                        .remove()
                    .end().end()
                        .addClass('active')
                        .append($('<form>',
                        {
                            'method': 'get',
                            'action': '',
                            'class': 'rel hid clear',
                            'html': $('<fieldset>',
                            {
                                'html': '<input type="hidden" name="stopIndex" value="'+stopIndex+'" />'
                                    + '<select name="stopName"></select>'
                                    + '<input type="text" name="stopTime" />'
                                    + '<input type="submit" value="Lagre" />'
                            })
                        }).clone());
                }
            })
        .find('.stop')
            .each(function()
            {
                $(this)
                    .after($(addLi).clone());
            });
    }
});