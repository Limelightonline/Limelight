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

namespace TemplateMonster\ImageZoom\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface as Scope;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use \Magento\Store\Model\StoreManagerInterface as StoreManager;

class Data extends AbstractHelper
{
    const SECTION = 'imagezoom';
    const ROOT_GROUP = 'general';
    const IMAGEZOOM_MEDIA_DIR = 'imagezoom_loader';

    protected $_zoomType;

    protected $_imageZoomOptions = [
        'general' => [
            'zoomType',
            'responsive',
            'scrollZoom',
            'imageCrossfade',
            'cursor',
            'borderSize',
            'borderColour',
            'gallery',
            'responsiveRange',
            //'zoomLens',
            //'lenszoom',
            'lensShape',
            'lensSize',
            'lensFadeIn',
            'lensFadeOut',
            'containLensZoom',
            'showLoadingIcon',
            'loadingIcon',
            'zIndex'
        ],
        'windowgroup' => [
            'lensBorderSize',
            'lensBorderColour',
            'lensColour',
            'lensOpacity',
            'zoomWindowWidth',
            'zoomWindowHeight',
            'zoomWindowPosition',
            'zoomWindowOffsetX',
            'zoomWindowOffsetY',
            'zoomWindowFadeIn',
            'zoomWindowFadeOut',
            'tint',
            'tintColour',
            'tintOpacity',
            'zoomTintFadeIn',
            'zoomTintFadeOut',
            'easing',
            'easingAmount'
        ],
//       'easinggroup' => [
//            'easingDuration',
//            'easingType',
//       ],
    ];

    protected $_zoomTypes = ['lens', 'window', 'inner'];
    protected $_lensShape = ['square', 'round'];
    //protected $_easingTypes;

    protected $_jsonEncoder;
    protected $_scopeConfig;
    protected $_directoryList;
    protected $_storeManager;


    public function __construct(
        Context $context,
        //EasingTypes $easingTypes,
        EncoderInterface $jsonEncoder,
        DirectoryList $directoryList,
        StoreManager $storeManager
    ){
        $this->_directoryList = $directoryList;
        //$this->_easingTypes = $easingTypes;
        $this->_jsonEncoder = $jsonEncoder;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_storeManager = $storeManager;

        parent::__construct($context);
    }

    /**
     * Check if imagezoom is enabled
     *
     * @return bool
     */
    public function isEnabledImageZoom()
    {
        return (bool) $this->_scopeConfig->getValue(self::SECTION . '/general/enabled', Scope::SCOPE_STORE);
    }

    /**
     * Check if loaing icon is enabled
     *
     * @return bool
     */
    public function isShownLoadingIcon()
    {
        return (bool) $this->_scopeConfig->getValue(self::SECTION . '/general/showLoadingIcon', Scope::SCOPE_STORE);
    }

    /**
     * Get options keys array for selected group
     *
     * @param $group
     * @return array|string
     */
    private function getGroupOptionsKeys($group)
    {
        return $this->_imageZoomOptions[$group];
    }

    /**
     * Get group options and values as associative array
     *
     * @param $group
     * @return array
     */
    public function getGroupOptions($group)
    {
        $optionsArray = [];
        $groups = $this->getGroupOptionsKeys($group);
        foreach($this->getGroupOptionsKeys($group) as $optionName){

            $rootGroup = self::ROOT_GROUP . '/';
            if($group == self::ROOT_GROUP){
                $rootGroup = '';
            }

            $value = $this->_scopeConfig->getValue(self::SECTION . '/' . $rootGroup . $group . '/' . $optionName,
                Scope::SCOPE_STORE);

            switch ($optionName){
                case 'zoomType':
                    $value = $this->_zoomTypes[$value];
                    $this->_zoomType = $value;
                    break;

                case 'lensShape':
                    $value = $this->_lensShape[$value];
                    break;

//                case 'easingType':
//                    $value = $this->getEasingType($value);
//                    break;

                case 'loadingIcon':
                    if($this->isShownLoadingIcon()){
                        $value = $this->_directoryList->getUrlPath('media') . '/imagezoom_loader/' . $value;
                        if(empty($value)){
                            $value = true;
                        }
                    } else {
                        $value = false;
                    }
                    break;

                case 'tint':
                    $value = (bool) $value;
                    if($this->_zoomType == 'lens' || $this->_zoomType == 'inner'){
                        $value = false;
                    }
                    break;

                case 'borderSize':
                case 'zoomWindowPosition':
                case 'tintOpacity':
                case 'lensOpacity':
                case 'zoomWindowOffsetX':
                case 'zoomWindowOffsetY':
                //case 'easingDuration':
                case 'easingAmount':
                case 'lensSize':
                case 'zoomWindowWidth':
                case 'zoomWindowHeight':
                case 'zoomWindowFadeIn':
                case 'zoomWindowFadeOut':
                case 'lensFadeIn':
                case 'lensFadeOut':
                case 'lensBorderSize':
                case 'zIndex':
                    $value = floatval($value);
                    break;

                case 'cursor':
                case 'borderColour':
                case 'lensColour':
                case 'tintColour':
                case 'lensBorderColour':
                case 'zoomBackground':
                    $value = (string) $value;
                    break;

                default:
                    $value = (bool) $value;
            }

            $optionsArray[$optionName] = $value;
        }

        return $optionsArray;
    }

    /**
     * Get easing type value
     *
     * @param $key
     * @return string/bool
     */
//    private function getEasingType($key)
//    {
//        $easingTypes = $this->_easingTypes->getEasingTypes();
//        if(array_key_exists($key, $easingTypes)){
//            return $easingTypes[$key];
//        }
//        return 'linear';
//    }

    /**
     * Get imagezoom options
     *
     * @return array
     */
    public function getOptions()
    {
        $options = [];

        foreach($this->_imageZoomOptions as $group => $groupOptions){
            $options = array_merge($options, $this->getGroupOptions($group));
        }
        $result = array_merge($options, $this->responsiveOptions());
        return $result;
    }

    /**
     * Responsive elevate zoom images
     *
     * @return array
     */
    private function responsiveOptions()
    {
        $range = $this->_scopeConfig->getValue(self::SECTION . '/general/responsiveRange', Scope::SCOPE_STORE);

        $rangeValues = array(220, 900);

        if(strpos($range, '-')){
            $rangeValues = explode('-', $range);
        }

        $options = [
            'minRespondRange' => $rangeValues[0],
            'maxRespondRange' => $rangeValues[1],
            'respond' => array(
                array(
                    'range' => $range,
                    'zoomType' => 'inner',
                    'tint' => false
                )
            )
        ];
        return $options;
    }

    /**
     * Get config options in json format
     *
     * @return string
     */
    public function getOptionsJson()
    {
        $options = $this->getOptions();
        $result = $this->_jsonEncoder->encode($options);
        return $result;
    }

    public function getLoadingIcon()
    {
        $config = $this->_scopeConfig->getValue(self::SECTION . '/general/loadingIcon', Scope::SCOPE_STORE);
        $path = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $path . self::IMAGEZOOM_MEDIA_DIR . '/' .  $config;
    }

}