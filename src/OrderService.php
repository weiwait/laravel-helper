<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/7/4
 * Time: 16:39
 */

namespace Weiwait\Helper;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException;

class OrderService
{
    //订单类型
    public const ORDER_TYPE_AMOUNT = 1; //外卖下单
    public const ORDER_TYPE_TO_UP = 2; //余额充值
    public const ORDER_TYPE_REFUND = 3; //退款
    public const ORDER_TYPE_PARCEL = 4; //快递代取
    public const ORDER_TYPE_HOMEMAKING = 5; //家政
    public const ORDER_TYPE_SECKILL = 6; //家政
    public const ORDER_TYPE_MEMBER = 7; //开通会员
    //支付方式
    public const PAYMENT_WECHAT_PAY = 1; //微信支付
    public const PAYMENT_ALIPAY = 2; //支付宝支付
    public const PAYMENT_BANK_CARD = 3; //银行卡支付
    public const PAYMENT_CASH = 4; //现金支付
    public const PAYMENT_CREDIT = 5; //余额支付
    public const PAYMENT_FREE = 6; //余额支付

    public const PAYMENTS = [
        self::PAYMENT_WECHAT_PAY => '微信支付',
        self::PAYMENT_ALIPAY => '支付宝支付',
        self::PAYMENT_BANK_CARD => '银行卡支付',
        self::PAYMENT_CASH => '现金支付',
        self::PAYMENT_CREDIT => '余额支付',
        self::PAYMENT_FREE => '免费',
    ];
    //下单平台
    public const PLATFORM = [
        'wechatMiniProgram' => 1,
        'webMobile' => 2,
        'webPc' => 3,
    ];
    public const WECHAT_MINI_PROGRAM = 1; //微信小程序
    public const WEB_MOBILE = 2; //移动端网站
    public const WEB_PC = 3; //pc端网站

    public static function generate($order, $orderType, $paymentProcedure = self::PAYMENT_WECHAT_PAY, $platform = self::WECHAT_MINI_PROGRAM)
    {
        //时间8 + 平台1 + 订单类型2(1普通, 2退款) + 支付方式2(1微信) + (随机码 + 月流水)5 + 随机码2 = 20位

        /** @var Model $order */
        $serialMon = $order::withTrashed()->where('created_at', '>=', Carbon::now()->startOfMonth())->where('created_at', '<=', Carbon::now()->endOfMonth())->count();
        try {
            $random = random_int(1234567, 9876543);
        } catch (\Exception $e) {
            Log::error($e);
            throw new PreconditionFailedHttpException('Retry please');
        }

        $serialMon = substr($random, 0, 4 - strlen($serialMon + 1)) . '0' . ($serialMon + 1);
        $serialMon .= substr($random, 4, 2);

        $orderType = str_pad($orderType, 2, '0', STR_PAD_LEFT);
        $paymentProcedure = str_pad($paymentProcedure, 2, '0', STR_PAD_LEFT);
        return (string) date('Ymd') . "{$platform}{$orderType}{$paymentProcedure}{$serialMon}";
    }

    public static function changeOrderType($orderNumber, $type)
    {
        $orderType = str_pad($type, 2, '0', STR_PAD_LEFT);
        return (string) substr_replace($orderNumber, $orderType, 9, 2);
    }
}
