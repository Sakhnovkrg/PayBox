<?php

namespace Sakhnovkrg\Paybox;

/**
 * Class PayboxInvoiceData
 * @package Sakhnovkrg\Paybox
 */
class PayboxInvoiceData
{
    /**
     * @var string
     */
    protected $redirectUrl;

    /**
     * @var int
     */
    protected $paymentId;

    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    /**
     * @return int
     */
    public function getPaymentId()
    {
        return $this->paymentId;
    }

    /**
     * PayboxInvoiceData constructor.
     * @param \SimpleXMLElement $xml
     */
    public function __construct($xml)
    {
        $this->redirectUrl = (string) $xml->pg_redirect_url;
        $this->paymentId = (int) $xml->pg_payment_id;
    }
}
