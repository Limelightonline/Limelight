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


class Swatches
{
    public function aroundToHtml(\Magento\Swatches\Block\Product\Renderer\Configurable $subject, callable $proceed)
    {
        $return = $proceed();

        if($return){
            $subject->setTemplate(
                'TemplateMonster_ImageZoom::product/view/renderer.phtml'
            );
        }
        return $return;
    }
}