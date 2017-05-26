<?php
/**
 * PL Development.
 *
 * @category    PL
 * @author      Linh Pham <plinh5@gmail.com>
 * @copyright   Copyright (c) 2016 PL Development. (http://www.polacin.com)
 */
namespace PL\Migs\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{

    /**
     * @var \Magento\Directory\Model\Config\Source\Country
     */
    protected $country;

    /**
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    protected $moduleList;

    /**
     * @var \PL\Migs\Logger\PLLogger
     */
    protected $plLogger;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Directory\Model\Config\Source\Country $country
     * @param \Magento\Framework\Module\ModuleListInterface $moduleList
     * @param \PL\Migs\Logger\PLLogger $plLogger
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Directory\Model\Config\Source\Country $country,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \PL\Migs\Logger\PLLogger $plLogger,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);
        $this->country = $country;
        $this->moduleList = $moduleList;
        $this->plLogger = $plLogger;
        $this->_storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param $field
     * @param $paymentMethodCode
     * @param null $storeId
     * @param bool|false $flag
     * @return bool|mixed
     */
    public function getPaymentConfigData($field, $paymentMethodCode, $storeId = null, $flag = false)
    {
        $path = 'payment/' . $paymentMethodCode . '/' . $field;
        if (!$storeId) {
            $storeId = $this->_storeManager->getStore()->getId();
        }
        if (!$flag) {
            /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
            return $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
            return $this->scopeConfig->isSetFlag($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        }
    }

    /**
     * @param $responseCode
     * @return \Magento\Framework\Phrase
     */
    public function getResponseDescription($responseCode)
    {
        switch ($responseCode) {
            case '0':
                $result = 'Transaction Successful';
                break;
            case '?':
                $result = 'Transaction status is unknown';
                break;
            case '1':
                $result = 'Unknown Error';
                break;
            case '2':
                $result = 'Bank Declined Transaction';
                break;
            case '3':
                $result = 'No Reply from Bank';
                break;
            case '4':
                $result = 'Expired Card';
                break;
            case '5':
                $result = 'Insufficient funds';
                break;
            case '6':
                $result = 'Error Communicating with Bank';
                break;
            case '7':
                $result = 'Payment Server System Error';
                break;
            case '8':
                $result = 'Transaction Type Not Supported';
                break;
            case '9':
                $result = 'Bank declined transaction (Do not contact Bank)';
                break;
            case 'A':
                $result = 'Transaction Aborted';
                break;
            case 'C':
                $result = 'Transaction Cancelled';
                break;
            case 'D':
                $result = 'Deferred transaction has been received and is awaiting processing';
                break;
            case 'F':
                $result = '3D Secure Authentication failed';
                break;
            case 'I':
                $result = 'Card Security Code verification failed';
                break;
            case 'L':
                $result = 'Shopping Transaction Locked (Please try the transaction again later)';
                break;
            case 'N':
                $result = 'Cardholder is not enrolled in Authentication scheme';
                break;
            case 'P':
                $result = 'Transaction has been received by the Payment Adaptor and is being processed';
                break;
            case 'R':
                $result = 'Transaction was not processed - Reached limit of retry attempts allowed';
                break;
            case 'S':
                $result = 'Duplicate SessionID (OrderInfo)';
                break;
            case 'T':
                $result = 'Address Verification Failed';
                break;
            case 'U':
                $result = 'Card Security Code Failed';
                break;
            case 'V':
                $result = 'Address Verification and Card Security Code Failed';
                break;
            default:
                $result = 'Unable to be determined';
        }
        return __($result);
    }
}
