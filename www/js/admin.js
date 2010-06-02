$(function()
{
    $("#new-user input:button").click(function(e) {
        e.preventDefault();
        var qs = $(this).parents("tr").find(":input").serialize();
        $.getJSON("/admin?" + qs, {
                module: "UserAdmin",
                service: "create"
            }, function(resp) {
            if (resp.ok) {
                $("#new-user-info").html($(
                    "<p>" + resp.username + " opprettet med "
                    + resp.pass + " som midlertidig passord.</p>"
                ));
            }
            else {
                $("#new-user-info").html($(
                    "<p>Noe gikk feil: " + resp.msg + "</p>"));
            }
        });
    });

    $("button").hover(function() {
        $(this).toggleClass("ui-state-hover");
    });
});
