<?php
/**
 * Created by PhpStorm.
 * User: Mr.Zhou
 * Date: 2019/2/20
 * Time: 11:04 AM
 */

namespace CityBrain\Api;

use CityBrain\Core\Http;

class Record
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
     * 停车记录日对账
     * @param array $params
     * @return array
     * @throws \CityBrain\Core\CityBrainException
     */
    public function uploadCheckRecord(array $params): array
    {
        $ret = [
            'parkingCode' => '',//停车场编号
            'checkDate' => '',//对账日期(格式: yyyy-MM-dd)
            'arriveNum' => 0,//到达记录数
            'leaveNum' => 0,//离开记录数
            'payTotal' => 0,//总金额，单位 (分)
            'uploadTime' => '',//上传时间
        ];
        return $this->http->post('/uploadCheckRecord', $params);
    }

    /**
     * 停车记录流水号下载
     * @param array $params
     * @return array
     * @throws \CityBrain\Core\CityBrainException
     */
    public function downUID(array $params): array
    {
        $ret = [
            'parkingCode' => '',//停车场编号
            'accountDate' => '',//对账日期(格式: yyyy-MM-dd)
            'type' => 1,//进出标志(0: 进，1:出)
            'uploadTime' => '',//上传时间
        ];
        return $this->http->post('/downUID', $params);
    }

}