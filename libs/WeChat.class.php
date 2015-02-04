<?php

// $weixin = new class_weixin("", "");
// var_dump($weixin->access_token);
// var_dump($weixin->lasttime);
// // var_dump($weixin->get_user_list());
// $openid = "oLVPpjkttuZTbwDwN7vjHNlqsmPs";
// var_dump($weixin->get_user_info($openid));
// $data ='{"button":[{"name":"关于我们","sub_button":[{"type":"click","name":"公司简介","key":"公司简介"},{"type":"click","name":"社会责任","key":"社会责任"},{"type":"click","name":"联系我们","key":"联系我们"}]},{"name":"产品服务","sub_button":[{"type":"click","name":"微信平台","key":"微信平台"},{"type":"click","name":"微博应用","key":"微博应用"},{"type":"click","name":"手机网站","key":"手机网站"}]},{"name":"技术支持","sub_button":[{"type":"click","name":"文档下载","key":"文档下载"},{"type":"click","name":"技术社区","key":"技术社区"},{"type":"click","name":"服务热线","key":"服务热线"}]}]}';
// var_dump($weixin->create_menu($data));
// var_dump($weixin->create_qrcode("QR_SCENE", "134324234"));
// var_dump($weixin->create_group("老师"));
// var_dump($weixin->update_group($openid, "100"));
// var_dump($weixin->location_geocoder(22.539968,113.954980));
// var_dump($weixin->upload_media("image","pondbay.jpg"));
// var_dump($weixin->send_custom_message($openid, "text", "asdf"));


/*
    WeChat
    CopyRight 2014 All Rights Reserved
*/

class WeChat {
  
	var $appid = "";
	var $appsecret = "";

    //构造函数，获取Access Token
	public function __construct($appid = NULL, $appsecret = NULL){
    if($appid){
      $this->appid = $appid;
    }
    if($appsecret){
      $this->appsecret = $appsecret;
    }

    //hardcode
    $this->lasttime = 1395049256;
    $this->access_token = "nRZvVpDU7LxcSi7GnG2LrUcmKbAECzRf0NyDBwKlng4nMPf88d34pkzdNcvhqm4clidLGAS18cN1RTSK60p49zIZY4aO13sF-eqsCs0xjlbad-lKVskk8T7gALQ5dIrgXbQQ_TAesSasjJ210vIqTQ";

    if (time() > ($this->lasttime + 7200)){
      $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->appid."&secret=".$this->appsecret;
      $res = $this->https_request($url);
      $result = json_decode($res, true);
      //save to Database or Memcache
      $this->access_token = $result["access_token"];
      $this->lasttime = time();
    }
	}

  //获取关注者列表
	public function get_user_list($next_openid = NULL) {
		$url = "https://api.weixin.qq.com/cgi-bin/user/get?access_token=".$this->access_token."&next_openid=".$next_openid;
    $res = $this->https_request($url);
    return json_decode($res, true);
	}

    //获取用户基本信息
	public function get_user_info($openid) {
		$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$this->access_token."&openid=".$openid."&lang=zh_CN";
		$res = $this->https_request($url);
    return json_decode($res, true);
	}

  //创建菜单
  public function create_menu($data)    {
    $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$this->access_token;
    $res = $this->https_request($url, $data);
    return json_decode($res, true);
  }

  //发送客服消息，已实现发送文本，其他类型可扩展
	public function send_custom_message($touser, $type, $data) {
    $msg = array('touser' =>$touser);
    switch($type) {
		  case 'text':
			  $msg['msgtype'] = 'text';
			  $msg['text']    = array('content'=> urlencode($data));
			  break;
    }
		$url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".$this->access_token;
		return $this->https_request($url, urldecode(json_encode($msg)));
	}

  //生成参数二维码
  public function create_qrcode($scene_type, $scene_id) {
    switch($scene_type) {
		  case 'QR_LIMIT_SCENE': //永久
        $data = '{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": '.$scene_id.'}}}';
			  break;
  	  case 'QR_SCENE':       //临时
        $data = '{"expire_seconds": 1800, "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": '.$scene_id.'}}}';
			  break;
    }
    $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$this->access_token;
    $res = $this->https_request($url, $data);
    $result = json_decode($res, true);
    return "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".urlencode($result["ticket"]);
  }
    
  //创建分组
  public function create_group($name) {
    $data = '{"group": {"name": "'.$name.'"}}';
    $url = "https://api.weixin.qq.com/cgi-bin/groups/create?access_token=".$this->access_token;
    $res = $this->https_request($url, $data);
    return json_decode($res, true);
  }
    
  //移动用户分组
  public function update_group($openid, $to_groupid) {
    $data = '{"openid":"'.$openid.'","to_groupid":'.$to_groupid.'}';
    $url = "https://api.weixin.qq.com/cgi-bin/groups/members/update?access_token=".$this->access_token;
    $res = $this->https_request($url, $data);
    return json_decode($res, true);
  }
    
  //上传多媒体文件
  public function upload_media($type, $file) {
    $data = array("media"  => "@".dirname(__FILE__).'\\'.$file);
    $url = "http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token=".$this->access_token."&type=".$type;
    $res = $this->https_request($url, $data);
    return json_decode($res, true);
  }
   
  //地理位置逆解析
  public function location_geocoder($latitude, $longitude) {
    $url = "http://api.map.baidu.com/geocoder/v2/?ak=B944e1fce373e33ea4627f95f54f2ef9&location=".$latitude.",".$longitude."&coordtype=gcj02ll&output=json";
    $res = $this->https_request($url);
    $result = json_decode($res, true);
    return $result["result"]["addressComponent"];
  }

  //https请求（支持GET和POST）
  protected function https_request($url, $data = null) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    if (!empty($data)){
      curl_setopt($curl, CURLOPT_POST, 1);
      curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
  }
}

