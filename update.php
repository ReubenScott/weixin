<?php




/**
 * 安装自定义菜单
 *
 */
function init_menu(){
  $data ='{"button":[{"name":"关于我们","sub_button":[{"type":"click","name":"公司简介","key":"公司简介"},{"type":"click","name":"社会责任","key":"社会责任"},{"type":"click","name":"联系我们","key":"联系我们"}]},{"name":"产品服务","sub_button":[{"type":"click","name":"微信平台","key":"微信平台"},{"type":"click","name":"微博应用","key":"微博应用"},{"type":"click","name":"手机网站","key":"手机网站"}]},{"name":"技术支持","sub_button":[{"type":"click","name":"文档下载","key":"文档下载"},{"type":"click","name":"技术社区","key":"技术社区"},{"type":"click","name":"服务热线","key":"服务热线"}]}]}';

  // 引入 $access_token 
  include_once('token.php');

  if($access_token!='') {
    $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token;

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

    $msg= json_decode($output,true);
    if($msg['errmsg']=='ok') {
      die('创建自定义菜单成功!');
    }  else {
      //TODO 记录失败日志
//      var_dump($output);
      die('创建自定义菜单失败!');
    }
  } else {
    die('创建失败,微信AppId或微信AppSecret填写错误');
  }

}

init_menu();


