define([
    "jquery",
    "elevateZoom",
    "fancyboxInit"
], function($){
    $.widget('tm.galleryThumb', {

        settings: {
            galleryThumb: 'a.elevatezoom-gallery',
            mainImagePlace: '.main-image-placeholder',
            imagezoomId: 'imagezoom-image'
        },

        _create: function() {
            var options = this.options;

            this.thumbClickAction(options);
        },

        thumbClickAction: function(options){
            var self = this;

            $(self.settings.galleryThumb).on('click', function(){

                var dataId = $(this).data('id'),
                    imagePlaceholder = $(self.settings.mainImagePlace),
                    imagezoomId = self.settings.imagezoomId,
                    zoomSelector = '#' + imagezoomId,
                    activeClass = 'zoomGalleryActive';

                $(this).addClass(activeClass).siblings().removeClass(activeClass);

                $(zoomSelector).data('ezPlus').destroy();

                imagePlaceholder.find('a').not('.hidden').addClass('hidden').find('img').removeAttr('id');
                imagePlaceholder.find('a[data-id="' + dataId + '"]').removeClass('hidden').find('img').attr('id', imagezoomId);

                $(zoomSelector).ezPlus(options);

                return false;
            })
        }

    });
    return $.tm.galleryThumb;
});