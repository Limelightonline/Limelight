<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Osc
 * @copyright   Copyright (c) 2016 Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */
namespace Mageplaza\Osc\Model\System\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Methods
 * @package Mageplaza\Osc\Model\System\Config\Source\Payment
 */
class PaymentMethods implements ArrayInterface
{
    /**
     * @var \Magento\Checkout\Model\Type\Onepage
     */
    protected $_modelTypeOnepage;

    /**
     * @var \Magento\Payment\Helper\Data
     */
    protected $_paymentHelperData;

    /**
     * @param \Magento\Payment\Helper\Data $paymentHelperData
     */
    public function __construct(
        \Magento\Payment\Helper\Data $paymentHelperData
    ) {
        $this->_paymentHelperData = $paymentHelperData;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $options = [
            [
                'label' => __('-- Please select --'),
                'value' => '',
            ],
        ];

        $methods = $this->_paymentHelperData->getPaymentMethods();
        foreach(array_keys($methods) as $code){
            try{
                $model = $this->_paymentHelperData->getMethodInstance($code);
                $options[] = [
                    'label' => $model->getTitle(),
                    'value' => $model->getCode(),
                ];
            } catch (\Exception $e){
                continue;
            }
        }

        return $options;
    }
}
