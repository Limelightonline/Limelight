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

namespace TemplateMonster\ImageZoom\Plugin;

use TemplateMonster\ImageZoom\Block\Product\View\Gallery as ImageZoom;
use TemplateMonster\ImageZoom\Helper\Data as DataHelper;

class Gallery
{
    protected $_imageZoomBlock;
    protected $_dataHelper;
    protected $_i = 0;

    public function __construct(
        ImageZoom $imageZoomBlock,
        DataHelper $dataHelper
    ){
        $this->_imageZoomBlock = $imageZoomBlock;
        $this->_dataHelper = $dataHelper;
    }

    /**
     * Replace Gallery method with ImageZoom one if enabled
     *
     * @param $subject
     * @param callable $proceed
     * @return string
     * @TODO resolve issue with twice called toHtml method
     */
    public function aroundToHtml($subject, callable $proceed)
    {
        $this->_i++;
        if(!$this->_dataHelper->isEnabledImageZoom()){
            return $proceed();
        }
        return ($this->_i > 1) ? $this->_imageZoomBlock->toHtml(): '';
    }
}