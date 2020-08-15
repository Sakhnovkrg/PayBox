<?php

namespace Sakhnovkrg\Paybox;

use SimpleXMLElement;

/**
 * Class PayboxSignatureHelper
 * @package Sakhnovkrg\Paybox
 * @link https://github.com/PayBox/module-drupal-commerce/blob/master/CommercePayboxSignature.php
 */
class PayboxSignatureHelper
{
    /**
     * @param string $url
     * @return string
     */
    public static function getScriptNameFromUrl($url)
    {
        $path = parse_url($url, PHP_URL_PATH);
        $len = strlen($path);
        if ($len == 0 || '/' == $path{$len - 1}) {
            return "";
        }
        return basename($path);
    }

    /**
     * @return string
     */
    public static function getOurScriptName()
    {
        return self::getScriptNameFromUrl($_SERVER['PHP_SELF']);
    }

    /**
     * @param string $scriptName
     * @param array $params
     * @param string $secretKey
     * @return string
     */
    public static function make($scriptName, $params, $secretKey)
    {
        $arrFlatParams = self::makeFlatParamsArray($params);
        return md5(self::makeSigStr($scriptName, $arrFlatParams, $secretKey));
    }

    /**
     * @param string $signature
     * @param string $scriptName
     * @param array $params
     * @param string $strSecretKey
     * @return bool
     */
    public static function check($signature, $scriptName, $params, $strSecretKey)
    {
        return (string)$signature === self::make($scriptName, $params, $strSecretKey);
    }

    /**
     * @param string $scriptName
     * @param array $params
     * @param string $strSecretKey
     * @return string
     */
    static function debug_only_SigStr($scriptName, $params, $strSecretKey)
    {
        return self::makeSigStr($scriptName, $params, $strSecretKey);
    }

    /**
     * @param string $scriptName
     * @param array $params
     * @param string $strSecretKey
     * @return string
     */
    private static function makeSigStr($scriptName, array $params, $strSecretKey)
    {
        unset($params['pg_sig']);

        ksort($params);

        array_unshift($params, $scriptName);
        array_push($params, $strSecretKey);

        return join(';', $params);
    }

    /**
     * @param array $params
     * @param string $parent_name
     * @return array|string[]
     */
    private static function makeFlatParamsArray($params, $parent_name = '')
    {
        $arrFlatParams = array();
        $i = 0;
        foreach ($params as $key => $val) {

            $i++;
            if ('pg_sig' == $key)
                continue;

            /**
             * Имя делаем вида tag001subtag001
             * Чтобы можно было потом нормально отсортировать и вложенные узлы не запутались при сортировке
             */
            $name = $parent_name . $key . sprintf('%03d', $i);

            if (is_array($val)) {
                $arrFlatParams = array_merge($arrFlatParams, self::makeFlatParamsArray($val, $name));
                continue;
            }

            $arrFlatParams += array($name => (string)$val);
        }

        return $arrFlatParams;
    }

    /**
     * @param string $scriptName
     * @param SimpleXMLElement $xml
     * @param string $strSecretKey
     * @return string
     */
    public static function makeXML($scriptName, $xml, $strSecretKey)
    {
        $arrFlatParams = self::makeFlatParamsXML($xml);
        return self::make($scriptName, $arrFlatParams, $strSecretKey);
    }

    /**
     * @param string $scriptName
     * @param SimpleXMLElement $xml
     * @param $strSecretKey
     * @return bool
     */
    public static function checkXML($scriptName, $xml, $strSecretKey)
    {
        if (!$xml instanceof SimpleXMLElement) {
            $xml = new SimpleXMLElement($xml);
        }
        $arrFlatParams = self::makeFlatParamsXML($xml);
        return self::check((string)$xml->pg_sig, $scriptName, $arrFlatParams, $strSecretKey);
    }

    /**
     * @param string $scriptName
     * @param SimpleXMLElement $xml
     * @param string $strSecretKey
     * @return string
     */
    public static function debug_only_SigStrXML($scriptName, $xml, $strSecretKey)
    {
        $arrFlatParams = self::makeFlatParamsXML($xml);
        return self::makeSigStr($scriptName, $arrFlatParams, $strSecretKey);
    }

    /**
     * @param SimpleXMLElement $xml
     * @param string $parent_name
     * @return array|string[]
     */
    private static function makeFlatParamsXML($xml, $parent_name = '')
    {
        if (!$xml instanceof SimpleXMLElement) {
            $xml = new SimpleXMLElement($xml);
        }

        $params = array();
        $i = 0;
        foreach ($xml->children() as $tag) {
            $i++;
            if ('pg_sig' == $tag->getName())
                continue;

            /**
             * Имя делаем вида tag001subtag001
             * Чтобы можно было потом нормально отсортировать и вложенные узлы не запутались при сортировке
             */
            $name = $parent_name . $tag->getName() . sprintf('%03d', $i);

            if ($tag->children()->count() > 0) {
                $params = array_merge($params, self::makeFlatParamsXML($tag, $name));
                continue;
            }

            $params += array($name => (string)$tag);
        }

        return $params;
    }
}
