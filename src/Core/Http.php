<?php
/**
 * Created by PhpStorm.
 * User: Mr.Zhou
 * Date: 2019/2/18
 * Time: 4:54 PM
 */

namespace CityBrain\Core;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Http
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var Rsa
     */
    protected $rsa;

    /**
     * Http constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->rsa = new Rsa($this->config['secret']);
        $this->client = new Client([
            'timeout' => $this->config['timeout']
        ]);
    }


    /**
     * post请求接口
     * @param string $url
     * @param array $data
     * @return array
     * @throws CityBrainException
     */
    public function post(string $url, array $data): array
    {
        $response = null;
        $requestData = [
            'cipher' => $this->rsa->encrypt(json_encode(['data' => $data])),
            'sign' => '',
            'accessID' => $this->config['accessId'],
        ];
        $requestData['sign'] = $this->rsa->sign($requestData['cipher']);
        try {
            $response = $this->client->post($this->config['uri'] . $url, ['form_params' => $requestData]);
        } catch (RequestException $exception) {
            $res = $exception->getResponse()->getBody()->getContents();
            $ret = json_decode($res);
            throw new CityBrainException("服务器异常：" . (!empty($ret->msg) ? $ret->msg : $res), $exception->getResponse()->getStatusCode());
        }
        if ($response->getStatusCode() !== 200) {
            throw new CityBrainException("返回状态码异常", $response->getStatusCode());
        }
        $content = $response->getBody()->getContents();
        $ret = json_decode($content, true);
        if (!$ret) {
            throw new CityBrainException("响应结果异常,json解析失败 error: " . $content);
        }
        if ($ret['resultCode'] != 200) {
            throw new CityBrainException("操作失败，" . $ret['msg']);
        }
        return $ret;
    }
}