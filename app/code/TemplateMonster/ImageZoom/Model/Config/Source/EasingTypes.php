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

namespace TemplateMonster\ImageZoom\Model\Config\Source;

use TemplateMonster\ImageZoom\Helper\EasingTypes as EasingHelper;
use \Magento\Framework\Option\ArrayInterface;

class EasingTypes implements ArrayInterface
{
    protected $_easingTypes;

    public function __construct(
        EasingHelper $easingTypes
    )
    {
        $this->_easingTypes = $easingTypes;
    }

    public function toOptionArray()
    {
        $result = [];

        foreach($this->_easingTypes->getEasingTypes() as $key => $type){

            $result[] = array(
                'value' => $key,
                'label' => $type
            );
        }

        return $result;
    }
}

