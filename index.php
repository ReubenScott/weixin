<?php
/*
    工作室 
    http://
    CopyRight 2014 All Rights Reserved
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
  
  //验证消息
  public function valid() {
    $echoStr = $_GET["echostr"]; //随机字符串
    //valid signature , option
    if($this->checkSignature()){
      echo $echoStr;
      exit;
    }
  }
    
  //检查签名
  private function checkSignature() {
    $signature = $_GET["signature"];    //微信加密签名
    $timestamp = $_GET["timestamp"];    //时间戳
    $nonce = $_GET["nonce"];            //随机数
    $token = TOKEN;
    $tmpArr = array($token, $timestamp, $nonce);
    sort($tmpArr, SORT_STRING);  //进行字典序排序
    //sha1加密后与签名对比
    if(sha1(implode($tmpArr)) == $signature ){
      return true;
    }else{
      return false;
    }
  }
  
  //响应消息
  public function responseMsg(){
    $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
    if (!empty($postStr)){
      $this->logger("R ".$postStr);
      $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
      $RX_TYPE = trim($postObj->MsgType);

      switch ($RX_TYPE) {
        case "event":
            $result = $this->receiveEvent($postObj);
            break;
        case "text":
            $result = $this->receiveText($postObj);
            break;
        case "image":
            $result = $this->receiveImage($postObj);
            break;
        case "location":
            $result = $this->receiveLocation($postObj);
            break;
        case "voice":
            $result = $this->receiveVoice($postObj);
            break;
        case "video":
            $result = $this->receiveVideo($postObj);
            break;
        case "link":
            $result = $this->receiveLink($postObj);
            break;
        default:
            $result = "unknown msg type: ".$RX_TYPE;
            break;
      }
      $this->logger("T ".$result);
      echo $result;
    }else {
      echo "";
      exit;
    }
  }

  //接收事件消息
  private function receiveEvent($object) {
    $content = "";
    switch ($object->Event)    {
      case "subscribe":
        $title = "欢迎关注理好财 ";
        $title .= (!empty($object->EventKey))?("\n来自二维码场景 ".str_replace("qrscene_","",$object->EventKey)):"";
        
        $content[] = array(
          "Title"=> $title,  
          "Description"=>"你不理财，财不理你", 
          "PicUrl"=>"http://cqcbepaper.cqnews.net/cqcb/res/1/20110525/95291306272655343.jpg", 
          "Url" =>"http://www.loveslicai.com"
        );
        
        break;
      case "unsubscribe":
        $content = "取消关注";
        break;
      case "SCAN":
        $content = "扫描场景 ".$object->EventKey;
        break;
      case "CLICK":
        switch ($object->EventKey) {
          case "COMPANY":
            $content = "理好财提供互联网相关产品与服务。";
            break;
          default:
            $content = "点击菜单：".$object->EventKey;
            break;
        }
        break;
      case "LOCATION":
        $content = "上传位置：纬度 ".$object->Latitude.";经度 ".$object->Longitude;
        break;
      case "VIEW":
        $content = "跳转链接 ".$object->EventKey;
        break;
      default:
        $content = "receive a new event: ".$object->Event;
        break;
    }
    
    if(is_array($content)){
      $result = $this->transmitNews($object, $content);
    }else{
      $result = $this->transmitText($object, $content);
    }
    return $result;
  }

  //接收文本消息
  private function receiveText($object) {
    switch ($object->Content)      {
      case "文本":
        $content = "这是个文本消息";
        break;
      case "图文":
      case "单图文":
        $content = array();
        $content[] = array("Title"=>"单图文标题",  "Description"=>"单图文内容", "PicUrl"=>"http://discuz.comli.com/weixin/weather/icon/cartoon.jpg", "Url" =>"http://m.cnblogs.com/?u=txw1958");
        break;
      case "多图文":
        $content = array();
        $content[] = array("Title"=>"多图文1标题", "Description"=>"", "PicUrl"=>"http://discuz.comli.com/weixin/weather/icon/cartoon.jpg", "Url" =>"http://m.cnblogs.com/?u=txw1958");
        $content[] = array("Title"=>"多图文2标题", "Description"=>"", "PicUrl"=>"http://d.hiphotos.bdimg.com/wisegame/pic/item/f3529822720e0cf3ac9f1ada0846f21fbe09aaa3.jpg", "Url" =>"http://m.cnblogs.com/?u=txw1958");
        $content[] = array("Title"=>"多图文3标题", "Description"=>"", "PicUrl"=>"http://g.hiphotos.bdimg.com/wisegame/pic/item/18cb0a46f21fbe090d338acc6a600c338644adfd.jpg", "Url" =>"http://m.cnblogs.com/?u=txw1958");
        break;
      case "音乐":
        $content = array("Title"=>"最炫民族风", "Description"=>"歌手：凤凰传奇", "MusicUrl"=>"http://121.199.4.61/music/zxmzf.mp3", "HQMusicUrl"=>"http://121.199.4.61/music/zxmzf.mp3");
        break;
      default:
        $content = $this->getMainMenu();
        break;
    }
    if(is_array($content)){
      if (isset($content[0]['PicUrl'])){
        $result = $this->transmitNews($object, $content);
      }else if (isset($content['MusicUrl'])){
        $result = $this->transmitMusic($object, $content);
      }
    }else{
      $result = $this->transmitText($object, $content);
    }
    return $result;
  }

  //接收图片消息
  private function receiveImage($object) {
    $content = array("MediaId"=>$object->MediaId);
    $result = $this->transmitImage($object, $content);
    return $result;
  }

  //接收位置消息
  private function receiveLocation($object) {
    $content = "你发送的是位置，纬度为：".$object->Location_X."；经度为：".$object->Location_Y."；缩放级别为：".$object->Scale."；位置为：".$object->Label;
    $result = $this->transmitText($object, $content);
    return $result;
  }

  //接收语音消息
  private function receiveVoice($object)    {
    if (isset($object->Recognition) && !empty($object->Recognition)){
      $content = "你刚才说的是：".$object->Recognition;
      $result = $this->transmitText($object, $content);
    }else{
      $content = array("MediaId"=>$object->MediaId);
      $result = $this->transmitVoice($object, $content);
    }

    return $result;
  }

  //接收视频消息
  private function receiveVideo($object) {
    $content = array("MediaId"=>$object->MediaId, "ThumbMediaId"=>$object->ThumbMediaId, "Title"=>"", "Description"=>"");
    $result = $this->transmitVideo($object, $content);
    return $result;
  }

  //接收链接消息
  private function receiveLink($object) {
    $content = "你发送的是链接，标题为：".$object->Title."；内容为：".$object->Description."；链接地址为：".$object->Url;
    $result = $this->transmitText($object, $content);
    return $result;
  }

  //回复文本消息
  private function transmitText($object, $content)   {
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

  //回复图片消息
  private function transmitImage($object, $imageArray)  {
    $itemTpl = "<Image>
                  <MediaId><![CDATA[%s]]></MediaId>
                </Image>";

    $item_str = sprintf($itemTpl, $imageArray['MediaId']);

    $textTpl = "<xml>
                  <ToUserName><![CDATA[%s]]></ToUserName>
                  <FromUserName><![CDATA[%s]]></FromUserName>
                  <CreateTime>%s</CreateTime>
                  <MsgType><![CDATA[image]]></MsgType>
                  $item_str
                </xml>";

    $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time());
    return $result;
  }

    //回复语音消息
    private function transmitVoice($object, $voiceArray) {
      $itemTpl = "<Voice>
                    <MediaId><![CDATA[%s]]></MediaId>
                  </Voice>";

      $item_str = sprintf($itemTpl, $voiceArray['MediaId']);
  
      $textTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[voice]]></MsgType>
                    $item_str
                  </xml>";

      $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time());
      return $result;
    }

  //回复视频消息
  private function transmitVideo($object, $videoArray) {
    $itemTpl = "<Video>
                  <MediaId><![CDATA[%s]]></MediaId>
                  <ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
                  <Title><![CDATA[%s]]></Title>
                  <Description><![CDATA[%s]]></Description>
                </Video>";

    $item_str = sprintf($itemTpl, $videoArray['MediaId'], $videoArray['ThumbMediaId'], $videoArray['Title'], $videoArray['Description']);

    $textTpl = "<xml>
                  <ToUserName><![CDATA[%s]]></ToUserName>
                  <FromUserName><![CDATA[%s]]></FromUserName>
                  <CreateTime>%s</CreateTime>
                  <MsgType><![CDATA[video]]></MsgType>
                  $item_str
                </xml>";

    $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time());
    return $result;
  }

  //回复图文消息
  private function transmitNews($object, $newsArray) {
    if(!is_array($newsArray)){
        return;
    }
    $itemTpl = "<item>
                  <Title><![CDATA[%s]]></Title>
                  <Description><![CDATA[%s]]></Description>
                  <PicUrl><![CDATA[%s]]></PicUrl>
                  <Url><![CDATA[%s]]></Url>
                </item> ";
    $item_str = "";
    foreach ($newsArray as $item){
        $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);
    }
    $newsTpl = "<xml>
                  <ToUserName><![CDATA[%s]]></ToUserName>
                  <FromUserName><![CDATA[%s]]></FromUserName>
                  <CreateTime>%s</CreateTime>
                  <MsgType><![CDATA[news]]></MsgType>
                  <Content><![CDATA[]]></Content>
                  <ArticleCount>%s</ArticleCount>
                  <Articles>$item_str</Articles>
                </xml>";

    $result = sprintf($newsTpl, $object->FromUserName, $object->ToUserName, time(), count($newsArray));
    return $result;
  }

  //回复音乐消息
  private function transmitMusic($object, $musicArray)    {
    $itemTpl = "<Music>
                  <Title><![CDATA[%s]]></Title>
                  <Description><![CDATA[%s]]></Description>
                  <MusicUrl><![CDATA[%s]]></MusicUrl>
                  <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
                </Music>";

    $item_str = sprintf($itemTpl, $musicArray['Title'], $musicArray['Description'], $musicArray['MusicUrl'], $musicArray['HQMusicUrl']);

    $textTpl = "<xml>
                  <ToUserName><![CDATA[%s]]></ToUserName>
                  <FromUserName><![CDATA[%s]]></FromUserName>
                  <CreateTime>%s</CreateTime>
                  <MsgType><![CDATA[music]]></MsgType>
                  $item_str
                </xml>";

    $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time());
    return $result;
  }
  
  
  

  
  /**
   * 帮助菜单
   */
  private function getMainMenu() {
    $menu = "您好，欢迎关注理好财。请回复数字选择服务：\n\n";
    $menu .= "1  上证指数\n";
    $menu .= "2  财经新闻\n";
    $menu .=  "\n\n详情查看  www.loveslicai.com";
    $menu .= "\n招商热线 18061801686";
    $menu .= "\n微信开发 15221891086";
    $menu .= "\n\n".date("Y-m-d H:i:s",time());
    
    return $menu;
  }
  

  //日志记录
  private function logger($log_content)    {
    if(isset($_SERVER['HTTP_APPNAME'])){   
      //SAE
      sae_set_display_errors(false);
      sae_debug($log_content);
      sae_set_display_errors(true);
    }else if($_SERVER['REMOTE_ADDR'] != "127.0.0.1"){ 
      //LOCAL
      $max_size = 10000;
      $log_filename = "log.xml";
      if(file_exists($log_filename) and (abs(filesize($log_filename)) > $max_size)){
        unlink($log_filename);
      }
      file_put_contents($log_filename, date('H:i:s')." ".$log_content."\r\n", FILE_APPEND);
    }
  }
}

?>