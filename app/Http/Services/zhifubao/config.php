<?php
$config = array (
    //应用ID,您的APPID。
    'app_id' => "2016092700606550",

    //商户私钥
    'merchant_private_key' => "MIIEowIBAAKCAQEAoOhWOtW0pCpfki/tj/JhiiQ4Aian5neF86osCyv3s+Kx5gFWUAzoHlSo0oD8RD2TIWO0WCqwDHQ/namwqBZogL0Xda5Uoo9D/4ZeC7wy7ox48B3FAKba9caYX9pJdpLW87YZe9Vo14rFVg5aCY2VPXhRCw+HsJ3Jhgn8GhSeD44sVb+dCg9FoE8WvFV12iIBdRA2sl8Sg8vnNr/GXMMtJQyfg8nPN0MsqMZBRAnwEOsSwhxqdHFJTtKTk5qP6iGrK7rPblz7IroeCt1tqKfykQ6yoQsHCFYTmTlAdKeXPQtestVREuGcx93kNpTwrEw7vYPoj7l6TNSkLPGyZiDqdQIDAQABAoIBAEErOFll2l6u5rw/exjU0xpu5txAKbpW6EKyxlnT+eO6SdDc88dCIvS91w5CvWkqAx//0LfwuhKhNm61nl3FjZeufgTkkVSxb5nbItXf6ETjUcv2Uqab7YyjXW1c7GoAuNxGAXY9DdRWWOwsrdy2oh4eT7yKEqu+jDMkch1iAk5L41Lv7tzRVZx3qVoKK7aaIucfi5byOl8hohNL9mR4uGYo1TVk7j2LmKoIgUGg6rtWgsjI/AJbLE/Y9ngIx6Yn2/dPWdswmHLCwnKtJw5w05bNu+65o0vIdPHgal+hRl/7eOgEl2t6l/zFcgAl8cHvTrH7+/+KC7SK0PLgZH4sItUCgYEAzFSqkj/B33odjYLSQzU8wYne/F1414a2oPAVUhHIcpR+kxxHgpgtaLg9XrN1R+FZVIl5k2Emyjc6ViqPxuhdOnMjhEHImIrbnezxATy7Q5qti7+LuPFxZwUnYOv+jMVswOP6AaNf8Jy5Vbpz73h7wkjfh+8bVLKfN0wgilukfesCgYEAyZis8FtARLZ8Uq/FZtCMitEyB/jGUB3igK9oPwfP/A2z3xxJ1TwBnZORStyPaCH6QIwuUvzOsLbOpIqadzrZfltpBELJfhPIWDFyxHtbc+48ebv5SpWWumLtLC4XDuk3d5dE8dybiQKNuZJhPU870uXkwZSv0sSNvMI/5MN3QR8CgYAJSoBHDicepkrZuG0fKTiOBFrjVsy74CBFySTvCmf27dGjltGZmpnV/SqtN7PJhPGu6mFg1jYRU9mPOXg4LzCuC7Y9uVAJv6ak07VRvRU2yb3CcP9FdZUWfiGAQrrcY83CJ5uin28pXbb/su66oNvfxbiEUdcITub2eLuyDuC36QKBgD4kjBrDZjpILmamvCkHXGtmNQ/0Bd2oeKvGZlIXrGs8Jhg+dhv/FVhnpr/24VXuM2A2VglK2MPsdiKOfzMFtwmWT/b6R3ZPztAw3fnnNLe11nqIET6GgiIOYKHfy0fTaD+7J4uOv3sqiFmN39+wTvah2zQ5C0HjSPXIwz1Xa6ltAoGBAIvWQSO3s3GYtRZb7iv4V+3EN8JDC53KRZFoLLXv+Ut/fTRY734q15QM5IewVt/GCsdcD5mvJop9lSGTJ+yuTdIxbZkRUbbtwdgJazcXEtY0ut7j0v9yxuawHkSNQVh9nNaWczFiiCuReeWR57g3+5Xhdp4MA+9k9RKOqNy7tlPR",

    'notify_url' => "http://39.104.56.173:9001/notify_url.php",
//
//    //同步跳转
    'return_url' => "http://39.104.56.173:9001/return_url.php",
    //异步通知地址
//    'notify_url' => "www.baidu.com",
//
//    //同步跳转
//    'return_url' => "www.baidu.com",

    //编码格式
    'charset' => "UTF-8",

    //签名方式
    'sign_type'=>"RSA2",
 //   https://openapi.alipay.com/gateway.do
    //支付宝网关
    'gatewayUrl' => "https://openapi.alipaydev.com/gateway.do",

    //支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
    'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA4+zGPoLD6BV+3WXgcQbcaX/5lvP3NOp99lRleAeaigKWGohc6QMfJOlLcLa4p66vcJwjoGDZKAz3yYSMDuh8pabPBfcMtQMowcD8KLLOE/tKvJtBxbzpCUAkRvejrjad+IufBkDwCXzXgENCLIsMMWusE0b8p8/87jkbPrupv+48zvL6btxhbTQk7ovcjN1FfLSdPKV3bCkmwPbSZCRGLbnVAJb0taMP730nU2lwNbYKlHoZsuSVbuDQuSsUZcnC/ECwYu4NTkP87w92Si88Q9K6NwUaNCoYPg3I8EErIZs8UcST0NPWmmTOWP9BTMABY+U2vCAQZw2UL0XDFGJ3fQIDAQAB",
);