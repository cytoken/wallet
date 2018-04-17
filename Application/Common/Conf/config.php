<?php
return array(

    //系统配置
    'X_AUTH' => true, // auth验证
    'SHOW_PAGE_TRACE' =>false,
    //'LOG_RECORD' => true, // 开启日志记录
    //'LOG_LEVEL'  =>'DEBUG', // 只记录EMERG ALERT CRIT ERR 错误


    'DB_TYPE'=>'mysql',// 数据库类型
    'DB_HOST'=>'rm-j6c9g51vy15zwfn510o.mysql.rds.aliyuncs.com',// 服务器地址
    'DB_NAME'=>'cyt',// 数据库名
    'DB_USER'=>'cyt',// 用户名
    'DB_PWD'=>'cyt1qa1qa!QA',// 密码
    'DB_PARAMS' => array(\PDO::ATTR_CASE => \PDO::CASE_NATURAL),
    'DB_PORT'=>3306,// 端口
    'DB_CHARSET'=>'utf8',// 数据库字符集
//
    'DEFAULT_MODULE' => 'Home',

    // 阿里云OSS相关配置
	'OSS_ENDPOINT' => "oss-cn-qingdao.aliyuncs.com",
	'OSS_ACCESS_KEY_ID' => "I0kThHwa60ISiCRK",
	'OSS_ACCESS_KEY_SECRET' => 'VpRMwZQ2xFeWDaj2ZgiEAU5AELKoF9',
	'OSS_URL' => 'http://luobojianzhi-image.oss-cn-qingdao.aliyuncs.com/',
	'OSS_BUCKET' => 'luobojianzhi-image',
    'SMS_ACCESS_KEY_ID' => 'LTAIJJSfWOdgTBB5', //短信accessKeyId
    'SMS_ACCESS_KEY_SECRET' => '5QKSNhdY9l6YDDO1xSRDUgPhMi7mGr', //短信accessKeySecret

    //生成二维码的配置
    'QRCODE_VIEW_PATH' => "http://wallet.commonwealthyouthtoken.com/index.php/Home/Index/signUp?myCode=", //扫码时的访问地址
    'QRCODE_FONT_PATH' => "./Public/fonts/", //打水印时用到的字体目录
    'QRCODE_TEMP_PATH' => "./Uploads/Temp/",
    'QRCODE_TEMP_LOGO' => "./Public/Admin/logo.png",
);
