/**
 * Copyright © 2015. All rights reserved.
 */

define([
    'jquery',
    'faeturedCarousel'
], function($){
    "use strict";

    $.widget('TemplateMonster.faeturedCarouselTheme', {


        options: {
            items: 3,
            itemsDesktop: [1199,4],
            itemsDesktopSmall: [979,3],
            itemsTablet: [768,2],
            itemsMobile: [300,2],
            autoPlay: false,
            navigation:true,
            pagination: false,
            addClassActive: true,
            navigationText: [],
        },

        _create: function() {
            if($(".grid-main.layout_4").length){
                this.options.itemsDesktop = [1199, 3];
            }
            this.element.owlCarousel(this.options);
        },

    });

    return $.TemplateMonster.faeturedCarouselTheme;

});
