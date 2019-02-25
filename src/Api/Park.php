<?php
/**
 * Created by PhpStorm.
 * User: Mr.Zhou
 * Date: 2019/1/16
 * Time: 9:44 AM
 */

namespace CityBrain\Api;

use CityBrain\Core\Http;

class Park
{

    /**
     * @var Http
     */
    protected $http;

    /**
     * Park constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->http = new Http($config);
    }

    /**
     * 上传停车场信息
     * @param array $params
     * @return array
     * @throws \CityBrain\Core\CityBrainException
     */
    public function uploadParkingInfo(array $params): array
    {
        $ret = [
            'parkingCode' => '',//停车场编号
            'parkingName' => '',//停车场名称
            'address' => '',//停车场地址
            'regionCode' => '',//行政区划
            'areaCode' => '',//城区编号
            'priceType' => 1,//定价类型（1:政府定价; 2:市场定价 ;3:不收费）
            'inroadFeesType' => '',//路内停车收费类型 可空（00:核心区域;01:一类区域;02:二类区域;03:三类区域;21: 风景区）
            'feeScale' => '',//收费标准
            'openTime' => '',//开放时间
            'parkingPattern' => 1,//停车场模式 可空（1:自行式停车;2:机械式停车;3:混合式停车）
            'parkingLocate' => 1,//停车位置（1:路外;2:路内）
            'parkingType' => 1,//停车场类型（1:配建停车场;2:公共停车场;3:道路停车位）
            'attachedType' => '',//配建类型 可空（01:住宅类;02:体育设施类;03:旅馆类;04:办公楼;05:餐饮 娱乐类;06:医院;07:教育类;08:工业类;09:游览场所;10: 影剧院类;11:文化类;12:市场类;13:商场类;14:金融类;15: 商业类;16:交通枢纽）
            'buildType' => 1,//建筑类型 可空（1:地面停车场;2:地下停车库;3:地上停车楼）
            'totalBerthNum' => 0,//泊位总数
            'openBerthNum' => 0,//开放泊位数
            'bmapX' => '',//百度经度坐标
            'bmapY' => '',
            'gmapX' => '',//高德经度坐标
            'gmapY' => '',
            'tempTotalNum' => 0,//临停车位数
            'intrinsicTotal' => 0,//月租车位数
            'visitorTotalNum' => 0,//访客车位数
            'visitorTotalNu' => 0,//充电桩车位 数
            'uploadTime' => '',//上传时间
        ];
        return $this->http->post('/uploadParkingInfo', $params);
    }

    /**
     * 车辆入场上报信息
     * @param array $params
     * @return array
     * @throws \CityBrain\Core\CityBrainException
     */
    public function uploadCarInData(array $params): array
    {
        $ret = [
            'uid' => '',//一次车辆到达 的流水号(场库 编号+入口编号 +时间戳 ( yyyyMMddHHm msssss))
            'plateNo' => '',//车牌号
            'parkingCode' => '',//停车场编号
            'gateNo' => '',//岗亭编号 可空
            'entryNum' => '',//入口编号
            'operatorCode' => '',//收费员编号 可空
            'totalBerthNum' => 0,//泊位总数
            'openBerthNum' => 0,//开放泊位数
            'freeBerthNum' => 0,//剩余泊位数
            'arriveTime' => '',//到达时间
            'parkingType' => '',//停车类型
            'uploadTime' => '',//上传时间
        ];
        return $this->http->post('/uploadCarInData', $params);
    }

    /**
     * 车辆出场上报信息
     * @param array $params
     * @return array
     * @throws \CityBrain\Core\CityBrainException
     */
    public function uploadCarOutData(array $params): array
    {
        $ret = [
            'uid' => '',//一次车辆到达 的流水号(场库 编号+入口编号 +时间戳 ( yyyyMMddHHm msssss))
            'plateNo' => '',//车牌号
            'parkingCode' => '',//停车场编号
            'gateNo' => '',//岗亭编号 可空
            'operatorCode' => '',//收费员编号 可空
            'totalBerthNum' => 0,//泊位总数
            'openBerthNum' => 0,//开放泊位数
            'freeBerthNum' => 0,//剩余泊位数
            'arriveTime' => '',//到达时间
            'parkingType' => '',//停车类型
            'endTime' => '',//离开时间
            'shouldpay' => 0,//应缴金额 分 可空
            'dealTime' => '',//交易时间 可空
            'dealFee' => '',//交易金额(单位: 分) 可空
            'paidType' => '',//付费方式 可空
            'payDetails' => '',//付费详情 可空
            'entryNum' => '',//入口编号
            'outNum' => '',//出口编号
            'uploadTime' => '',//上传时间
        ];
        return $this->http->post('/uploadCarOutData', $params);
    }

    /**
     * 上传停车场空闲状态
     * 停车场库本地系统的网络中断后重连时调用
     * @param array $params
     * @return array
     * @throws \CityBrain\Core\CityBrainException
     */
    public function uploadParkingState(array $params): array
    {
        $ret = [
            'parkingCode' => '',//停车场编号
            'parkingCode' => 0,//泊位总数
            'openBerthNum' => 0,//开放泊位数
            'freeBerthNum' => 0,//剩余泊位数
            'uploadTime' => '',//上传时间
        ];
        return $this->http->post('/uploadParkingState', $params);
    }

    /**
     * 上传照片接口
     * @param array $params
     * @return array
     * @throws \CityBrain\Core\CityBrainException
     */
    public function uploadPhoto(array $params): array
    {
        $ret = [
            'uid' => '',//流水号
            'time' => '',//照片时间
            'type' => '',//照片类型
            'name' => '',//照片名称
            'file' => '',//照片文件 base64之后的字符串，不超过512K
            'uploadTime' => '',//上传时间
        ];
        return $this->http->post('/uploadPhoto', $params);
    }

    /**
     * 电子钥匙缴费上报
     * @param array $params
     * @return array
     * @throws \CityBrain\Core\CityBrainException
     */
    public function uploadMonthlyFee(array $params): array
    {
        $ret = [
            'plateNo' => '',//车牌号
            'parkingCode' => '',//停车场编号
            'payDate' => '',//缴费日期
            'validStartDate' => '',//有效日期起
            'validEndDate' => '',//有效日期止
            'chargeType' => 1,//包月收费类型（1:车位租赁费;2:车位管理费;3:车位租赁退费;4:车位管理费 退费）
            'shouldpay' => 0,//应收金额 (分)
            'paidFee' => 0,//实收金额 (分)
            'invoiceFlag' => 0,//是否已开发票
            'invoiceNo' => '',//发票号码 可空
            'invoiceType' => 1,//发票类型 可空（1:专票，2:普票）
            'remark' => '',//备注 可空
            'uploadTime' => '',//上传时间
        ];
        return $this->http->post('/uploadMonthlyFee', $params);
    }

    /**
     * @param string $code
     * @return array
     * @throws \CityBrain\Core\CityBrainException
     */
    public function uploadHeartbeat(string $code)
    {
        return $this->http->post('/uploadHeartbeat', ['parkingCode' => $code, 'uploadTime' => date("Y-m-d H:i:s")]);
    }
}