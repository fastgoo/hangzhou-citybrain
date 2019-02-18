<?php
/**
 * Created by PhpStorm.
 * User: Mr.Zhou
 * Date: 2019/1/15
 * Time: 4:47 PM
 */

namespace CarPay\Api;

use CarPay\Core\ApiUrl;
use CarPay\Core\CarPayException;
use CarPay\Core\Sign;
use CarPay\Core\Tool;
use GuzzleHttp\Client;

class  User
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
     * 获取车牌变更事件参数
     * 服务商参考文档：https://pay.weixin.qq.com/wiki/doc/api/pap_sl_jt_v2.php?chapter=20_912&index=11
     * 普通商家参考文档：https://pay.weixin.qq.com/wiki/doc/api/pap_jt_v2.php?chapter=20_912&index=11
     * @return array
     * @throws CarPayException
     */
    public function getCarResponse(): array
    {
        $data = Tool::xmlToArray(file_get_contents("php://input"));
        if (!$data) {
            throw new CarPayException("解析微信回调参数失败，content: " . file_get_contents("php://input"));
        }
        if (!Sign::check($data, $this->config['key'], $data['sign_type'])) {
            throw new CarPayException("微信回调参数签名验证失败了");
        }
        return [
            'plate_number' => $data['plate_number'],
            'event_type' => $data['vehicle_event_type'],
            'change_time' => $data['vehicle_event_createtime']
        ];
    }

    /**
     * 获取用户签约状态
     * 服务商参考文档：https://pay.weixin.qq.com/wiki/doc/api/pap_sl_jt_v2.php?chapter=20_932&index=10
     * 普通商家参考文档：https://pay.weixin.qq.com/wiki/doc/api/pap_jt_v2.php?chapter=20_93&index=10
     * @param string $plateNumber
     * @return string
     * @throws \CarPay\Core\CarPayException
     */
    public function getState(string $plateNumber): string
    {
        $req = $this->createParams();
        $req['plate_number'] = $plateNumber;
        $req['sign'] = Sign::make($req, $this->config['key'], $this->config['sign_type']);
        $res = Tool::post(ApiUrl::QUERY_USER_STATUS, $req);
        return $res['user_state'];
    }

    /**
     * 获取用户状态信息
     * @param string $plateNumber
     * @return array
     * @throws CarPayException
     */
    public function getStateInfo(string $plateNumber): array
    {
        $req = $this->createParams();
        $req['plate_number'] = $plateNumber;
        $req['sign'] = Sign::make($req, $this->config['key'], $this->config['sign_type']);
        $res = Tool::post(ApiUrl::QUERY_USER_STATUS, $req);
        return $res;
    }

    /**
     * 获取授权信息接口
     * 服务商参考文档：https://pay.weixin.qq.com/wiki/doc/api/pap_sl_jt_v2.php?chapter=20_932&index=10
     * 普通商家参考文档：https://pay.weixin.qq.com/wiki/doc/api/pap_jt_v2.php?chapter=20_93&index=10
     * @param string $openid
     * @param string $plateNumber
     * @param string $highWayType
     * @return array
     */
    public function getAuthSign(string $openid, string $plateNumber = '', string $highWayType = ''): array
    {
        $req = $this->createParams();
        unset($req['version'], $req['jump_scene']);
        $highWayType && $req['channel_type'] = $highWayType;
        $req['openid'] = $openid;
        if ($this->config['sub_appid']) {
            $req['sub_openid'] = $openid;
            unset($req['openid']);
        }
        if ($plateNumber) {
            $req['plate_number'] = $plateNumber;
        }
        $req['sign'] = Sign::make($req, $this->config['key'], $this->config['sign_type']);
        return $req;
    }

    /**
     * 根据code获取 openid
     * @param string $code
     * @return string
     */
    public function getOpenidByCode(string $code): string
    {
        $appId = $this->config['appid'];
        $appSecret = $this->config['appsecret'];
        if ($this->config['sub_appid']) {
            $appId = $this->config['sub_appid'];
            $appSecret = $this->config['sub_appsecret'];
        }
        $response = (new Client(['timeout' => 3.0]))->get("https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appId}&secret={$appSecret}&code={$code}&grant_type=authorization_code");
        if ($response->getStatusCode() != 200) {
            return '';
        }
        $content = $response->getBody()->getContents();
        $ret = json_decode($content, true);
        if (!$ret || empty($ret['openid'])) {
            return '';
        }
        return $ret['openid'];
    }

    /**
     * 车辆入场通知微信服务器
     * @param array $params
     * @return bool
     * @throws CarPayException
     */
    public function incomeNotify(array $params): bool
    {
        $req = $this->createParams();
        unset($req['jump_scene']);
        $sceneInfo = [
            'start_time' => $params['start_time'],
            'parking_name' => $params['parking_name'],
            'free_time' => $params['free_time'],
        ];
        if (!empty($params['notify_url'])) {
            $sceneInfo['notify_url'] = $params['notify_url'];
        }
        if (!empty($params['car_type'])) {
            $sceneInfo['car_type'] = $params['car_type'];
        }
        if (isset($params['space_number'])) {
            $sceneInfo['openid'] = $params['openid'];
            $sceneInfo['space_number'] = $params['space_number'];
            if (!empty($sceneInfo['notify_url'])) {
                unset($sceneInfo['notify_url']);
            }
            if (!empty($sceneInfo['plate_number'])) {
                unset($sceneInfo['plate_number']);
            }
        } else {
            $sceneInfo['plate_number'] = $params['plate_number'];
        }
        $req['scene_info'] = json_encode(['scene_info' => $sceneInfo], JSON_UNESCAPED_UNICODE);
        $req['sign'] = Sign::make($req, $this->config['key'], $this->config['sign_type']);
        $res = Tool::post(ApiUrl::USER_INCOME_NOTIFY, $req);
        return $res ? true : false;
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