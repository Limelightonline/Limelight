<?php

/**
 * Copyright ? 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mageplaza\Osc\Observer;

use Magento\Config\Model\ResourceModel\Config as ModelConfig;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\GiftMessage\Helper\Message;
use Mageplaza\Osc\Helper\Config as HelperConfig;

class OscConfigObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var HelperConfig
     */
    protected $_helperConfig;

    /**
     * @var ModelConfig
     */
    protected $_modelConfig;

    /**
     * GiftMessageConfigObserver constructor.
     *
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        HelperConfig $helperConfig,
        ModelConfig $modelConfig
    ) {
        $this->_helperConfig = $helperConfig;
        $this->_modelConfig  = $modelConfig;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $scopeId       = 0;
        $isGiftMessage = !$this->_helperConfig->isDisabledGiftMessage();
        $this->_modelConfig->saveConfig(
            Message::XPATH_CONFIG_GIFT_MESSAGE_ALLOW_ORDER,
            $isGiftMessage,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            $scopeId
        );
    }
}
