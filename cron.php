<?php

/**
 * wechat token cron
 */

include_once('libs/WeChat.class.php');
$wechat = new WeChat;

$token = 'token.php';

if(file_exists($token)){
  $fopen = fopen($token,'r+') or exit($token." 没有读写权限！");
  //存储配置信息
  $content = file_get_contents($token);
  //查找替换
  $content = preg_replace('/\$access_token\s*=\s*[\'|"][a-zA-Z0-9\-\_]*[\'|"];/isU','$access_token = "'.$wechat->access_token().'";',$content);

  if(fwrite($fopen,$content)){
    echo '更新token成功';
  }else{
    echo '更新token失败';
  }
  fclose($fopen);
} else {
  $fopen = fopen($token,'w+') or exit("Unable to connect 2");
  if( fwrite($fopen,$str)) {
    echo '文件写入成功';
  }
  fclose($fopen);
}



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