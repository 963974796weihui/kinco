<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yansongda\Pay\Pay;
use Yansongda\Pay\Log;
use App\Http\Controllers;

class AlipayController extends Controller
{
    protected $config = [
        'app_id' => '2016092700606550',//你创建应用的APPID
        'notify_url' => 'http://39.104.56.173:8091/notify',//异步回调地址
        'return_url' => 'http://39.104.56.173:8091/return',//同步回调地址
        'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA4+zGPoLD6BV+3WXgcQbcaX/5lvP3NOp99lRleAeaigKWGohc6QMfJOlLcLa4p66vcJwjoGDZKAz3yYSMDuh8pabPBfcMtQMowcD8KLLOE/tKvJtBxbzpCUAkRvejrjad+IufBkDwCXzXgENCLIsMMWusE0b8p8/87jkbPrupv+48zvL6btxhbTQk7ovcjN1FfLSdPKV3bCkmwPbSZCRGLbnVAJb0taMP730nU2lwNbYKlHoZsuSVbuDQuSsUZcnC/ECwYu4NTkP87w92Si88Q9K6NwUaNCoYPg3I8EErIZs8UcST0NPWmmTOWP9BTMABY+U2vCAQZw2UL0XDFGJ3fQIDAQAB',//是支付宝公钥，不是应用公钥,  公钥要写成一行,不要换行
        // 加密方式： **RSA2**
        'private_key' => 'MIIEowIBAAKCAQEAoOhWOtW0pCpfki/tj/JhiiQ4Aian5neF86osCyv3s+Kx5gFWUAzoHlSo0oD8RD2TIWO0WCqwDHQ/namwqBZogL0Xda5Uoo9D/4ZeC7wy7ox48B3FAKba9caYX9pJdpLW87YZe9Vo14rFVg5aCY2VPXhRCw+HsJ3Jhgn8GhSeD44sVb+dCg9FoE8WvFV12iIBdRA2sl8Sg8vnNr/GXMMtJQyfg8nPN0MsqMZBRAnwEOsSwhxqdHFJTtKTk5qP6iGrK7rPblz7IroeCt1tqKfykQ6yoQsHCFYTmTlAdKeXPQtestVREuGcx93kNpTwrEw7vYPoj7l6TNSkLPGyZiDqdQIDAQABAoIBAEErOFll2l6u5rw/exjU0xpu5txAKbpW6EKyxlnT+eO6SdDc88dCIvS91w5CvWkqAx//0LfwuhKhNm61nl3FjZeufgTkkVSxb5nbItXf6ETjUcv2Uqab7YyjXW1c7GoAuNxGAXY9DdRWWOwsrdy2oh4eT7yKEqu+jDMkch1iAk5L41Lv7tzRVZx3qVoKK7aaIucfi5byOl8hohNL9mR4uGYo1TVk7j2LmKoIgUGg6rtWgsjI/AJbLE/Y9ngIx6Yn2/dPWdswmHLCwnKtJw5w05bNu+65o0vIdPHgal+hRl/7eOgEl2t6l/zFcgAl8cHvTrH7+/+KC7SK0PLgZH4sItUCgYEAzFSqkj/B33odjYLSQzU8wYne/F1414a2oPAVUhHIcpR+kxxHgpgtaLg9XrN1R+FZVIl5k2Emyjc6ViqPxuhdOnMjhEHImIrbnezxATy7Q5qti7+LuPFxZwUnYOv+jMVswOP6AaNf8Jy5Vbpz73h7wkjfh+8bVLKfN0wgilukfesCgYEAyZis8FtARLZ8Uq/FZtCMitEyB/jGUB3igK9oPwfP/A2z3xxJ1TwBnZORStyPaCH6QIwuUvzOsLbOpIqadzrZfltpBELJfhPIWDFyxHtbc+48ebv5SpWWumLtLC4XDuk3d5dE8dybiQKNuZJhPU870uXkwZSv0sSNvMI/5MN3QR8CgYAJSoBHDicepkrZuG0fKTiOBFrjVsy74CBFySTvCmf27dGjltGZmpnV/SqtN7PJhPGu6mFg1jYRU9mPOXg4LzCuC7Y9uVAJv6ak07VRvRU2yb3CcP9FdZUWfiGAQrrcY83CJ5uin28pXbb/su66oNvfxbiEUdcITub2eLuyDuC36QKBgD4kjBrDZjpILmamvCkHXGtmNQ/0Bd2oeKvGZlIXrGs8Jhg+dhv/FVhnpr/24VXuM2A2VglK2MPsdiKOfzMFtwmWT/b6R3ZPztAw3fnnNLe11nqIET6GgiIOYKHfy0fTaD+7J4uOv3sqiFmN39+wTvah2zQ5C0HjSPXIwz1Xa6ltAoGBAIvWQSO3s3GYtRZb7iv4V+3EN8JDC53KRZFoLLXv+Ut/fTRY734q15QM5IewVt/GCsdcD5mvJop9lSGTJ+yuTdIxbZkRUbbtwdgJazcXEtY0ut7j0v9yxuawHkSNQVh9nNaWczFiiCuReeWR57g3+5Xhdp4MA+9k9RKOqNy7tlPR',//密钥,密钥要写成一行,不要换行
        'log' => [ // optional
            'file' => './logs/alipay.log',
            'level' => 'info', // 建议生产环境等级调整为 info，开发环境为 debug
            'type' => 'single', // optional, 可选 daily.
            'max_file' => 30, // optional, 当 type 为 daily 时有效，默认 30 天
        ],
        'http' => [ // optional
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // 更多配置项请参考 [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
        ],
        'mode' => 'dev', // optional,设置此参数，将进入沙箱模式
    ];
    public function Alipay()
    {
        $order = [
            'out_trade_no' => time(),
            'total_amount' => '0.01',
            'subject' => '上海步科研发中心',
        ];

        $alipay = Pay::alipay($this->config)->web($order);

        return $alipay;// laravel 框架中请直接 `return $alipay`
    }

    public function AliPayReturn()
    {
        $data = Pay::alipay($this->config)->verify(); // 是的，验签就这么简单！
        return view('emails.alipay', ['data' => $data]);
        // 订单号：$data->out_trade_no
        // 支付宝交易号：$data->trade_no
        // 订单总金额：$data->total_amount
    }

    public function AliPayNotify()
    {
        $alipay = Pay::alipay($this->config);

        try{
            $data = $alipay->verify(); // 是的，验签就这么简单！

            // 请自行对 trade_status 进行判断及其它逻辑进行判断，在支付宝的业务通知中，只有交易通知状态为 TRADE_SUCCESS 或 TRADE_FINISHED 时，支付宝才会认定为买家付款成功。
            // 1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号；
            // 2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额）；
            // 3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）；
            // 4、验证app_id是否为该商户本身。
            // 5、其它业务逻辑情况
		

 	    $date['gmt_payment'] = $data->gmt_payment;
	    $date['subject'] = $data->subject;
	    $date['out_trade_no'] = $data->out_trade_no;
	    $date['total_amount'] = $data->total_amount;
	    $date['trade_status'] = $data->trade_status;
	    $date['trade_no'] = $data->trade_no;
	    $date['auth_app_id'] = $data->auth_app_id;
	    $date['receipt_amount'] = $data->receipt_amount;
	    $date['app_id'] = $data->app_id;
	    $date['buyer_pay_amount'] = $data->buyer_pay_amount;
	    $date['seller_id'] = $data->seller_id;
            $date['sncode'] =str_random(16);
            $date['administrtor_id'] =$this->id;
            $date['long'] ='90';
            $date['buy_time'] =time();
            $date['pay_type'] =1;
        $res = DB::table('ki_admin_code')->insertGetId($date);
        Log::debug('Alipay notify', $data->all());
        } catch (\Exception $e) {
            //$e->getMessage();
            //ceshi
        }

        return $alipay->success();// laravel 框架中请直接 `return $alipay->success()`
    }
}