$(function()
{
    if($('#edit-route').length)
    {
        $('#edit-route #stops')
            .click(function(e)
            {
                if($(e.target).is('a') && $(e.target).closest('li').is('.add'))
                {
                    e.preventDefault();
                    
                    $(this).closest('li')
                        .addClass('active')
                        .append($(addForm).clone());
                }
            });
            
        // Build form DOM
        var addForm = $('<form>',
        {
            'method': 'get',
            'action': '',
            'class': 'rel hid clear',
            'html': $('<fieldset>',
            {
                'html': $('<input>',
                {
                    'type': 'text',
                    'name': 'stop-name',
                    'value': ''
                })
            })
        });
        
        // Build li/a DOM
        var addLi = $('<li>',
        {
            'class': 'add',
            'html': $('<a>',
            {
                'href': '#',
                'text': 'Legg til ny her'
            })
        });
        
        $('#edit-route #stops')
            .prepend($(addLi).clone())
        .find('li:not(.add)')
            .each(function()
            {
                $(this)
                    .after($(addLi).clone());
            });
    }
});