define([
    "jquery",
    "fancyBox"
], function($){
    $.widget('tm.fancyboxInit', {

        _create: function() {
            var options = this.options;

            $(".fancybox").fancybox(options);
        }

    });
    return $.tm.fancyboxInit;
});