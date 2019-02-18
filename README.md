<p align="center">
  微信支付 ● 车主平台API
</p>
<p align="center">对接微信支付车主平台的API接口SDK，主要是为线下停车微信自动扣费提供便捷方式</p>

<p align="center">
  <a href="https://github.com/fastgoo/wxpay-car"><img src="https://img.shields.io/badge/license-MIT-brightgreen.svg"></a> 
 <a href="https://github.com/fastgoo/wxpay-car"><img src="https://img.shields.io/badge/php->=7-brightgreen.svg"></a> 
 <a href="https://pay.weixin.qq.com/wiki/doc/api/pap_sl_jt_v2.php?chapter=20_952&index=1"><img src="https://img.shields.io/badge/微信支付文档-服务商-2077ff.svg"></a>
 <a href="https://pay.weixin.qq.com/wiki/doc/api/pap_jt_v2.php?chapter=20_952&index=1"><img src="https://img.shields.io/badge/微信支付文档-普通商家-2077ff.svg"></a>
</p>

---
### 已实现接口
- 用户状态查询
- 用户签约
- 用户入场通知
- 申请扣款
- 查询订单
- 申请退款
- 查询退款
- 车牌状态变更异步回调
- 扣费接口异步回调

#### 初始化实例
```php
$config = [
    'mch_id' => '',//商户号
    'appid' => '',//商户号所绑定的公众号APPID
    'appsecret' => '',//商户号所绑定的公众号APPSECRET
    'key' => '',//微信支付的 加密key，在商户平台中可以看的到
    //'sub_mch_id' => '',//子商户的商户号，如果不是服务商没有子商户号的话，这个字段则不存在
    //'sub_appid' => '',//子商户绑定的appid，如果不是服务商没有子商户号的话，这个字段则不存在
    //'sub_appsecret' => '',//子商户绑定的appsecret，如果不是服务商没有子商户号的话，这个字段则不存在
    'sign_type' => 'HMAC-SHA256',//签名类型
    'trade_scene' => 'PARKING',//场景值
    'version' => '2.0',//版本号，固定的
    'jump_scene' => 'APP',//跳转场景：H5 | APP
];
$client = \CarPay\CarClient::init($config);
```

#### 用户状态查询
说明：这里请求微信的接口需要catch异常错误，所有的非成功的返回结果都是属于异常错误
```php
try {
    switch ($client->user()->getState($pnumber)) {
        case "NORMAL"://正常用户，已开通车主服务，且已授权访问
            //do something...
            break;
        case "PAUSED"://已暂停车主服务
            //do something...
            break;
        case "OVERDUE"://用户已开通车主服务，但欠费状态。提示用户还款，请跳转到车主服务
            //do something...
            break;
        case "UNAUTHORIZED"://用户未授权使用当前业务，或未开通车主服务。请跳转到授权接口
            //根据code获取到openid，然后获取授权所需的参数，传给 小程序 | h5 | APP 唤起微信签约
            $code = "";//code是需要授权才可以拿的到的，可以参考微信用户授权相关的流程
            $pnumber = "";
            $openid = $client->user()->getOpenidByCode($code);
            $authInfo = $client->user()->getAuthSign($openid, $pnumber);
            print_r($authInfo);
            break;
        default://异常状态类型
    }
} catch (\CarPay\Core\CarPayException $carPayException) {
    var_dump($carPayException->getMessage());
}
```
