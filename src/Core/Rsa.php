<?php
/**
 * Created by PhpStorm.
 * User: Mr.Zhou
 * Date: 2019/2/18
 * Time: 8:51 AM
 */

namespace CityBrain\Core;

class Rsa
{
    /**
     * 私钥
     * @var resource
     */
    protected $privateSecret;

    /**
     * Rsa constructor.
     * @param string $secret
     * @throws CityBrainException
     */
    public function __construct(string $secret)
    {
        $this->privateSecret = openssl_pkey_get_private($this->formatPriKey($secret));
        if (!$this->privateSecret) {
            throw new CityBrainException("密钥载入失败，请检查密钥是否正确");
        }
    }

    /**
     * 生成rsa签名信息
     * @param string $str
     * @return string
     * @throws CityBrainException
     */
    public function sign(string $str): string
    {
        $ret = openssl_sign($str, $sign, $this->privateSecret, 2);
        if (!$ret) {
            throw new CityBrainException("rsa私钥签名失败了");
        }
        return base64_encode($sign);
    }

    /**
     * rsa私钥加密
     * @param string $str
     * @return string
     * @throws CityBrainException
     */
    public function encrypt(string $str): string
    {
        $encrypt = '';
        foreach (str_split($str, 117) as $chunk) {
            if (openssl_private_encrypt($chunk, $encryptData, $this->privateSecret)) {
                $encrypt .= $encryptData;
            } else {
                throw new CityBrainException("rsa私钥加密失败了");
            }
        }
        return base64_encode($encrypt);
    }

    /**
     * 格式化私钥
     * @param $priKey
     * @return string
     */
    private function formatPriKey($priKey): string
    {
        return "-----BEGIN PRIVATE KEY-----\n" . chunk_split($priKey, 64, "\n") . '-----END PRIVATE KEY-----';
    }

    /**
     * 格式化公钥
     * @param $pubKey
     * @return string
     */
    private function formatPubKey($pubKey): string
    {
        return "-----BEGIN PUBLIC KEY-----\n" . chunk_split($pubKey, 64, "\n") . '-----END PUBLIC KEY-----';
    }

    /**
     * 释放资源
     */
    public function __destruct()
    {
        openssl_free_key($this->privateSecret);
    }
}