$(function()
{
    $("#route-search").submit(function(e) {
        e.preventDefault();
        var form = $(this);
        var time = form.find("#time").val();
        var url = "/" + form.find('#from').val().replace("/", "%252f") + "/" + 
            form.find('#to').val().replace("/", "%252f");
        url += "/?time=" + time;
        window.location.href = url;
    });
});
