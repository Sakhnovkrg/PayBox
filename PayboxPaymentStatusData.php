<?php

namespace Sakhnovkrg\Paybox;

/**
 * Class PayboxPaymentStatusData
 * @package Sakhnovkrg\Paybox
 */
class PayboxPaymentStatusData
{
    /**
     * @var int
     */
    protected $paymentId;

    /**
     * @var string
     */
    protected $transactionStatus;

    /**
     * @var bool
     */
    protected $canReject;

    /**
     * @var bool
     */
    protected $testingMode;

    /**
     * @var bool
     */
    protected $captured;

    /**
     * @var string
     */
    protected $cardPan;

    /**
     * @var string
     */
    protected $createDate;

    /**
     * @return int
     */
    public function getPaymentId()
    {
        return $this->paymentId;
    }

    /**
     * @return string
     */
    public function getTransactionStatus()
    {
        return $this->transactionStatus;
    }

    /**
     * @return bool
     */
    public function isCanReject()
    {
        return $this->canReject;
    }

    /**
     * @return bool
     */
    public function isTestPayment()
    {
        return $this->testingMode;
    }

    /**
     * @return bool
     */
    public function isCaptured()
    {
        return $this->captured;
    }

    /**
     * @return string
     */
    public function getCardPan()
    {
        return $this->cardPan;
    }

    /**
     * @return string
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }

    /**
     * PayboxPaymentStatusData constructor.
     * @param $xml
     */
    public function __construct($xml)
    {
        $this->paymentId = (int) $xml->pg_payment_id;
        $this->transactionStatus = (string) $xml->pg_transaction_status;
        $this->canReject = (bool) $xml->pg_can_reject;
        $this->testingMode = (bool) $xml->pg_testing_mode;
        $this->captured = (bool) $xml->pg_captured;
        $this->cardPan = (string) $xml->pg_card_pan;
        $this->createDate = (string) $xml->pg_create_date;
    }
}
