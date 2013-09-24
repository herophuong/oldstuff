$(document).ready(function () {
    
    /* TOOLTIP */
    $("[rel=tooltip]").tooltip();
    
    /* IMAGE POPUP */
    $("a.group").fancybox({
        'titleShow'     : false,
        openEffect : 'elastic',
        openSpeed  : 150,
        closeEffect : 'elastic',
        closeSpeed  : 150,
        closeClick : true,
    });
    
    /* ISOTOPE */
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
    
    /* FILTER BAR */
    $("#category-select").on('change', function() {
        $("#filter-category").val(jQuery(this).val());
        $("#filter-form")[0].submit();
    });
    $("#purpose-select > button").on('click', function() {
        $("#filter-purpose").val(jQuery(this).data('value'));
        $("#filter-form")[0].submit();
    });
    $("#tab-select > button").on('click', function() {
        $("#filter-tab").val(jQuery(this).data('value'));
        $("#filter-form")[0].submit();
    });
});