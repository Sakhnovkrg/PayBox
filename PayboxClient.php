<?php

namespace Sakhnovkrg\Paybox;

use Sakhnovkrg\Paybox\Exceptions\PayboxClientException;
use Sakhnovkrg\Paybox\Exceptions\PayboxServerException;

/**
 * Class PayboxClient
 * @package Sakhnovkrg\Paybox
 */
class PayboxClient
{
    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var int
     */
    protected $merchantId;
    /**
     * @var string
     */
    protected $secretKey;
    /**
     * @var string
     */
    protected $salt;
    /**
     * @var bool
     */
    protected $testingMode;

    /**
     * @return int
     */
    public function getMerchantId()
    {
        return $this->merchantId;
    }

    /**
     * @return string
     */
    public function getSecretKey()
    {
        return $this->secretKey;
    }

    /**
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @return bool
     */
    public function getIsTest()
    {
        return $this->testingMode;
    }

    /**
     * @param string $orderId
     * @param int|float $cost
     * @param string $description
     * @param string $currency
     * @return PayboxInvoice
     * @throws PayboxClientException
     */
    public function createInvoice($orderId, $cost, $description, $currency = 'KZT')
    {
        return new PayboxInvoice($this, $orderId, $cost, $description, $currency);
    }

    /**
     * @param $paymentId
     * @return PayboxPaymentStatusData
     * @throws PayboxClientException
     * @throws PayboxServerException
     */
    public function getPaymentStatus($paymentId)
    {
        $params = [
            'pg_merchant_id' => $this->merchantId,
            'pg_payment_id' => (integer)$paymentId,
            'pg_salt' => $this->salt
        ];

        return new PayboxPaymentStatusData($this->request('get_status.php', $params));
    }

    /**
     * @param string $scriptName
     * @param array $params
     * @return string
     */
    protected function getSignedQueryString($scriptName, array $params)
    {
        $params['pg_sig'] = PayboxSignatureHelper::make($scriptName, $params, $this->secretKey);

        return http_build_query($params);
    }

    /**
     * @param string $scriptName
     * @param array $params
     * @return \SimpleXMLElement
     * @throws PayboxClientException
     * @throws PayboxServerException
     */
    public function request($scriptName, $params)
    {
        $queryString = $this->getSignedQueryString($scriptName, $params);

        $url = $this->baseUrl . '/' . $scriptName . '?' . $queryString;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        if ($info['content_type'] != 'text/xml; charset=UTF-8') {
            throw new PayboxClientException('Не удалось выполнить запрос ' . $url);
        }

        $data = new \SimpleXMLElement($response);

        if ($data->pg_status == 'error') {
            $num = 'Ошибка №' . $data->pg_error_code;
            $msg = isset($data->pg_error_description) ? $num . ' — ' . $data->pg_error_description : $num;
            throw new PayboxServerException($msg, (int)$data->pg_error_code);
        }

        if (!PayboxSignatureHelper::checkXML($scriptName, $data, $this->secretKey)) {
            throw new PayboxClientException('Некорректная подпись запроса.');
        }

        return $data;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * @param string $baseUrl
     * @return $this
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
        return $this;
    }

    /**
     * PayboxClient constructor.
     * @param int $merchantId
     * @param string $secretKey
     * @param string $salt
     * @param bool $testingMode
     */
    public function __construct($baseUrl, $merchantId, $secretKey, $salt, $testingMode = false)
    {
        $this->baseUrl = $baseUrl;
        $this->merchantId = $merchantId;
        $this->secretKey = $secretKey;
        $this->salt = $salt;
        $this->testingMode = $testingMode;
    }
}
