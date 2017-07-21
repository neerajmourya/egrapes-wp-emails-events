jQuery(function ($) {    
    $(document).ready(function (e) {        
        $(".egeb-form-accordion").accordion({
            heightStyle: "content",
            collapsible: true
        });
        
        $(".egeb-form-tabs-vertical").tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
        $(".egeb-form-tabs-vertical>ui>li").removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
    });
});