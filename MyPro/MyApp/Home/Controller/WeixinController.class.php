<?php
namespace Home\Controller;
use Think\Controller;
class WeixinController extends Controller {
    public function index(){
        //获取参数
        $nonce      = $_GET['nonce'];
        $token      = 'bluesky';
        $timestamp  = $_GET['timestamp'];
        $echostr    = $_GET['echostr'];
        $signature  = $_GET['signature'];

        //形成数组
        $array = array();
        $array = array($nonce, $timestamp, $token );
        //排序
        sort($array);
        //拼接成字符串然后sha1加密,与$signature对比
        $str = sha1(implode($array));
        if($signature == $str){
            echo $echostr;
            exit;
        }else{
            $this->responseMsg();
        }
    }

    //接收事件推送，并回复
    public function responseMsg(){
        //1,获取到微信推送过来的post数据(xml格式)
        $postArr = $GLOBALS['HTTP_RAW_POST_DATA'];
        //2,处理消息类型，并设置回复类型和内容
        $postObj = simplexml_load_string($postArr);
        //$postObj->ToUserName;
        //$postObj->FromUserName;
        //$postObj->CreateTime;
        //$postObj->MsgType;
        //$postObj->Event;
        //判断该数据包是否是订阅的事件推送
        if(strtolower($postObj->MsgType) == 'event'){
            //如果是关注事件(subscribe)[subscribe(订阅)、unsubscribe(取消订阅)]
            if(strtolower($postObj->Event) == 'subscribe'){
                //回复用户消息
                $toUser    = $postObj->FromUserName; //用户openid
                $fromUser  = $postObj->ToUserName; //微信公众帐号
                $time      = time();
                $msgType   = 'text';
                $content   = '欢迎关注布鲁斯凯的微信公众帐号，回复sex或者性感或者诱惑有惊喜哦！';
                $template  = "  <xml>
                                <ToUserName><![CDATA[%s]]></ToUserName>
                                <FromUserName><![CDATA[%s]]></FromUserName>
                                <CreateTime>%s</CreateTime>
                                <MsgType><![CDATA[%s]]></MsgType>
                                <Content><![CDATA[%s]]></Content>
                                </xml>";
                $info      = sprintf($template, $toUser, $fromUser, $time, $msgType, $content);
                echo $info;
            }
        }
        //用户发送文本消息
        if(strtolower($postObj->MsgType) == 'text'){
            //判断用户发送消息内容
            $userContent = trim(strtolower($postObj->Content));
            switch($userContent){
                case 'sex':
                    $content = '郑晓辉你啥么';
                    break;
                case '性感':
                    $content = '郑晓辉你还真输入啊';
                    break;
                case '诱惑':
                    $content = '这里面真有好东西，但是我就不告诉你';
                    break;
                default:
                    $content = '不管你输入什么，都是这';
                    break;
            }

            //回复用户消息
            $toUser    = $postObj->FromUserName; //用户openid
            $fromUser  = $postObj->ToUserName; //微信公众帐号
            $time      = time();
            $msgType   = 'text';
            $template  = "  <xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[%s]]></MsgType>
                            <Content><![CDATA[%s]]></Content>
                            </xml>";
            $info      = sprintf($template, $toUser, $fromUser, $time, $msgType, $content);
            echo $info;

        }





    }









}