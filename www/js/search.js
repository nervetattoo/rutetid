$(function()
{
    $("#route-search").submit(function(e) {
        e.preventDefault();
        var form = $(this);
        var url = "/" + form.find('#from').val().replace("/", "%252f") + "/" + 
            form.find('#to').val().replace("/", "%252f");

        var time = form.find("#time");
        // Only send time information if the user actively changed it
        if (time.data('user') == true)
            url += "/?time=" + time.val();
        window.location.href = url;
    });
});
