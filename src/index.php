<?php
include "./config.php";
include "./function.php";

$title = isset($_REQUEST['title']) ? $_REQUEST['title'] : '无标题';
$desp = isset($_REQUEST['desp']) ? $_REQUEST['desp'] : '无正文';
$sendKey = isset($_REQUEST['sendKey']) ? $_REQUEST['sendKey'] : '';
$smsHash = isset($_REQUEST['hash']) ? $_REQUEST['hash'] : '';
$type = isset($_REQUEST['type']) ? false : true;
$encode = $_SERVER['REQUEST_METHOD'] == 'GET' ? false : true;

	$redis = new Redis();
	$redis->connect($REDIS_IP, $REDIS_PORT);
	if($REDIS_PASSWD != "") {
		$redis->auth($REDIS_PASSWD);
	}      
	$redis->select($REDIS_DB);
	if(!strcmp($redis->ping(),"1")==0){
		errMessage($ERR_REDIS_CONN);
	}

	if($type) {
	    
	    if($SEND_KEY != "") {
    		if(!strcmp($SEND_KEY,$sendKey)==0){
    			errMessage($ERR_SEND_KEY);
    		}
    	}
    	
		if($redis->exists($REDIS_CACHE_TOKEN)) {
			$accessToken = $redis->get($REDIS_CACHE_TOKEN);
		} else {
			$url = $GET_TOKEN_URL."?corpid=".$CORPID."&corpsecret=".$CORPSECRET;
			$res = httpRequest($url,null,true);
			if($res == null) {
				errMessage($ERR_TOKEN);
			}
			$tokenJson = json_decode($res,true);
			if($tokenJson["errcode"] != 0 || $tokenJson["errcode"] != "ok"){
				errMessage($ERR_TOKEN);
			}
			$accessToken = $tokenJson["access_token"];
			$redis->setex($REDIS_CACHE_TOKEN,$tokenJson["expires_in"],$tokenJson["access_token"]);
		}
		
		$pushUrl = $PUSH_MSG_URL.$accessToken;
		
		$data = array(
    		    "touser"=>$TOUSER,
    		    "toparty"=>$TOPARTY,
    		    "totag"=>$TOTAG,
    		    "msgtype"=>"textcard",
    		    "agentid"=>$AGENTID,
    		    "textcard"=>"",
    		    "enable_id_trans"=>0,
    		    "enable_duplicate_check"=>0,
		        "duplicate_check_interval"=>1800
		    );
		    
		$title = strip_tags($title);
		$desp = strip_tags($desp,"<p><b><br><span><div>");
		if($encode){
		    $title = urldecode($title);
		    $desp = urldecode($desp);
		}
		
		$sunDesp = strip_tags($desp);
		if(mb_strlen($sunDesp,'UTF8') >= $DESP_SIZE) {
		    $sunDesp = substr($sunDesp,$DESP_SIZE);
		}
		
		$hash = md5($title.$desp);
	 	$collBackUrl = $COLL_BACK_URL.$hash;
		
	    $textcard = array(
	            "title"=>$title,
	            "description"=>$sunDesp,
	            "url"=>$collBackUrl,
	            "btntxt"=>"更多"
	        );	    
        
        $data["textcard"] = $textcard;
		$description = json_encode($data);
		$res = httpRequest($pushUrl,$description,true);
		if($res == null){
				errMessage($ERR_MESSAGE);
			}
		$tokenJson = json_decode($res,true);
		if($tokenJson["errcode"] != 0 || $tokenJson["errcode"] != "ok"){
			errMessage($tokenJson["errcode"]);
		}
		
		$sms = array(
		    'title' => $title,
		    'desp' => $desp
		);
		
		$redis->setex($REDIS_CACHE_SMS.$hash,$SMS_CACHE_TIME,json_encode($sms));
		
		suressMessage($tokenJson["errcode"]);
	} else {
	    if($redis->exists($REDIS_CACHE_SMS.$smsHash)) {
			$sms = json_decode($redis->get($REDIS_CACHE_SMS.$smsHash),true);
		}else{
		    $sms = array(
		    'title' => "消息不存在",
		    'desp' => "可能原因:<br /> 1.消息hash不正确<br />2.消息已过期"
		);
		}
		
		$mod = file_get_contents("mod.html");
		$mod = str_replace("#[title]",$sms["title"],$mod);
		$mod = str_replace("#[desp]",$sms["desp"],$mod);
		echo $mod;
	}
	
?>