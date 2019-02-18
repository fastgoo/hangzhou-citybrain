<?php
/**
 * Created by PhpStorm.
 * User: Mr.Zhou
 * Date: 2019/1/16
 * Time: 9:44 AM
 */

namespace CarPay\Api;

use CarPay\Core\ApiUrl;
use CarPay\Core\CarPayException;
use CarPay\Core\Sign;
use CarPay\Core\Tool;

class Order
{
    /**
     * @var array
     */
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * 获取微信自动扣费回调响应
     * @return array
     * @throws CarPayException
     */
    public function getPayResponse()
    {
        $data = Tool::xmlToArray(file_get_contents("php://input"));
        if (!$data) {
            throw new CarPayException("解析微信回调参数失败，content: " . file_get_contents("php://input"));
        }
        if (!Sign::check($data, $this->config['key'], $data['sign_type'])) {
            throw new CarPayException("微信回调参数签名验证失败了");
        }
        return $data;
    }

    /**
     * 自动扣款接口
     * 服务商接入文档：https://pay.weixin.qq.com/wiki/doc/api/pap_sl_jt_v2.php?chapter=20_982&index=3
     * 普通商家接入文档：https://pay.weixin.qq.com/wiki/doc/api/pap_sl_jt_v2.php?chapter=20_982&index=3
     * @param array $params
     * @return bool
     * @throws \CarPay\Core\CarPayException
     */
    public function pay(array $params): bool
    {
        $req = $this->createParams();
        unset($req['jump_scene']);
        $req['body'] = $params['body'];
        $req['out_trade_no'] = $params['out_trade_no'];
        $req['total_fee'] = (int)$params['total_fee'];
        $req['spbill_create_ip'] = $params['spbill_create_ip'];
        $req['notify_url'] = $params['notify_url'];
        $req['trade_type'] = "PAP";
        $sceneInfo = [
            'start_time' => $params['start_time'],
            'end_time' => $params['end_time'],
            'charging_time' => $params['charging_time'],
            'plate_number' => $params['plate_number'],
            'parking_name' => $params['parking_name'],
        ];
        if (!empty($params['car_type'])) {
            $sceneInfo['car_type'] = $params['car_type'];
        }
        if (!empty($params['space_number'])) {
            $sceneInfo['openid'] = $params['openid'];
            $sceneInfo['space_number'] = $params['space_number'];
            unset($sceneInfo['plate_number']);
        }
        $req['scene_info'] = json_encode(['scene_info' => $sceneInfo], JSON_UNESCAPED_UNICODE);
        !empty($params['detail']) && $req['detail'] = $params['detail'];
        !empty($params['attach']) && $req['attach'] = $params['attach'];
        !empty($params['fee_type']) && $req['fee_type'] = $params['fee_type'];
        !empty($params['goods_tag']) && $req['goods_tag'] = $params['goods_tag'];
        $req['sign'] = Sign::make($req, $this->config['key'], $this->config['sign_type']);
        $res = Tool::post(ApiUrl::PAY_APPLY, $req);
        return $res ? true : false;
    }

    /**
     * 查询订单
     * @param array $params
     * @return array
     * @throws CarPayException
     */
    public function payQuery(array $params)
    {
        $req = $this->createParams();
        unset($req['jump_scene'], $req['version'], $req['trade_scene']);
        if (!empty($params['transaction_id'])) {
            $req['transaction_id'] = $params['transaction_id'];
        } elseif (!empty($params['out_trade_no'])) {
            $req['out_trade_no'] = $params['out_trade_no'];
        }
        $req['sign'] = Sign::make($req, $this->config['key'], $this->config['sign_type']);
        $res = Tool::post(ApiUrl::QUERY_ORDER_STATUS, $req);
        return $res;
    }

    /**
     * 退款申请
     * @param array $params
     * @return array
     * @throws CarPayException
     */
    public function refund(array $params)
    {
        $req = $this->createParams();
        $req['transaction_id'] = $params['transaction_id'];
        $req['out_trade_no'] = $params['out_trade_no'];
        $req['out_refund_no'] = $params['out_refund_no'];
        $req['total_fee'] = $params['total_fee'];
        $req['refund_fee'] = $params['refund_fee'];
        !empty($params['refund_fee_type']) && $req['refund_fee_type'] = $params['refund_fee_type'];
        !empty($params['refund_desc']) && $req['refund_desc'] = $params['refund_desc'];
        !empty($params['refund_account']) && $req['refund_account'] = $params['refund_account'];
        !empty($params['notify_url']) && $req['notify_url'] = $params['notify_url'];
        $req['sign'] = Sign::make($req, $this->config['key'], $this->config['sign_type']);
        $res = Tool::post(ApiUrl::REFUND, $req);
        return $res;
    }

    /**
     * 退款查询
     * @param array $params
     * @return array
     * @throws CarPayException
     */
    public function refundQuery(array $params)
    {
        $req = $this->createParams();

        if (!empty($params['refund_id'])) {
            $req['refund_id'] = $params['refund_id'];
        } elseif (!empty($params['out_trade_no'])) {
            $req['out_trade_no'] = $params['out_trade_no'];
        } elseif (!empty($params['transaction_id'])) {
            $req['transaction_id'] = $params['transaction_id'];
        } elseif (!empty($params['out_refund_no'])) {
            $req['out_refund_no'] = $params['out_refund_no'];
        }

        isset($params['offset']) && $req['offset'] = $params['offset'];
        $req['sign'] = Sign::make($req, $this->config['key'], $this->config['sign_type']);
        $res = Tool::post(ApiUrl::REFUND, $req);
        return $res;
    }

    /**
     * 根据日期获取对账单
     * @param string $date 20180101
     * @param string $type ALL | SUCCESS | REFUND | RECHARGE_REFUND
     * @param string $tarType GZIP
     * @return array
     * @throws CarPayException
     */
    public function getBillByDate(string $date, string $type = 'ALL', string $tarType = '')
    {
        $req = $this->createParams();
        $req['bill_date'] = $date;
        $req['bill_type'] = $type;
        $tarType && $req['tar_type'] = $tarType;
        $req['sign'] = Sign::make($req, $this->config['key'], $this->config['sign_type']);
        $res = Tool::post(ApiUrl::DOWNLOAD_BILL, $req);
        return $res;
    }

    /**
     * 创建请求参
     * @return array
     */
    protected function createParams(): array
    {
        $config = [
            'appid' => $this->config['appid'],
            'mch_id' => $this->config['mch_id'],
            'nonce_str' => Tool::createRandStr(32, 3),
            'sign_type' => $this->config['sign_type'],
            'trade_scene' => $this->config['trade_scene'],
            'version' => $this->config['version'],
            'jump_scene' => $this->config['jump_scene'],
        ];
        if (!empty($this->config['sub_appid'])) {
            $config['sub_appid'] = $this->config['sub_appid'];
            $config['sub_mch_id'] = $this->config['sub_mch_id'];
        }
        return $config;
    }
}