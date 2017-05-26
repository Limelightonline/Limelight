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


class EasingTypes
{
    protected $_easingTypes = [
        'linear',
        'swing',
        'easeInQuad',
        'easeOutQuad',
        'easeInOutQuad',
        'easeInCubic',
        'easeOutCubic',
        'easeInOutCubic',
        'easeInQuart',
        'easeOutQuart',
        'easeInOutQuart',
        'easeInQuint',
        'easeOutQuint',
        'easeInOutQuint',
        'easeInExpo',
        'easeOutExpo',
        'easeInOutExpo',
        'easeInSine',
        'easeOutSine',
        'easeInOutSine',
        'easeInCirc',
        'easeOutCirc',
        'easeInOutCirc',
        'easeInElastic',
        'easeOutElastic',
        'easeInOutElastic',
        'easeInBack',
        'easeOutBack',
        'easeInOutBack',
        'easeInBounce',
        'easeOutBounce',
        'easeInOutBounce',
    ];

    public function getEasingTypes()
    {
        return $this->_easingTypes;
    }
}