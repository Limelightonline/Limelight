<?php
/**
 * NOTICE OF LICENSE
 * This source file is subject to the General Public License (GPL 2.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/GPL-2.0
 *
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade the module to newer
 * versions in the future.
 *
 * @author Alexey Svistunov
 * @copyright 2002-2016 TemplateMonster
 * @license http://opensource.org/licenses/GPL-2.0 General Public License (GPL 2.0)
 */

namespace TemplateMonster\ImageZoom\Block\Product\View;

use Magento\Framework\Data\Collection;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Helper\Image as ImageHelper;
use TemplateMonster\ImageZoom\Helper\Data as DataHelper;
use Magento\Catalog\Model\ProductRepository;
use \Magento\Catalog\Model\Product;
use Magento\Framework\Json\EncoderInterface;

class Gallery extends Template
{
    protected $_productBlock;
    protected $_dataHelper;
    protected $_imageHelper;
    protected $_template = 'product/view/gallery.phtml';
    protected $_product;
    protected $_repository;

    public function __construct(
        AbstractProduct $productBlock,
        Template\Context $context,
        ImageHelper $imageHelper,
        DataHelper $dataHelper,
        ProductRepository $repository,
        EncoderInterface $jsonEncoder,
        array $data = []
    ) {
        $this->jsonEncoder = $jsonEncoder;
        $this->_productBlock = $productBlock;
        $this->_imageHelper = $imageHelper;
        $this->_dataHelper = $dataHelper;
        $this->_product = $productBlock->getProduct();
        $this->_repository = $repository;

        parent::__construct($context, $data);
    }

    public function getAllImages()
    {
        $images = [];
        $images[$this->_product->getId()] = $this->getGalleryImages($this->_product);

        foreach($this->getProductVariationsIds() as $variation){
            $images[$variation] = $this->getGalleryImages($this->getProduct($variation));
        }

        return $this->jsonEncoder->encode($images);
    }


    /**
     * Get simple product ids for configurable product
     *
     * @return \int[]|null
     */
    public function getProductVariationsIds()
    {
        if($this->_product->canConfigure()){
            return $this->_product->getExtensionAttributes()->getConfigurableProductLinks();
        }
        return null;
    }

    /**
     * Get configurable product options
     *
     * @return \Magento\ConfigurableProduct\Api\Data\OptionInterface[]|null
     */
    public function getProductConfigOptions()
    {
        if($this->_product->canConfigure()){
            return $this->_product->getExtensionAttributes()->getConfigurableProductOptions();
        }
        return null;
    }

    /**
     * Get product model object
     *
     * @param null $id
     * @return \Magento\Catalog\Api\Data\ProductInterface|\Magento\Catalog\Model\Product|mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProduct($id = null)
    {
        if($id){
            return $this->_repository->getById($id);
        }
        return $this->_product;
    }

    /**
     * Retrieve collection of gallery images
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return Collection
     */
    protected function getGalleryImagesData(Product $product)
    {
        $images = $product->getMediaGalleryImages();

        if ($images instanceof Collection) {
            foreach ($images as $image) {
                /* @var \Magento\Framework\DataObject $image */

                $imageFile = $image->getData('file');

                $image->setData(
                    'large_image',
                    $this->_imageHelper->init($product, 'product_page_image_large')
                    ->constrainOnly(true)->keepAspectRatio(true)->keepFrame(false)
                    ->setImageFile($imageFile)->getUrl());

                $image->setData(
                    'medium_image',
                    $this->_imageHelper->init($product, 'product_page_image_medium')
                        ->constrainOnly(true)->keepAspectRatio(true)->keepFrame(false)
                        ->setImageFile($imageFile)->getUrl());

                $image->setData(
                    'small_image',
                    $this->_imageHelper->init($product, 'product_page_image_small')
                        ->constrainOnly(true)->keepAspectRatio(true)->keepFrame(false)
                        ->setImageFile($imageFile)->getUrl());
                            }
        }
        return $images;
    }

    /**
     * Build gallery images array
     *
     * @param Product $product
     * @return array
     */
    public function getGalleryImages(Product $product)
    {
        $imagesItems = [];

        foreach ($this->getGalleryImagesData($product) as $image) {

            /* @var \Magento\Framework\DataObject $image */
            $imagesItems[] = [
                'small'     => $image->getData('small_image'),
                'medium'    => $image->getData('medium_image'),
                'large'     => $image->getData('large_image'),
                'isMain'    => $this->isMainImage($image, $product),
                'videoData' => $this->getVideoData($image),
                'size' => getimagesize($image->getData('medium_image'))
            ];
        }

        if (!$imagesItems) {
            $imagesItems[] = [
                'thumb' => $this->_imageHelper->getDefaultPlaceholderUrl('thumbnail'),
                'img' => $this->_imageHelper->getDefaultPlaceholderUrl('image'),
                'full' => $this->_imageHelper->getDefaultPlaceholderUrl('image'),
                'isMain' => true
            ];
        }
        return $imagesItems;
    }

    /**
     * Get video item data
     *
     * @param \Magento\Framework\DataObject $image
     * @return null
     */
    protected function getVideoData(\Magento\Framework\DataObject $image)
    {
        $videoData = null;

        if(!empty($image->getData('video_url'))){
            $videoUrl = $image->getData('video_url');

            $urlParsed = parse_url($videoUrl);

            $videoData['host'] = $this->getVideoHost($urlParsed['host']);
            $videoData['id'] = $this->getVideoId($urlParsed);
        }

        return $videoData;
    }

    /**
     * Get video host
     *
     * @param $host
     * @return string
     */
    protected function getVideoHost($host)
    {
        $result = '';

        if(strpos($host, 'youtu') !== false){
            $result = 'youtube';
        }

        if(strpos($host, 'vimeo') !== false){
            $result = 'vimeo';
        }

        return $result;
    }

    /**
     * Get video ID
     *
     * @TODO move video function to separate class
     * @param $parsedUrl
     * @return null
     */
    protected function getVideoId($parsedUrl)
    {
        $v = null;
        $result = null;

        if(key_exists('query', $parsedUrl)){
            $query = $parsedUrl['query'];
            parse_str($query);
            $result = $v;
        } else {
            $params = explode('/', $parsedUrl['path']);
            $result = $params[count($params) - 1];
        }

        return $result;
    }


    /**
     * Get imageZoom options as json
     *
     * @return string
     */
    public function getImageZoomOptionsJson()
    {
        return $this->_dataHelper->getOptionsJson();
    }


    /**
     * Is product main image
     *
     * @param $image
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    public function isMainImage($image, \Magento\Catalog\Model\Product $product)
    {
        return $product->getImage() == $image->getFile();
    }

    /**
     * @return bool
     */
    public function isShownLoadingIcon()
    {
        return $this->_dataHelper->isShownLoadingIcon();
    }

    /**
     * @return string
     */
    public function getLoadingIcon()
    {
        return $this->_dataHelper->getLoadingIcon();
    }
}