<?php
	/**
    * @param str          $url     发送接口地址
    * @param array/json   $data    要发送的数据  
    * @param false/true   $json    false $data数组格式  true $data json格式
    * @return  返回json数据
    */   
	 function httpRequest($url, $data = null,$json = FALSE){
        //创建了一个curl会话资源，成功返回一个句柄；
        $curl = curl_init();
        //设置url
        curl_setopt($curl, CURLOPT_URL, $url);
        //设置为FALSE 禁止 cURL 验证对等证书（peer’s certificate）
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        //设置为 1 是检查服务器SSL证书中是否存在一个公用名(common name)
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            //设置请求为POST
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30); //最长的可忍受的连接时间
            //设置POST的数据域
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            if($json){
                curl_setopt($curl, CURLOPT_HEADER, 0);
                curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json; charset=utf-8',
                        'Content-Length: ' . strlen($data)
                    )
                );

            }
        }
        //设置是否将响应结果存入变量，1是存入，0是直接输出
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        //然后将响应结果存入变量
        $output = curl_exec($curl);
        //关闭这个curl会话资源
        curl_close($curl);
        return $output;
    }
	
	function errMessage($message) {
		$res =array ("code"=>500,"message"=>$message,"data"=>"");
		echo json_encode($res);
		exit;
	}
	
	function suressMessage($message) {
		$res =array ("code"=>200,"message"=>$message,"data"=>"");
		echo json_encode($res);
		exit;
	}
?>