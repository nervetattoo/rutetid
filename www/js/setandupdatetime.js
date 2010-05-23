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
                if(!$(this).data('user'))
                    $(this).val('');
                else
                    clearInterval(setTimeInterval);
            })
            .blur(function()
            {
                if($(this).val() == '' || $(this).val().length < 5)
                {
                    setNowTime();
                    var setTimeInterval = setInterval(setNowTime, 15000);
                    $(this).data('user', false);
                }
                else
                    $(this).data('user', true);
            });
        
        function setNowTime()
        {
            var d = new Date();
            var h = formatTime(d.getHours());
            var m = formatTime(d.getMinutes());
            
            $('#time').val(h + ':' + m);
        }
        
        function formatTime(num)
        {
            num = num.toString();
            if(num.length == 1)
                num = '0' + num;
            
            return num;
        }
        
        $('#time')
            .bind('change blur keydown keyup', function()
            {
                userInput = $(this).val();
                userInput = userInput.toString();
                userInput = userInput.replace(/ /gi, '');
                
                if(!isNaN(userInput))
        		{
        			if(userInput.length > 3)
        			{
        				var numberArray = [];
        				for(var i = userInput.length - 2; i > 0; i = i - 2)
        				{
        					numberArray.push(userInput.substring(i, i + 2));
        				}
        				
        				var rest = userInput.length - (numberArray.length * 2);
        				if(rest > 0)
        					numberArray.push(userInput.substring(0, rest));
        				
        				numberArray[0] = (numberArray[0] < 0) ? 0 : numberArray[0];
        				numberArray[0] = (numberArray[0] > 59) ? 59 : numberArray[0];
        				numberArray[1] = (numberArray[1] < 0) ? 0 : numberArray[1];
        				numberArray[1] = (numberArray[1] > 23) ? 23 : numberArray[1];
        				
        				numberArray.reverse();
        				userInput = numberArray.join(':');
        			}
        		}
        		
                $(this).val(userInput);
            });
        
        setNowTime();
        var setTimeInterval = setInterval(setNowTime, 15000);
    }
});