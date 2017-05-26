<?php
/**
 * PL Development.
 *
 * @category    PL
 * @author      Linh Pham <plinh5@gmail.com>
 * @copyright   Copyright (c) 2016 PL Development. (http://www.polacin.com)
 */
namespace PL\Migs\Model\Source;

/** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
class Securehashtype implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Possible actions on order place
     *
     * @return array
     */
    public function toOptionArray()
    {
        /** @noinspection PhpDeprecationInspection */
        return [
            [
                'value' => 'MD5',
                'label' => __('MD5'),
            ],
            [
                'value' => 'SHA256',
                'label' => __('SHA256'),
            ]
        ];
    }
}
