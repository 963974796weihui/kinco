邮件发送
.env文件
MAIL_DRIVER=smtp
MAIL_HOST=smtp.qq.com
MAIL_PORT=465
MAIL_USERNAME=963974796@qq.com
MAIL_PASSWORD=atieulhhlkrxbchf
MAIL_ENCRYPTION=ssl
config文件中的mail.php
 'from' => [
        'address' => env('MAIL_FROM_ADDRESS', '963974796@qq.com'),
        'name' => env('MAIL_FROM_NAME', 'Example'),
    ],
    
授权码到期时间：
定时任务去修改到期时间ki_admin_hmi表中的auth_code设置为0
没到期的授权码不允许删除