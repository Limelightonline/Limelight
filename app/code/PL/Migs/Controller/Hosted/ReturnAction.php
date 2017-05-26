<?php
/**
 * PL Development.
 *
 * @category    PL
 * @author      Linh Pham <plinh5@gmail.com>
 * @copyright   Copyright (c) 2016 PL Development. (http://www.polacin.com)
 */
namespace PL\Migs\Controller\Hosted;

/** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
class ReturnAction extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \PL\Migs\Helper\Data
     */
    protected $migsHelper;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;

    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $order;

    /**
     * @var \Magento\Sales\Model\Order\Status\HistoryFactory
     */
    protected $orderHistoryFactory;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $session;

    /**
     * @var \PL\Migs\Logger\PLLogger
     */
    protected $plLogger;

    /**
     * @var \PL\Migs\Model\Api\PaymentRequest
     */
    protected $paymentRequest;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Model\Context $test
     * @param \PL\Migs\Helper\Data $migsHelper
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Sales\Model\Order\Status\HistoryFactory $orderHistoryFactory
     * @param \Magento\Checkout\Model\Session $session
     * @param \PL\Migs\Logger\PLLogger $plLogger
     * @param \PL\Migs\Model\Api\PaymentRequest $paymentRequest
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        \Magento\Framework\Model\Context $test,
        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        \PL\Migs\Helper\Data $migsHelper,
        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        \Magento\Sales\Model\OrderFactory $orderFactory,
        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        \Magento\Sales\Model\Order\Status\HistoryFactory $orderHistoryFactory,
        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        \Magento\Checkout\Model\Session $session,
        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        \PL\Migs\Logger\PLLogger $plLogger,
        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        \PL\Migs\Model\Api\PaymentRequest $paymentRequest,
        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->migsHelper = $migsHelper;
        $this->orderFactory = $orderFactory;
        $this->orderHistoryFactory = $orderHistoryFactory;
        $this->session = $session;
        $this->plLogger = $plLogger;
        $this->paymentRequest = $paymentRequest;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    public function execute()
    {
        $params = $this->getRequest()->getParams();
        if (isset($params['vpc_SecureHash']) && $this->validateReceipt($params)) {
            if (isset($params['vpc_MerchTxnRef'])) {
                $incrementId = $params['vpc_MerchTxnRef'];
                $txnResponseCode = $params['vpc_TxnResponseCode'];
                $order = $this->getOrder($incrementId);
                if ($order->getId()) {
                    if ($txnResponseCode =='0' || $txnResponseCode == '00') {
                        $this->paymentRequest->success($order, $params);
                        $this->_redirect('checkout/onepage/success');
                    } else {
                        $this->paymentRequest->cancel($order, $params);
                        $this->messageManager->addError(__('You have cancelled the order. Please try again'));
                        $this->_redirect('checkout/cart');
                    }
                }
            }
        }
    }

    /**
     * @param $params
     * @return bool
     */
    public function validateReceipt($params)
    {
        $storeId = $this->storeManager->getStore()->getId();
        $hashType = $this->migsHelper->getPaymentConfigData('secure_hash_type', 'migs_hosted', $storeId);
        $encryptor = $this->_objectManager->get('\Magento\Framework\Encryption\EncryptorInterface');
        $secure_secret = $encryptor->decrypt(
            $this->migsHelper->getPaymentConfigData('secure_secret', 'migs_hosted', $storeId)
        );
        $md5HashData = $secure_secret;
        ksort($params);
        if ($hashType == 'MD5') {
            foreach ($params as $key => $value) {
                if ($key != 'vpc_SecureHash' && strlen($value) > 0) {
                    $md5HashData .= $value;
                }
            }
            return strtoupper($params['vpc_SecureHash']) == strtoupper(md5($md5HashData));
        }

        if ($hashType == 'SHA256') {
            $hashString='';
            foreach ($params as $key => $value) {
                if ($key != 'vpc_SecureHash' && $key != 'vpc_SecureHashType' && strlen($value) > 0) {
                    $hashString .= $key . "=" . $value . "&";
                }
            }
            return strtoupper($params['vpc_SecureHash']) == strtoupper(hash_hmac('SHA256', rtrim($hashString, "&"), pack('H*', $secure_secret)));
        }
    }

    /**
     * @param $incrementId
     * @return \Magento\Sales\Model\Order
     */
    protected function getOrder($incrementId)
    {
        if (!$this->order) {
            $this->order = $this->orderFactory->create()->loadByIncrementId($incrementId);
        }
        return $this->order;
    }
}
