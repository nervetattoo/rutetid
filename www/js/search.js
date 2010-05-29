$(function()
{
    $("#route-search").submit(function(e) {
        e.preventDefault();
        var form = $(this);
        var time = form.find("#time").val();
        var url = "/" + form.find('#from').val() + "/" + 
            form.find('#to').val();
        url += "/?time=" + time;
        window.location.href = url;
    });
});
