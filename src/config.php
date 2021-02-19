<?php
/**
* 具体参数含义以及获取方式请参考 
* https://work.weixin.qq.com/api/doc/90000/90135/90664
*/

//企业ID
$CORPID = "";
//应用Secret
$CORPSECRET = "";
//应用ID
$AGENTID = "";

/**
* 以下参数由你自己根据实际状况配置
*/
//回调URL
$COLL_BACK_URL = "https://127.0.0.1/push/?type=body&hash=";
//接口请求SEND_KEY,可以留空
$SEND_KEY = "";
//redis配置
$REDIS_IP = "127.0.0.1";
$REDIS_PORT = 6379;
$REDIS_PASSWD = "";
$REDIS_DB = 1;

//消息体长度
$DESP_SIZE = 80;
//消息缓存时间
$SMS_CACHE_TIME = 7200;


/*
	以下为常量区,没必要不用改
*/

$REDIS_CACHE_HEAD = "weixinPush:";
$REDIS_CACHE_TOKEN = $REDIS_CACHE_HEAD."token";
$REDIS_CACHE_SMS = $REDIS_CACHE_HEAD."sms:";

$GET_TOKEN_URL = "https://qyapi.weixin.qq.com/cgi-bin/gettoken";
$PUSH_MSG_URL = "https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token=";

$TOUSER = "@all";
$TOPARTY = "@all";
$TOTAG = "@all";

$ERR_SEND_KEY = "SendKey错误";
$ERR_REDIS_CONN = "Redis连接失败";
$ERR_TOKEN = "Token获取失败";
$ERR_MESSAGE = "发送失败,返回为空";


