/*
 *  @author     Hein Haraldson Berg
 *  @email      hein@keyteq.no
 */

$(function()
{
    // Auto fill time field
    if($('#time').length)
    {
        // Handle user interaction
        $('#time')
            .focus(function()
            {
                $(this).val('');
                clearInterval(setTimeInterval);
            })
            .blur(function()
            {
                if($(this).val() == '' || $(this).val().length < 5)
                    var setTimeInterval = setInterval(setNowTime, 15000);
            });
        
        function setNowTime()
        {
            var d = new Date();
            var hours = twoDigitize(d.getHours());
            var minutes = twoDigitize(d.getMinutes());
            
            $('#time').val(hours + ':' + minutes);
        }
        
        function twoDigitize(num)
        {
            num = num.toString();
            if(num.length == 1)
                num = '0' + num;
            
            return num;
        }
        
        setNowTime();
        var setTimeInterval = setInterval(setNowTime, 15000);
    }
});