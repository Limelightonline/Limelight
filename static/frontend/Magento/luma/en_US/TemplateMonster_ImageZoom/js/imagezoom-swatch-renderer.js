define([
    'jquery',
    'Magento_Swatches/js/swatch-renderer',
    'galleryThumb',
    'imageZoom'
],function($, tm){

    $.widget('tm.imageZoomSwatchRenderer', $.mage.SwatchRenderer,{

        options: {
            imageZoomImage: '#imagezoom-image',
            imageZoomGallery: '#imagezoom-gallery'
        },

        _create: function () {
            this._super();
        },

        _Rebuild: function () {
            this.loadingMask('show');
            this._super();
        },

        /**
         * Update [gallery-placeholder] or [product-image-photo]
         * @param {Array} images
         * @param {jQuery} context
         * @param {Boolean} isProductViewExist
         */
        updateBaseImage: function (images, context, isProductViewExist) {
            var justAnImage = images[0],
                //updateImg,
                imagesToUpdate,
                gallery = context.find(this.options.imageZoomImage).data('ezPlus'),
                galleryOptions = gallery.options;
                //item;

            if (isProductViewExist) {
                imagesToUpdate = images.length ? this._setImageType($.extend(true, [], images)) : [];
                //this.options.onlyMainImg = imagesToUpdate.length == 1;

                this.updateMainImages(imagesToUpdate, gallery);
                this.updateThumbnails(imagesToUpdate);

                //if (this.options.onlyMainImg) {
                    //updateImg = imagesToUpdate.filter(function (img) {
                    //    return img.isMain;
                    //});
                    //item = updateImg.length ? updateImg[0] : imagesToUpdate[0];
                    //this.updateMainImageData(item.img, item.full, gallery);
                    //this.updateMainImages(imagesToUpdate, gallery);
                //} else {
                    //this.updateMainImageData(images[0].img, images[0].full, gallery);
                    //this.updateMainImages(imagesToUpdate, gallery);
                    //this.updateThumbnails(imagesToUpdate);
                //}

                $('#imagezoom-image').ezPlus(galleryOptions);
                $.tm.galleryThumb(galleryOptions);
                $.tm.fancyboxInit({cycle: true});
                this.loadingMask('hide');

            } else if (justAnImage && justAnImage.img) {
                context.find('.product-image-photo').attr('src', justAnImage.img);
            }
        },

        updateMainImages: function(images){

            var html = '',
                image,
                activeId,
                activeClass,
                mainImagePlaceholder = '.main-image-placeholder';

            for(var i = 0; i < images.length; i++) {
                image = images[i];
                if (image.isMain) {
                    activeId = 'id="imagezoom-image"';
                    activeClass = 'main';
                } else {
                    activeId = '';
                    activeClass = 'hidden';
                }

                html += '<a href="' + image.full + '" class="fancybox ' + activeClass + '" ';
                html += 'rel="group" data-id="' + i + '">';
                html += '<img ' + activeId + ' src="' + image.img + '" alt="" width="" data-zoom-image="' + image.full + '">';
                html += '</a>';
            }

            $(mainImagePlaceholder).html(html);
        },

        updateThumbnails: function(images){
            var thumbsGallery = $(this.options.imageZoomGallery),
                html = '',
                activeClass;

            for(var i = 0; i < images.length; i++) {
                var image = images[i],
                    isMain = typeof(image.isMain) !== "undefined";

                if (isMain == true) {
                    activeClass = 'zoomGalleryActive';
                } else {
                    activeClass = '';
                }
                html += '<a href="#" class="elevatezoom-gallery ' + activeClass + '"';
                html += 'data-image="' + image.img + '"';
                html += 'data-id="' + i + '"';
                html += 'data-zoom-image="' + image.full + '">';
                html += '<img src="' + image.thumb + '" alt="" width="">';
                html += '</a>';
            }

            thumbsGallery.html(html);
        },

        loadingMask: function(show){
            var loadingMask = $('.loadingMask');

            if(show == 'show'){
                loadingMask.addClass('show');
            } else {
                loadingMask.removeClass('show');
            }
        }
    });

    return $.tm.imageZoomSwatchRenderer;
});