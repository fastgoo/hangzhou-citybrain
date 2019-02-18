<?php
/**
 * Created by PhpStorm.
 * User: Mr.Zhou
 * Date: 2019/1/15
 * Time: 4:59 PM
 */

namespace CarPay\Core;

class ApiUrl
{
    /**
     * 查询用户状态
     */
    const QUERY_USER_STATUS = "https://api.mch.weixin.qq.com/vehicle/partnerpay/querystate";

    /**
     * 查询订单状态
     */
    const QUERY_ORDER_STATUS = "https://api.mch.weixin.qq.com/transit/partnerpay/queryorder";

    /**
     * 用户进场通知
     */
    const USER_INCOME_NOTIFY = "https://api.mch.weixin.qq.com/vehicle/partnerpay/notification";

    /**
     * 申请支付
     */
    const PAY_APPLY = "https://api.mch.weixin.qq.com/vehicle/partnerpay/payapply";

    /**
     * 下载对账订单
     */
    const DOWNLOAD_BILL = "https://api.mch.weixin.qq.com/pay/downloadbill";

    /**
     * 退款申请
     */
    const REFUND = "https://api.mch.weixin.qq.com/secapi/pay/refund";

    /**
     * 退款状态查询
     */
    const REFUND_QUERY = "https://api.mch.weixin.qq.com/secapi/pay/refundquery";

}