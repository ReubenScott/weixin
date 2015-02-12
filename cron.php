<?php

/**
 * wechat cron 获取Access Token 
 */

include "settings.php" ;
include_once('libs/WeChat.class.php');

$wechat = new WeChat($appid, $appsecret);
$access_token = $wechat->refresh_access_token() ;

$token_file = 'token.php';

if(file_exists($token_file)){
  $fopen = fopen($token_file,'r+') or exit($token_file." 没有读写权限！");
  //存储配置信息
  $content = file_get_contents($token_file);
  //查找替换
  $content = preg_replace('/\$access_token\s*=\s*[\'|"][a-zA-Z0-9\-\_]*[\'|"];/isU','$access_token = "'.$access_token.'";',$content);

  // 正则去除'//'和'/* */'注释
  $content = preg_replace("/(\/{2,}.*?$)|(\/\*(\n|.)*?\*\/)/isU" ,'',$content);

  $content .= "//" .$access_token ;

  if(fwrite($fopen,$content)){
    echo '更新token成功';
  }else{
    echo '更新token失败';
  }
  fclose($fopen);
} else {
  $fopen = fopen($token_file,'w+') or exit("创建 ".$token_file." 失败！");
  
  $content = " <?php \r\n \$access_token = '".$access_token . "';" ;
  
  if(fwrite($fopen,$content)) {
    echo '文件写入成功';
  }
  fclose($fopen);
}


