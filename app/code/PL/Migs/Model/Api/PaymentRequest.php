<?php
/**
 * PL Development.
 *
 * @category    PL
 * @author      Linh Pham <plinh5@gmail.com>
 * @copyright   Copyright (c) 2016 PL Development. (http://www.polacin.com)
 */
namespace PL\Migs\Model\Api;

use Magento\Framework\DataObject;

class PaymentRequest extends DataObject
{
    const DEFAULT_STATUS_NEW = 'pending';

    const DEFAULT_STATUS_PENDING_PAYMENT = 'pending_payment';

    const DEFAULT_STATUS_PROCESSING = 'processing';

    /**
     * @var \PL\Migs\Helper\Data
     */
    protected $migsHelper;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $appState;

    /**
     * @var \PL\Migs\Logger\PLLogger
     */
    protected $plLogger;

    /**
     * @var \Magento\Sales\Model\Order\Email\Sender\OrderSender
     */
    protected $orderSender;

    /**
     * @var \Magento\Sales\Model\Order\Email\Sender\InvoiceSender
     */
    protected $invoiceSender;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $session;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \PL\Migs\Helper\Data $migsHelper
     * @param \PL\Migs\Logger\PLLogger $plLogger
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender
     * @param \Magento\Sales\Model\Order\Email\Sender\InvoiceSender $invoiceSender
     * @param \Magento\Checkout\Model\Session $session
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        \PL\Migs\Helper\Data $migsHelper,
        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        \PL\Migs\Logger\PLLogger $plLogger,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender,
        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        \Magento\Sales\Model\Order\Email\Sender\InvoiceSender $invoiceSender,
        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        \Magento\Checkout\Model\Session $session,
        array $data = []
    ) {
        parent::__construct($data);
        $this->migsHelper = $migsHelper;
        $this->plLogger = $plLogger;
        $this->appState = $context->getAppState();
        $this->orderSender = $orderSender;
        $this->invoiceSender = $invoiceSender;
        $this->session = $session;
        $this->jsonHelper = $jsonHelper;
    }

    /**
     * @param $order
     */
    public function invoice($order)
    {
        $this->checkOrderStatus($order);
        if (!$order->hasInvoices() && $order->canInvoice()) {
            $invoice = $order->prepareInvoice();
            if ($invoice->getTotalQty() > 0) {
                /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
                $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_ONLINE);
                $invoice->setTransactionId($order->getPayment()->getTransactionId());
                $invoice->register();
                $invoice->addComment(__('Automatic invoice.'), false);
                $invoice->save();
                $this->invoiceSender->send($invoice);
            }
        }
    }

    /**
     * @param $order
     * @param array $responseData
     */
    public function success($order, $responseData = [])
    {
        $this->checkOrderStatus($order);
        if ($order->getId()) {
            $additionalData = $this->jsonHelper->jsonEncode($responseData);
            $order->getPayment()->setTransactionId($responseData['vpc_TransactionNo']);
            $order->getPayment()->setLastTransId($responseData['vpc_TransactionNo']);
            $order->getPayment()->setAdditionalInformation('payment_additional_info', $additionalData);
            $paymentMethodCode = $order->getPayment()->getMethod();
            $processingOrderStatus = $this->getProcessingStatus($paymentMethodCode);
            $order->setStatus($processingOrderStatus);
            $note = __('Approved the payment online. Transaction ID: "%1"', $responseData['vpc_TransactionNo']);
            $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING);
            $order->addStatusHistoryComment($note);
            //$order->setCustomerNoteNotify(true);
            $order->save();
            $this->orderSender->send($order);
            $this->invoice($order);
            if ($this->appState->getAreaCode()=='frontend') {
                $this->session->getQuote()->setIsActive(false)->save();
            }
        }
    }

    /**
     * @param $order
     * @param array $responseData
     */
    public function cancel($order, $responseData = [])
    {
        $this->checkOrderStatus($order);
        if ($order->getId()) {
            $additionalData = $this->jsonHelper->jsonEncode($responseData);
            $order->getPayment()->setTransactionId($responseData['vpc_TransactionNo']);
            $order->getPayment()->setLastTransId($responseData['vpc_TransactionNo']);
            $order->getPayment()->setAdditionalInformation('payment_additional_info', $additionalData);

            // Cancel order
            $note = __('Payment Gateway was declined. Transaction ID: "%1"', $responseData['vpc_TransactionNo']);
            $order->setState(\Magento\Sales\Model\Order::STATE_CANCELED);
            $order->setStatus(\Magento\Sales\Model\Order::STATE_CANCELED);
            $order->setCustomerNoteNotify(false);
            $order->addStatusHistoryComment($note);
            $order->cancel()->save();

            /*$order->setActionFlag(\Magento\Sales\Model\Order::STATE_CANCELED, true);
            if ($order->canCancel()) {
                $order->cancel()->save();
            }*/
        }

        if ($this->appState->getAreaCode()=='frontend') {
            //$this->session->restoreQuote();
        }
    }

    /**
     * @param $order
     */
    public function checkOrderStatus($order)
    {
        if ($order->getId()) {
            $state = $order->getState();
            switch ($state) {
                case \Magento\Sales\Model\Order::STATE_HOLDED:
                case \Magento\Sales\Model\Order::STATE_CANCELED:
                case \Magento\Sales\Model\Order::STATE_CLOSED:
                case \Magento\Sales\Model\Order::STATE_COMPLETE:
                    break;
            }
        }
    }

    /**
     * @param $code
     * @return bool|mixed|string
     */
    public function getOrderStatus($code)
    {
        $status = $this->migsHelper->getPaymentConfigData($code, 'order_status');
        if (empty($status)) {
            $status = self::DEFAULT_STATUS_NEW;
        }
        return $status;
    }

    /**
     * @param $code
     * @return bool|mixed|string
     */
    public function getPendingStatus($code)
    {
        $status = $this->migsHelper->getPaymentConfigData($code, 'pending_status');
        if (empty($status)) {
            $status = self::DEFAULT_STATUS_PENDING_PAYMENT;
        }
        return $status;
    }

    /**
     * @param $code
     * @return bool|mixed|string
     */
    public function getProcessingStatus($code)
    {
        $status = $this->migsHelper->getPaymentConfigData('processing_status', $code);
        if (empty($status)) {
            $status = self::DEFAULT_STATUS_PROCESSING;
        }
        return $status;
    }
}
