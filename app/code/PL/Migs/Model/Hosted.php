<?php
/**
 * PL Development.
 *
 * @category    PL
 * @author      Linh Pham <plinh5@gmail.com>
 * @copyright   Copyright (c) 2016 PL Development. (http://www.polacin.com)
 */
namespace PL\Migs\Model;

use Magento\Framework\DataObject;

/** @noinspection PhpDeprecationInspection */

/** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
class Hosted extends \Magento\Payment\Model\Method\AbstractMethod
{
    const METHOD_CODE = 'migs_hosted';

    protected $_code = self::METHOD_CODE;

    // Local constants
    const COMMAND_PAY = 'pay';

    const COMMAND_CAPTURE = 'capture';

    const TRANSACTION_SOURCE_INTERNET = 'INTERNET';

    const TRANSACTION_SOURCE_MAILORDER = 'MAILORDER';

    const TRANSACTION_SOURCE_TELORDER = 'TELORDER';

    const SOURCE_SUBTYPE_SINGLE = 'SINGLE';

    const SOURCE_SUBTYPE_INSTALLMENT = 'INSTALLMENT';

    const SOURCE_SUBTYPE_RECURRING = 'RECURRING';

    const MAESTRO_CHQ = 'CHQ';

    const MAESTRO_SAV = 'SAV';

    const VPC_VERSION = '1';

    const VPC_URL = 'https://migs.mastercard.com.au/vpcpay';

    const EPS_SSL = 'ssl';

    const EPS_3D = 'threeDSecure';

    protected $_infoBlockType = 'PL\Migs\Block\Info\Hosted';

    /**
     * @var bool
     */
    protected $_canAuthorize = true;

    /**
     * @var bool
     */
    protected $_canRefund = true;

    /**
     * @var bool
     */
    protected $_canRefundInvoicePartial = true;

    /**
     * @var bool
     */
    protected $_canUseInternal = false;

    /**
     * @var bool
     */
    protected $_isInitializeNeeded = true;

    /**
     * @var array
     */
    protected $_allowCurrencyCode = array();

    /**
     * @var Api\PaymentRequest
     */
    protected $paymentRequest;

    /**
     * @var \PL\Migs\Helper\Data
     */
    protected $migsHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $resolver;

    /**
     * @var \PL\Migs\Logger\PLLogger
     */
    protected $plLogger;

    /**
     * @var \Magento\Directory\Model\CountryFactory
     */
    protected $countryFactory;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected $encryptor;

    /**
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     * @param \Magento\Framework\App\RequestInterface $request
     * @param Api\PaymentRequest $paymentRequest
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \PL\Migs\Helper\Data $migsHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Locale\ResolverInterface $resolver
     * @param \PL\Migs\Logger\PLLogger $plLogger
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Payment\Model\Method\Logger $logger
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        \Magento\Framework\App\RequestInterface $request,
        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        \PL\Migs\Model\Api\PaymentRequest $paymentRequest,
        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        \Magento\Framework\UrlInterface $urlBuilder,
        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        \PL\Migs\Helper\Data $migsHelper,
        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        \Magento\Framework\Locale\ResolverInterface $resolver,
        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        \PL\Migs\Logger\PLLogger $plLogger,
        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        \Magento\Directory\Model\CountryFactory $countryFactory,
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
        \Magento\Payment\Model\Method\Logger $logger,
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
            $resource,
            $resourceCollection,
            $data
        );
        $this->paymentRequest = $paymentRequest;
        $this->urlBuilder = $urlBuilder;
        $this->migsHelper = $migsHelper;
        $this->storeManager = $storeManager;
        $this->resolver = $resolver;
        $this->plLogger = $plLogger;
        $this->request = $request;
        $this->countryFactory = $countryFactory;
        $this->encryptor = $encryptor;
    }

    /**
     * Checkout redirect URL getter for onepage checkout (hardcode)
     *
     * @see \Magento\Checkout\Controller\Onepage::savePaymentAction()
     * @see \Magento\Quote\Model\Quote\Payment::getCheckoutRedirectUrl()
     * @return string
     */
    public function getCheckoutRedirectUrl()
    {
        return $this->urlBuilder->getUrl('migs/hosted/redirect', ['_secure' => $this->getRequest()->isSecure()]);
    }

    /**
     * Retrieve request object
     *
     * @return \Magento\Framework\App\RequestInterface
     */
    protected function getRequest()
    {
        return $this->request;
    }

    /**
     * @return string
     */
    public function getFormUrl()
    {
        return self::VPC_URL;
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
     * @param string $paymentAction
     * @param object $stateObject
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function initialize($paymentAction, $stateObject)
    {
        if ($paymentAction == 'order') {
            $order = $this->getInfoInstance()->getOrder();
            $order->setCustomerNoteNotify(false);
            $order->setCanSendNewEmailFlag(false);
            $comment = __('Redirecting to the payment gateway. Order #%1', $order->getIncrementId());
            $order->setCustomerNote($comment);
            $stateObject->setIsNotified(false);
            $stateObject->setState(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT);
            $stateObject->setStatus(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT);
        }
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getFormFields()
    {
        $paymentInfo = $this->getInfoInstance();
        $order = $paymentInfo->getOrder();
        $storeId = $order->getStoreId();
        $billingAddress = $order->getBillingAddress();
        $shippingAddress = $order->getShippingAddress();
        if (!$shippingAddress || !is_object($shippingAddress)) {
            /** @noinspection PhpUnusedLocalVariableInspection */
            $shippingAddress = $billingAddress;
        }

        $formFields = [];
        $formFields['vpc_Version'] = self::VPC_VERSION;
        $formFields['vpc_Command'] = self::COMMAND_PAY;
        $formFields['vpc_AccessCode'] = substr($this->getConfigData('access_code', $storeId), 0, 8);
        $formFields['vpc_MerchTxnRef'] = substr($order->getIncrementId(), 0, 40);
        $formFields['vpc_Merchant'] = substr($this->getConfigData('merchant_id', $storeId), 0, 16);
        $formFields['vpc_OrderInfo'] = $this->getOrderDescription($order);
        $formFields['vpc_Amount'] = $this->getGrandTotal($order);
        $formFields['vpc_Locale'] = substr($this->resolver->getLocale(), 0, 2);
        $formFields['vpc_ReturnURL'] = $this->cutQueryFromUrl($this->getReturnUrl());
        if ($this->getConfigData('ticket_no', $storeId)) {
            $formFields['vpc_TicketNo'] = substr($this->getConfigData('ticket_no', $storeId), 0, 15);
        }
        if (isset($formFields['vpc_Card']) && in_array($formFields['vpc_Card'], array(
                'Visa',
                'Mastercard'
            )) && $this->getConfigData('use_3d', $storeId)) {
            $formFields['vpc_Gateway'] = self::EPS_3D;
        } elseif (isset($formFields['vpc_Card'])) {
            $formFields['vpc_Gateway'] = self::EPS_SSL;
        }
        $formFields['vpc_SecureHash'] = $this->getHash($formFields, $storeId);
		if ($this->getConfigData('secure_hash_type') == 'SHA256') {
            $formFields['vpc_SecureHashType'] = 'SHA256';
        }
        ///$this->plLogger->addPLDebug(print_r($formFields, true));
        return $formFields;
    }

    /**
     * @param $formFields
     * @param bool|false $storeId
     * @return string
     */
    public function getHash($formFields, $storeId = false)
    {
        ksort($formFields);
        $secureSecret = $this->encryptor->decrypt(trim($this->getConfigData('secure_secret', $storeId)));
        if ($this->getConfigData('secure_hash_type') == 'MD5') {
            $md5HashData=$secureSecret;
            foreach ($formFields as $key => $value) {
                if (strlen($value) > 0) {
                    $md5HashData .= $value;
                }
            }
            return strtoupper(md5($md5HashData));
        }

        if ($this->getConfigData('secure_hash_type') == 'SHA256') {
            $hashString='';
            foreach ($formFields as $key => $value) {
                if (strlen($value) > 0) {
                    $hashString.= $key . "=" . $value . "&";
                }
            }
            return strtoupper(hash_hmac('SHA256', rtrim($hashString, "&"), pack('H*', $secureSecret)));
        }
    }

    /**
     * @param $order
     * @return string
     */
    public function getOrderDescription($order)
    {
        $description =  __('Order')." ".$order->getIncrementId();
        return $description;
    }

    /**
     * @param $url
     * @return mixed
     */
    protected function cutQueryFromUrl($url)
    {
        $explode = explode('?', $url);
        return $explode[0];
    }

    /**
     * @return string
     */
    public function getReturnUrl()
    {
        return $this->urlBuilder->getUrl('migs/hosted/return', ['_secure' => $this->getRequest()->isSecure()]);
    }

    /**
     * @param $order
     * @return float
     */
    public function getGrandTotal($order)
    {
        $amount = $order->getBaseGrandTotal();
        return round($amount * 100);
    }
}
