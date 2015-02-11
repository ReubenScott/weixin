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
  $content = preg_replace('/\$access_token\s*=\s*[\'|"][a-zA-Z0-9\-\_]*[\'|"];/isU','$access_token = "'.$wechat->refresh_access_token().'";',$content);

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


