$(document).ready(function(){

    //On click signup hide login
    $("#signup").click(function()
    {
        $("#first").slideUp("slow",function()
        {
            $("#second").slideDown("slow");
        });
    });

    //on click signin hide register
    $("#signin").click(function()
    {
        $("#second").slideUp("slow",function()
        {
            $("#first").slideDown("slow");
        });
    });

});