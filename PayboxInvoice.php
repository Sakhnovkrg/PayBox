<?php

namespace Sakhnovkrg\Paybox;

use Sakhnovkrg\Paybox\Exceptions\PayboxClientException;

/**
 * Class PayboxInvoice
 * @package Sakhnovkrg\Paybox
 */
class PayboxInvoice
{
    /**
     * @var PayboxClient
     */
    protected $paybox;

    /**
     * @var string
     */
    protected $orderId;

    /**
     * @var int|float
     */
    protected $cost;

    /**
     * @var string
     */
    protected $currency;

    /**
     * @var array
     */
    protected $extraParams = [];

    /**
     * @var string
     */
    protected $description;

    /**
     * @param string $url
     * @return $this
     */
    public function setCheckUrl($url)
    {
        $this->extraParams['pg_check_url'] = $url;
        return $this;
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setResultUrl($url)
    {
        $this->extraParams['pg_result_url'] = $url;
        return $this;
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setRefundUrl($url)
    {
        $this->extraParams['pg_refund_url'] = $url;
        return $this;
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setCaptureUrl($url)
    {
        $this->extraParams['pg_capture_url'] = $url;
        return $this;
    }

    /**
     * @param string $method
     * @return $this
     */
    public function setRequestMethod($method)
    {
        $this->extraParams['pg_request_method'] = $method;
        return $this;
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setSuccessUrl($url)
    {
        $this->extraParams['pg_success_url'] = $url;
        return $this;
    }

    /**
     * @param string $method
     * @return $this
     */
    public function setSuccessUrlMethod($method)
    {
        $this->extraParams['pg_success_url_method'] = $method;
        return $this;
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setFailureUrl($url)
    {
        $this->extraParams['pg_failure_url'] = $url;
        return $this;
    }

    /**
     * @param string $method
     * @return $this
     */
    public function setFailureUrlMethod($method)
    {
        $this->extraParams['pg_failure_url_method'] = $method;
        return $this;
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setStateUrl($url)
    {
        $this->extraParams['pg_state_url'] = $url;
        return $this;
    }

    /**
     * @param string $method
     * @return $this
     */
    public function setStateUrlMethod($method)
    {
        $this->extraParams['pg_state_url_method'] = $method;
        return $this;
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setSiteUrl($url)
    {
        $this->extraParams['pg_site_url'] = $url;
        return $this;
    }

    /**
     * @param string $system
     * @return $this
     */
    public function setPaymentSystem($system)
    {
        $this->extraParams['pg_payment_system'] = $system;
        return $this;
    }

    /**
     * @param string $seconds
     * @return $this
     */
    public function setLifetime($seconds)
    {
        $this->extraParams['pg_lifetime'] = $seconds;
        return $this;
    }

    /**
     * @param string $val
     * @return $this
     */
    public function setParam1($val)
    {
        $this->extraParams['pg_param1'] = $val;
        return $this;
    }

    /**
     * @param string $val
     * @return $this
     */
    public function setParam2($val)
    {
        $this->extraParams['pg_param2'] = $val;
        return $this;
    }

    /**
     * @param string $val
     * @return $this
     */
    public function setParam3($val)
    {
        $this->extraParams['pg_param3'] = $val;
        return $this;
    }

    /**
     * @param string $phone
     * @return $this
     */
    public function setUserPhone($phone)
    {
        $this->extraParams['pg_user_phone'] = (string) $phone;
        return $this;
    }

    /**
     * @param string $email
     * @return $this
     */
    public function setUserEmail($email)
    {
        $this->extraParams['pg_user_contact_email'] = $email;
        return $this;
    }

    /**
     * @param string $ip
     * @return $this
     */
    public function setUserIp($ip)
    {
        $this->extraParams['pg_user_ip'] = $ip;
        return $this;
    }

    /**
     * @param int $flag
     * @return $this
     */
    public function setPostponePayment($flag)
    {
        $this->extraParams['pg_postpone_payment'] = $flag;
        return $this;
    }

    /**
     * @param string $lang
     * @return $this
     */
    public function setLanguage($lang)
    {
        $this->extraParams['pg_language'] = $lang;
        return $this;
    }

    /**
     * @param int $flag
     * @return $this
     */
    public function setRecurringStart($flag)
    {
        $this->extraParams['pg_recurring_start'] = $flag;
        return $this;
    }

    /**
     * @param int $months
     * @return $this
     */
    public function setRecurringLifetime($months)
    {
        $this->extraParams['pg_recurring_lifetime'] = $months;
        return $this;
    }

    /**
     * @return PayboxInvoiceData
     * @throws Exceptions\PayboxServerException
     * @throws PayboxClientException
     */
    public function getData()
    {
        $params = [
            'pg_merchant_id' => $this->paybox->getMerchantId(),
            'pg_amount' => $this->cost,
            'pg_salt' => $this->paybox->getSalt(),
            'pg_order_id' => $this->orderId,
            'pg_description' => $this->description,
            'pg_testing_mode' => (int)$this->paybox->getIsTest(),
            'pg_currency' => $this->currency
        ];

        $params = array_merge($params, $this->extraParams);
        return new PayboxInvoiceData($this->paybox->request('init_payment.php', $params));
    }

    /**
     * @param string $code
     * @return $this
     */
    public function setCurrency($code)
    {
        $this->currency = $code;
        return $this;
    }

    /**
     * @param string $key
     * @param mixed $val
     * @return $this
     */
    public function addExtraParam($key, $val)
    {
        $this->extraParams[$key] = $val;
        return $this;
    }

    /**
     * @param array $fields
     * @return $this
     */
    public function addExtraFields(array $fields)
    {
        $this->extraParams = array_merge($this->extraParams, $fields);
        return $this;
    }

    /**
     * PayboxInvoice constructor.
     * @param PayboxClient $paybox
     * @param int $orderId
     * @param int|float $cost
     * @param string $description
     * @param string $currency
     * @throws PayboxClientException
     */
    public function __construct(PayboxClient $paybox, $orderId, $cost, $description, $currency = 'KZT')
    {
        $this->paybox = $paybox;
        $this->orderId = $orderId;
        $this->cost = $cost;
        $this->currency = $currency;
        if (!$description) {
            throw new PayboxClientException('Укажите описание платежа.');
        }
        $this->description = $description;
    }
}
