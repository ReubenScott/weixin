<?php

$access_token = '';


//function curl_info($appid,$secret) {
//
//  $ch = curl_init(); 
//
//  curl_setopt($ch, CURLOPT_URL, "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$secret);
//
//  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
//
//  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
//
//  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
//
//  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
//
//  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
//
//  curl_setopt($ch, CURLOPT_AUTOREFERER, 1); 
//
//  // curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
//
//  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
//
//  $tmpInfo = curl_exec($ch); 
//
//  if (curl_errno($ch)) {  
//
//    echo 'Errno'.curl_error($ch);
//
//  }
//
//  curl_close($ch); 
//
//  $arr= json_decode($tmpInfo,true);
//
//  return $arr;
//
//}
//
//function curl_menu($ACCESS_TOKEN,$data) {
//
//  $ch = curl_init(); 
//
//  curl_setopt($ch, CURLOPT_URL, "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$ACCESS_TOKEN); 
//
//  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
//
//  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
//
//  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
//
//  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
//
//  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
//
//  curl_setopt($ch, CURLOPT_AUTOREFERER, 1); 
//
//  curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
//
//  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
//
//  $tmpInfo = curl_exec($ch); 
//
//  if (curl_errno($ch)) {
//
//    echo 'Errno'.curl_error($ch);
//
//  }
//
//  curl_close($ch); 
//
//  $arr= json_decode($tmpInfo,true);
//
//  return $arr;
//
//}
//
//function create_menu() {
//
//  $ACCESS_LIST= curl_info(APP_ID,APP_SCR);//获取到的凭证，你需要自己define APP_ID和APP_SCR（应用密钥），这个也是在微信公众平台后台开发者中心找
//
//  if($ACCESS_LIST['access_token']!='') {
//
//    $access_token = $ACCESS_LIST['access_token'];//获取到ACCESS_TOKEN
//
//    $data = '把上面代码1拷贝黏贴在这里';
//    
////      {
////         "button":[
////         { 
////              "type":"view",
////              "name":"登录微站",
////              "url":"https://open.weixin.qq.com/connect/oauth2/authorize?appid={在微信公众平台后台获取这个APPID}&redirect_uri={你填写的回调域名下的地址}&response_type=code&scope=snsapi_base&state=1#wechat_redirect"
////          }]
////    }
//
//    $msg = curl_menu($access_token,preg_replace("#u([0-9a-f]+)#ie", "iconv('UCS-2', 'UTF-8', pack('H4', '1'))", $data));
//
//    if($msg['errmsg']=='ok') {
//
//      die('创建自定义菜单成功!');
//
//    }
//
//    else {
//
//      die('创建自定义菜单失败!');
//
//    }
//
//  }
//
//  else {
//
//    die('创建失败,微信AppId或微信AppSecret填写错误');
//
//  }
//
//}
//
//create_menu();

?>