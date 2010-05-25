/*
 *  @author     Hein Haraldson Berg
 *  @email      hein@keyteq.no
 */

$(function()
{
    $('a[rel*="popover"]')
        .click(function(e)
        {
            e.preventDefault();
            
            var box = $('#popover-' + $(this).attr('id'));
            
            $(box)
                .css(
                {
                    display: 'block',
                    opacity: 0
                })
                .animate(
                {
                    opacity: 1
                },
                {
                    duration: 300,
                    complete: function()
                    {
                        $(this).data('show', true);
                    }
                });
        });
        
    $('.popover').find('.close')
        .click(function(e)
        {
            e.preventDefault();
            
            var box = $(this).closest('.popover');
            
            if($(box).data('show'))
            {
                $(box)
                    .animate(
                    {
                        opacity: 0
                    },
                    {
                        duration: 150,
                        complete: function()
                        {
                            $(this)
                                .data('show', false)
                                .css('display', 'none');
                        }
                    });
            }
        });
});