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
                'class': 'add-form',
                'text': 'Legg til nytt stopp her'
            })
        });
        
        // Add 
        $('#edit-route #stops')
            .prepend($(addLi).clone())
            .click(function(e)
            {
                if($(e.target).is('.add-form'))
                {
                    e.preventDefault();
                    
                    $(e.target).closest('li').siblings('.active')
                        .removeClass('active')
                    .find('form')
                        .remove()
                    .end().end()
                        .addClass('active')
                        .append($(addForm).clone());
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