$(document).ready(function () {
    
    $("[rel=tooltip]").tooltip();
    
    $("a.group").fancybox({
        'titleShow'     : false,
        openEffect : 'elastic',
        openSpeed  : 150,
        closeEffect : 'elastic',
        closeSpeed  : 150,
        closeClick : true,
    });
    
    $container = $("#gallery-container");
    
    $container.isotope({
        itemSelector: '.item',
        resizable: false,
        masonry: { columnWidth: $container.width() / 4 }
    });
    
    $(window).smartresize(function(){
        $container.isotope({
            // update columnWidth to a percentage of container width
            masonry: { columnWidth: $container.width() / 4 }
        });
    });
});