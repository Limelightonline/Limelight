<?php

namespace TemplateMonster\LayoutSwitcher\Block;

use TemplateMonster\LayoutSwitcher\Helper\Data as LayoutSwitcherHelper;
use TemplateMonster\LayoutSwitcher\Model\Config\Source\Website as WebsiteSource;
use TemplateMonster\LayoutSwitcher\Model\Config\Source\Store as StoreSource;
use Magento\Framework\View\Element\Template;

/**
 * Switcher frontend block.
 *
 * @package TemplateMonster\LayoutSwitcher\Block
 */
class Switcher extends AbstractBlock
{
    /**
     * @var string
     */
    protected $_template = 'switcher.phtml';

    /**
     * @var WebsiteSource
     */
    protected $_websiteSource;

    protected $_storeSource;

    /**
     * Switcher constructor.
     *
     * @param WebsiteSource        $websiteSource
     * @param StoreSource          $storeSource
     * @param LayoutSwitcherHelper $helper
     * @param Template\Context     $context
     * @param bool                 $livedemoMode
     * @param array                $data
     */
    public function __construct(
        WebsiteSource $websiteSource,
        StoreSource $storeSource,
        LayoutSwitcherHelper $helper,
        Template\Context $context,
        $livedemoMode = false,
        array $data = []
    )
    {
        $this->_websiteSource = $websiteSource;
        $this->_storeSource = $storeSource;
        parent::__construct($helper, $context, $livedemoMode, $data);
    }

    /**
     * Get form action url.
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('layoutswitcher/index/index');
    }

    /**
     * Get reset post action.
     *
     * @return string
     */
    public function getResetPostAction()
    {
        return $this->_helper->getResetPostAction();
    }

    /**
     * Get theme options.
     *
     * @return array
     */
    public function getThemeOptions()
    {
        return $this->_websiteSource->toOptionArray();
    }

    public function getHomepageOptions()
    {
        return $this->_storeSource->toOptionArray();
    }

    /**
     * Get layout types.
     *
     * @return array
     */
    public function getLayoutTypes()
    {
        return $this->_helper->getLayoutTypes();
    }

    /**
     * Get layout options.
     *
     * @param string $type
     *
     * @return array
     */
    public function getLayoutOptions($type)
    {
        return $this->_helper->getLayouts($type);
    }

    /**
     * Check is current theme.
     *
     * @param string $theme
     *
     * @return bool
     */
    public function isCurrentTheme($theme)
    {
        /** @var \Magento\Store\Model\Store $store */
        $store = $this->_storeManager->getStore();

        return $theme === $store->getWebsite()->getCode();
    }

    /**
     * Check is current layout.
     *
     * @param string $type
     * @param string $layout
     *
     * @return bool
     */
    public function isCurrentLayout($type, $layout)
    {
        return $layout === $this->_helper->getCookieValue($type);
    }
}