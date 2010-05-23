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
                $('<input>',
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
                'text': 'Legg til ny her'
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
                    
                    $(e.target)
                        .hide()
                    .closest('li')
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