<?php

/**
 * Created by PhpStorm.
 * User: Mr.Zhou
 * Date: 2019/1/16
 * Time: 9:21 AM
 */
include_once "./vendor/autoload.php";

$config = [
    'accessId' => 'A00001',
    'secret' => 'MIICdgIBADANBgkqhkiG9w0BAQEFAASCAmAwggJcAgEAAoGBALHHnteMDckJNlPxZ7eMzxSDmH4lTMeD6I3EvpVKad8Sw4apQvIG9ZY7ZHeUKKTOlsU0yBOp432BheP74EdU1aljnMqXpFNn+bEgTXpXCzaIdJlij9H4y/2m//mGE9l1OX2EVHZKSmeMY/GihZlMD6tP3yJ8QdolBZI/3CgH7BLDAgMBAAECgYAvkQioBXoeww89MIcerlct1vPzNImxjFKps+2GRk3DeOLF4f3eggwtsSB1ejfRuNDQXQn3cOpER2aKlHbyvvkXkNhrd/lCjpk6wtDYQsq/eeQ7wC8Am6hQ2d8cKySCl5LrpHHzkGkTv1DHw7rNKrMR03ahJWXsyPcqrbhvBMwrMQJBAPlh95E8wPSsqqYA/74o7Iqxa7nq9osXT6t5xrJc2CpI2go4OK1Da1zOI+mCbNpnuA7PnWu9xam2cCmNAsTHGskCQQC2f0L3no9mtGmuB7M7xN4Me5pUlZqVRWzLKDUK3IPEHzUZs7WDQ77RqOJBrvdHxFpY3ZS+bDFYouUbck39vHsrAkEAiIgCKhnA6jO+GbRiT5HILwaDm/3vjKbuj0rUZcI+9qd7+CxfmzxWAzE4qBcn0UsHkdRIszvqg8fGEHmLEoCPQQJAZWT3lBRooCuEu8hTcNXEeTMDYBNuu5jDBWzla49xNjoQiqMqKjAtiNdIPi4z/Y++krkpt1LtZ825dTJg2qUp2QJAMdlZbhYPL99fjhsbUS+xNisTczoi9Y+PEh+expEvfnTIj/YqKHtVdCdIPxktews831vU14GF+UwWFEZQYLt65w==',
    'uri' => 'http://220.191.209.248:9100/tcjg/api',
    'timeout' => 10.0,
];

$park = new \CityBrain\Api\Park($config);
try {
//    $ret = $park->uploadHeartbeat("yb0001");
//    var_dump($ret);
//    exit;
    $ret = $park->uploadParkingInfo([
        'parkingCode' => 'yb0002',//停车场编号
        'parkingName' => '云泊测试停车场2',//停车场名称
        'address' => '杭州市西湖区银江科技产业园',//停车场地址
        'regionCode' => '330106',//行政区划
        'areaCode' => '05',//城区编号
        'priceType' => 2,//定价类型（1:政府定价; 2:市场定价 ;3:不收费）
        'inroadFeesType' => '',//路内停车收费类型 可空（00:核心区域;01:一类区域;02:二类区域;03:三类区域;21: 风景区）
        'feeScale' => '每小时5元',//收费标准
        'openTime' => '24小时',//开放时间
        'parkingPattern' => 1,//停车场模式 可空（1:自行式停车;2:机械式停车;3:混合式停车）
        'parkingLocate' => 1,//停车位置（1:路外;2:路内）
        'parkingType' => 1,//停车场类型（1:配建停车场;2:公共停车场;3:道路停车位）
        'attachedType' => '',//配建类型 可空（01:住宅类;02:体育设施类;03:旅馆类;04:办公楼;05:餐饮 娱乐类;06:医院;07:教育类;08:工业类;09:游览场所;10: 影剧院类;11:文化类;12:市场类;13:商场类;14:金融类;15: 商业类;16:交通枢纽）
        'buildType' => 1,//建筑类型 可空（1:地面停车场;2:地下停车库;3:地上停车楼）
        'totalBerthNum' => 100,//泊位总数
        'openBerthNum' => 100,//开放泊位数
        'bmapX' => '1',//百度经度坐标
        'bmapY' => '1',
        'gmapX' => '1',//高德经度坐标
        'gmapY' => '1',
        'tempTotalNum' => 80,//临停车位数
        'intrinsicTotal' => 50,//月租车位数
        'visitorTotalNum' => 80,//访客车位数
        'visitorTotalNu' => 0,//充电桩车位 数
        'uploadTime' => date("Y-m-d H:i:s"),//上传时间
    ]);
    var_dump($ret);
    exit;
} catch (\Exception $exception) {
    var_dump($exception->getMessage());
}
