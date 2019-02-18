<?php
/**
 * Created by PhpStorm.
 * User: Mr.Zhou
 * Date: 2019/1/15
 * Time: 4:54 PM
 */

namespace CarPay\Core;

use GuzzleHttp\Client;

class Tool
{

    /**
     * post请求微信接口
     * @param string $url
     * @param array $data
     * @return array
     * @throws CarPayException
     */
    public static function post(string $url, array $data): array
    {
        //var_dump(self::arrayToXml($data));exit;
        $response = (new Client(['timeout' => 3.0]))->post($url, ['body' => self::arrayToXml($data)]);
        if ($response->getStatusCode() !== 200) {
            throw new CarPayException("微信返回状态码异常", $response->getStatusCode());
        }
        $content = $response->getBody()->getContents();
        $ret = self::xmlToArray($content);
        if (!$ret) {
            throw new CarPayException("微信响应结果异常,xml解析失败 error: " . $content);
        }
        if ($ret['return_code'] != "SUCCESS" || $ret['result_code'] != "SUCCESS") {
            throw new CarPayException("接口调用失败 error: " . $content);
        }
        return $ret;
    }

    /**
     * 生成随机数
     * @param $len
     * @param int $type
     * @return string
     */
    public static function createRandStr($len, $type = 1): string
    {
        switch ($type) {
            case 1:
                $chars = '123456789abcdefghijklmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
                break;
            case 2:
                $chars = '123456789abcdefghijklmnpqrstuvwxyz';
                break;
            case 3:
                $chars = '123456789ABCDEFGHJKLMNPQRSTUVWXYZ';
                break;
            default:
                $chars = '123456789abcdefghijklmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
        }
        return substr(str_shuffle(str_repeat($chars, rand(5, 8))), 0, $len);
    }

    /**
     * 数组转xml格式字符串
     * @param array $params
     * @return string
     */
    public static function arrayToXml(array $params): string
    {
        $xml = '<xml>';
        foreach ($params as $key => $val) {
            $xml .= '<' . $key . '>' . $val . '</' . $key . '>';
        }
        $xml .= '</xml>';
        return $xml;
    }

    /**
     * xml字符串转数组
     * @param $xml
     * @return array
     */
    public static function xmlToArray(string $xml): array
    {
        libxml_disable_entity_loader(true);
        $arr = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $arr ?: [];
    }
}