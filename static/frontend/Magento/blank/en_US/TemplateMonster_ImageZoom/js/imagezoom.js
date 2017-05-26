define([
    "jquery",
    "elevateZoom",
    "fancyboxInit",
    'galleryThumb'
], function($){
    $.widget('tm.imageZoom', {

        _create: function() {

            var context = this,
                options = this.options;

            //EzPlus init
            context.element.ezPlus(options);

            //EzPlus resize init
            $( window ).on( "resize", function() {
                var windowWidth = $('body').innerWidth(),
                    zoomContainers = $('.zoomContainer');

                if(options.minRespondRange < windowWidth < options.maxRespondRange){
                    zoomContainers.not(':last').remove();
                    context.element.ezPlus(options);
                }
            });

            $.tm.fancyboxInit({cycle: true});
            $.tm.galleryThumb(options);

            //$('.image_thumb').on('click', function(){
            //    $('iframe').attr('src', $('iframe').attr('src'));
            //});

            this._videoThumbs();

        },

        _videoThumbs: function(){
            var galleryThumb        = $('.elevatezoom-gallery'),
                galleryPlaceholder  = $('.main-image-placeholder'),
                dataId              = galleryPlaceholder.find('a.main').attr('data-id');

            galleryThumb.on('click', function(){
                var thumb       = $(this),
                    thumbId     = thumb.attr('data-id'),
                    iframe      = $('#frame' + dataId),
                    frameSrc    = iframe.attr('src');

                iframe.attr('src', frameSrc);

                dataId = thumbId;
            });
        }

    });
    return $.tm.imageZoom;
});