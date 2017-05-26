<?php
/**
 * PL Development.
 *
 * @category    PL
 * @author      Linh Pham <plinh5@gmail.com>
 * @copyright   Copyright (c) 2016 PL Development. (http://www.polacin.com)
 */
namespace PL\Migs\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Payment\Model\InfoInterface;

/** @noinspection PhpDeprecationInspection */

/** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
class Migs extends \Magento\Payment\Model\Method\Cc
{
    const METHOD_CODE = 'migs';

    // Credit Card URLs
    const CC_URL_LIVE = 'https://migs.mastercard.com.au/vpcdps';

    const STATUS_APPROVED = 'Approved';

    const PAYMENT_ACTION_AUTH_CAPTURE = 'authorize_capture';

    const PAYMENT_ACTION_AUTH = 'authorize';

    protected $_code = self::METHOD_CODE;

    /**
     * @var bool
     */
    protected $_isGateway = true;

    /**
     * @var bool
     */
    protected $_canCapture = true;

    /**
     * @var \PL\Migs\Helper\Data
     */
    protected $migsHelper;

    /**
     * @var \PL\Migs\Model\Curl
     */
    protected $curl;

    /**
     * @var \PL\Migs\Logger\PLLogger
     */
    protected $plLogger;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \PL\Migs\Helper\Data $migsHelper
     * @param \PL\Migs\Logger\PLLogger $plLogger
     * @param \PL\Migs\Model\Curl $curl
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Payment\Model\Method\Logger $logger
     * @param \Magento\Framework\Module\ModuleListInterface $moduleList
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \PL\Migs\Helper\Data $migsHelper,
        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        \PL\Migs\Logger\PLLogger $plLogger,
        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        \PL\Migs\Model\Curl $curl,
        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        \Magento\Framework\Model\Context $context,
        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        \Magento\Framework\Registry $registry,
        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        \Magento\Payment\Helper\Data $paymentData,
        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        \Magento\Payment\Model\Method\Logger $logger,
        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        /** @noinspection PhpDeprecationInspection */
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $moduleList,
            $localeDate,
            $resource,
            $resourceCollection,
            $data
        );
        $this->migsHelper = $migsHelper;
        $this->plLogger = $plLogger;
        $this->curl = $curl;
        $this->storeManager = $storeManager;
    }

    /**
     * get Geteway Url
     * @return string
     */
    public function getGatewayUrl()
    {
        return self::CC_URL_LIVE;
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validate()
    {

        /** @noinspection PhpDeprecationInspection */
        parent::validate();
        $paymentInfo = $this->getInfoInstance();
        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        if ($paymentInfo instanceof \Magento\Sales\Model\Order\Payment) {
            $paymentInfo->getOrder()->getBaseCurrencyCode();
        } else {
            $paymentInfo->getQuote()->getBaseCurrencyCode();
        }
        return $this;
    }

    /**
     * @param InfoInterface $payment
     * @param float $amount
     * @return $this
     * @throws LocalizedException
     */
    public function capture(InfoInterface $payment, $amount)
    {
        $this->setAmount($amount)->setPayment($payment);
        $result = $this->processRequest($payment);
        if ($result === false) {
            $message = __('There has been an error processing your payment. Please try later or contact us for help.');
            throw new LocalizedException($message);
        } else {
            // Check if there is a gateway error
            if ($result['vpc_TxnResponseCode'] == "0") {
                $payment->setStatus(self::STATUS_APPROVED)
                    ->setLastTransId($result['vpc_TransactionNo'])
                    ->setTransactionId($result['vpc_TransactionNo'])
                    ->setParentTransactionId($result['vpc_TransactionNo'])
                    ->setIsTransactionClosed(1);
            } else {
                throw new LocalizedException(__('Gateway Error: %1', $result['vpc_Message']));
            }
        }

        return $this;
    }

    /**
     * @param InfoInterface $payment
     * @return array|bool
     */
    protected function processRequest(InfoInterface $payment)
    {

        $date_expiry = substr($payment->getCcExpYear(), 2, 2)
            . str_pad($payment->getCcExpMonth(), 2, '0', STR_PAD_LEFT);
        $amount = $this->getAmount() * 100;
        if ($payment->getOrder()->getBaseCurrencyCode() == 'JPY' ||
            $payment->getOrder()->getBaseCurrencyCode() == 'ITL' ||
            $payment->getOrder()->getBaseCurrencyCode() == 'GRD'
        ) {
            $amount = $amount / 100;
        }
        $storeId = $payment->getOrder()->getStoreId();
        $request = [
            'vpc_Version' => '1',
            'vpc_Command' => 'pay',
            'vpc_MerchTxnRef' => $payment->getOrder()->getIncrementId(),
            'vpc_Merchant' => htmlentities(
                $this->migsHelper->getPaymentConfigData('username', 'commweb', $storeId)
            ),
            'vpc_OrderInfo' => $payment->getOrder()->getIncrementId(),
            'vpc_CardNum' => htmlentities($payment->getCcNumber()),
            'vpc_CardExp' => htmlentities($date_expiry),
            'vpc_CardSecurityCode' => htmlentities($payment->getCcCid()),
            'vpc_AccessCode' => htmlentities(
                $this->migsHelper->getPaymentConfigData('password', 'commweb', $storeId)
            ),
            'vpc_Amount' => htmlentities($amount),
            'vpc_CSCLevel' => 'N',
            'vpc_TicketNo' => ''
        ];

        $postRequestData = '';
        $amp = '';
        foreach ($request as $key => $value) {
            if (!empty($value)) {
                $postRequestData .= $amp . urlencode($key) . '=' . urlencode($value);
                $amp = '&';
            }
        }

        $this->curl->setConfig(['timeout' => 30]);
        $this->curl->write(\Zend_Http_Client::POST, $this->getGatewayUrl(), '1.1', [], $postRequestData);
        $response = $this->curl->read();
        if ($this->curl->getErrno()) {
            $this->curl->close();
            return false;
        }
        $this->curl->close();

        // Strip out header tags
        $response = preg_split('/^\r?$/m', $response, 2);
        $response = trim($response[1]);

        // Fill out the results
        $result = [];
        $pieces = explode('&', $response);
        foreach ($pieces as $piece) {
            $tokens = explode('=', $piece);
            $result[$tokens[0]] = $tokens[1];
        }
        //$this->plLogger->addPLDebug(print_r($result,1));
        return $result;
    }
}
