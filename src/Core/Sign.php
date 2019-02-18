<?php
/**
 * Created by PhpStorm.
 * User: Mr.Zhou
 * Date: 2019/1/16
 * Time: 8:51 AM
 */

namespace CarPay\Core;

use function GuzzleHttp\Psr7\build_query;

class Sign
{
    /**
     * 验证签名信息
     * @param array $data
     * @param string $key
     * @param string $type
     * @return bool
     */
    public static function check(array $data, string $key, string $type): bool
    {
        $originalSign = $data['sign'];
        unset($data['sign']);
        $sign = self::make($data, $key, $type);
        if ($originalSign == $sign) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 生成签名字符串
     * @param array $params
     * @param string $key
     * @param string $type
     * @return string
     */
    public static function make(array $params, string $key, string $type): string
    {
        ksort($params, SORT_NATURAL | SORT_FLAG_CASE);
        $str = build_query($params);
        $key && $str .= "&key=" . $key;
        $str = urldecode($str);
        switch ($type) {
            case 'MD5':
                $sign = md5($str);
                break;
            case 'HMAC-SHA256':
                $sign = hash_hmac("sha256", $str, $key);
                break;
            default:
                $sign = '';
                break;
        }
        return strtoupper($sign);
    }


}