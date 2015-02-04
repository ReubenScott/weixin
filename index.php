<?php
/**
  * wechat php test
  */

//定义应用的根目录！（这个不是系统的根目录）本程序将应用目录限制在独立应用下
define("ROOT",str_replace("\\","/",dirname(__FILE__))."/");

//define your token
define("TOKEN", "weixin");
$wechatObj = new wechatCallbackapi();

if (!isset($_GET['echostr'])) {
  $wechatObj->responseMsg();
}else{
  $wechatObj->valid();
}

class wechatCallbackapi {
  
  public function valid() {
    $echoStr = $_GET["echostr"]; //随机字符串
    //valid signature , option
    if($this->checkSignature()){
      echo $echoStr;
      exit;
    }
  }
    
  private function checkSignature() {
    $signature = $_GET["signature"];    //微信加密签名
    $timestamp = $_GET["timestamp"];    //时间戳
    $nonce = $_GET["nonce"];            //随机数
    $token = TOKEN;
    $tmpArr = array($token, $timestamp, $nonce);
    sort($tmpArr);  //进行字典序排序
    //sha1加密后与签名对比
    if(sha1(implode($tmpArr)) == $signature ){
      return true;
    }else{
      return false;
    }
  }
  
  
  public function responseMsg()    {
    $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
    if (!empty($postStr)){
      $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
      $RX_TYPE = trim($postObj->MsgType);
      
//      FromUserName :  og1hvuL0Ku4DS-A3Fln8CEdE-MB4
//      ToUserName :  gh_0d1d823325c9

      $result = "";
      switch ($RX_TYPE){
        case "event":
          $result = $this->receiveEvent($postObj);
          break;
        case "text":
          $content = $postObj->Content ;   // 接受到的文字消息
          if(strcmp($content, "sh000001") == 0 ){
            $result = require_once ROOT.'/modules/stock/stock.module';
            $result = $this->transmitText($postObj,$result);
          } else if(strcmp($content, "1") == 0 ){
            $result = $this->transmitNews($postObj);
          } else if(strcmp($content, "？") == 0 ){
            $result = $this->transmitText($postObj,$this->getMainMenu());
          } else {
//            $result = $this->transmitText($postObj,$content);
            $result = $this->transmitText($postObj,$this->getMainMenu());
          }          
          break;
      }
      echo $result;
    }else {
      echo "";
      exit;
    }
  }
  

  private function receiveEvent222($object) {
    switch ($object->Event) {
        case "subscribe":
        $content = $this->getMainMenu();
        break;
    }
    $result = $this->transmitText($object, $content);
    return $result;
  }
  
  
  
  private function receiveEvent($object){
    $content = "";
    switch ($object->Event) {
      case "subscribe":
        $content[] = array(
          "Title"=>"欢迎关注理好财",  
          "Description"=>"你不理财，财不理你", 
          "PicUrl"=>"http://cqcbepaper.cqnews.net/cqcb/res/1/20110525/95291306272655343.jpg", 
          "Url" =>"http://www.loveslicai.com"
        );
        break;
      case "CLICK":
        switch ($object->EventKey) {
          case "图文":
            $content[] = array(
              "Title"=>"OpenID", 
              "Description"=>"你的OpenID为：".$object->FromUserName, 
              "PicUrl"=>"", "Url" =>"http://m.cnblogs.com/?u=txw1958&openid=".$object->FromUserName
            );
            break;
        }
        break;
    }
    if(is_array($content)){
      $result = $this->transmitNews($object, $content);
    }else{
      $result = $this->transmitText($object, $content);
    }
    return $result;
  }
  
  
  
  /**
   * 主菜单
   * @return
   */
  private function getMainMenu() {
    $menu = "您好，欢迎关注理好财。请回复数字选择服务：\n\n";
    $menu .= "1  上证指数\n";
    $menu .= "2  财经新闻\n";
    $menu .=  "\n\n详情查看  www.loveslicai.com";
    $menu .= "\n招商热线 18061801686";
    $menu .= "\n微信开发 15221891086";
    return $menu;
  }
  

  private function receiveText($object)   {
    $keyword = trim($object->Content);
    $url = "http://apix.sinaapp.com/weather/?appkey=".$object->ToUserName."&city=".urlencode($keyword); 
    $output = file_get_contents($url);
    $content = json_decode($output, true);

    $result = $this->transmitNews($object, $content);
    return $result;
  }

  /**
   * 回复文字
   *
   * @param unknown_type $object
   * @param unknown_type $content
   * @return unknown
   */
  private function transmitText($object, $content)    {
    if (!isset($content) || empty($content)){
      return "";
    }
    $textTpl = "<xml>
      <ToUserName><![CDATA[%s]]></ToUserName>
      <FromUserName><![CDATA[%s]]></FromUserName>
      <CreateTime>%s</CreateTime>
      <MsgType><![CDATA[text]]></MsgType>
      <Content><![CDATA[%s]]></Content>
      </xml>";
    $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content);
    return $result;
  }
  
  
    
  /**
   * 图片新闻
   *
   * @param unknown_type $object
   * @param unknown_type $newsArray
   * @return unknown
   */
  private function transmitNews($object, $newsArray){
    
//    $newsArray[] = array( 
//      'Title' => 'good', 
//      'Description' => 'very well', 
//      'PicUrl' => 'http://image.sinajs.cn/newchart/min/n/sh000001.gif',
//      'Url' => 'http://www.loveslicai.com/stock'
//    ); 
    
    if(!is_array($newsArray)){
      return "";
    }
    
    $itemTpl = "<item>
                    <Title><![CDATA[%s]]></Title>
                    <Description><![CDATA[%s]]></Description>
                    <PicUrl><![CDATA[%s]]></PicUrl>
                    <Url><![CDATA[%s]]></Url>
                </item>     ";
    
    $item_str = "";
    foreach ($newsArray as $item){
      $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);
    }
    $newsTpl = "<xml>
      <ToUserName><![CDATA[%s]]></ToUserName>
      <FromUserName><![CDATA[%s]]></FromUserName>
      <CreateTime>%s</CreateTime>
      <MsgType><![CDATA[news]]></MsgType>
      <ArticleCount>%s</ArticleCount>
      <Articles>
      $item_str
      </Articles>
      </xml>";

    $result = sprintf($newsTpl, $object->FromUserName, $object->ToUserName, time(), count($newsArray));
    return $result;
  }
  
  
  
  private function logger($content){
//     file_put_contents("log.html" , " REMOTE_ADDR: " .$_SERVER["REMOTE_ADDR"]. " QUERY_STRING: " .$_SERVER["QUERY_STRING"]."<br/>",FILE_APPEND);
//     file_put_contents("log.html" , date('Y-m-d H:i:s ').$content."<br/>",FILE_APPEND);
     file_put_contents("log.html" , date('Y-m-d H:i:s ').$content."<br/>");
  }
  
  
}

