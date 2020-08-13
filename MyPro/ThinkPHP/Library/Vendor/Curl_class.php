<?php
/**
 * @author [boz]
 * 如果json请求注意header必须有
 * array('Content-Type: application/json','Content-Length: ' . strlen($data_string))
 */
class Curl{
    public $ch = null;
    public function __construct(){
        if (!function_exists('curl_init')){
            exit('Please open curl first');
        }else{
            $this->ch = curl_init();
        }
    }

    /**
     * [执行函数]
     * @DateTime  2016-05-27T11:57:11+0800
     * @param     [type]                   $method      [description]
     * @param     [type]                   $url         [description]
     * @param     string                   $fields      [description]
     * @param     string                   $httpHeaders [description]
     * @param     string                   $username    [description]
     * @param     string                   $password    [description]
     * @param     integer                  $timeOut     [description]
     * @return    [type]                                [description]
     */
    protected function excute($method, $url, $fields = '', $httpHeaders = '', $username = '', $password = '',$timeOut=10){
        if (is_string($url) && strlen($url)){
            curl_setopt($this->ch, CURLOPT_URL, $url);
        }else{
            return false;
        }

        //是否显示头部信息
        curl_setopt($this->ch, CURLOPT_HEADER, false);
        //是返回内容还是直接输出，false为直接输出，true是返回内容
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        //gzip防止页面乱码
        //curl_setopt($this->ch, CURLOPT_ENCODING ,'gzip');
        //不获取body信息(默认获取)
        //curl_setopt($this->ch, CURLOPT_NOBODY, false);
        //如果页面会自动跳转有location的话，设置成true可以获取跳转后的页面内容
        curl_setopt($this->ch,CURLOPT_FOLLOWLOCATION,1);




        //设置超时时间
        if($timeOut>0){
            curl_setopt($this->ch, CURLOPT_TIMEOUT, $timeOut);//单位秒
            //curl_setopt($this->ch, CURLOPT_TIMEOUT_MS, $timeOut);//单位毫秒
        }
        if ($username != ''){
            curl_setopt($this->ch, CURLOPT_USERPWD, $username . ':' . $password);
        }

        $method = strtolower($method);
        //post请求
        if($method == 'post'){
            curl_setopt($this->ch, CURLOPT_POST, true);
            if (is_array($fields)){
                $fields = $this->createLinkstringUrlencode($fields);
            }
            curl_setopt($this->ch, CURLOPT_POSTFIELDS, $fields);
        }else if($method == 'get'){
            curl_setopt($this->ch, CURLOPT_POST, false);
        }else{
            return false;
        }

        //是否发送头信息
        if (is_array($httpHeaders))
        {
            curl_setopt($this->ch, CURLOPT_HTTPHEADER, $httpHeaders);
        }
        if($httpHeaders != '' && !is_array($httpHeaders)){
            exit('httpHeaders need Array');
        }

        $ret = curl_exec($this->ch);


        //检查是否有错误反生
        if(curl_errno($this->ch)){
            $info  = curl_getinfo( $this->ch );
            $info['errno'] = $errno;
            var_dump($info);
            // exit;
            return 'err';
        }else{
            return $ret;
        }

    }

    /**
     * post请求
     * @param  [type]  $url         [URL地址]
     * @param  string  $fields      [参数(字符串或者数组)]
     * @param  integer $timeOut     [超时时间]
     * @param  Array  $httpHeaders [头信息]
     * @param  string  $username    [用户名]
     * @param  string  $password    [密码]
     * @return [type]               [description]
     */
    public function post($url, $fields = '', $timeOut=10, $httpHeaders = '', $username = '', $password = ''){
        return $this->excute('POST', $url, $fields, $httpHeaders, $username, $password, $timeOut);
    }

    /**
     * get请求
     * @param  [type]  $url         [URL地址]
     * @param  [type]  $fields      [参数(字符串或者数组)]
     * @param  integer $timeOut     [超时时间]
     * @param  string  $httpHeaders [头信息]
     * @param  string  $username    [用户名]
     * @param  string  $password    [密码]
     * @return [type]               [description]
     */
    public function get($url, $fields = '', $timeOut=10, $httpHeaders = '', $username = '', $password = ''){
        return $this->excute('GET', $url, $fields, $httpHeaders, $username, $password, $timeOut);
    }

    /**
     * 数组用&拼接为字符串并urlencode值
     * @param  [type] $para [description]
     * @return [type]       [description]
     */
    private function createLinkstringUrlencode($para)
    {
        $arg  = "";
        while (list ($key, $val) = each ($para)) {
            $arg.=$key."=".urlencode($val)."&";
        }
        //去掉最后一个&字符
        $arg = substr($arg,0,count($arg)-2);

        //如果存在转义字符，那么去掉转义
        if(get_magic_quotes_gpc()){$arg = stripslashes($arg);}

        return $arg;
    }


    public function quit(){
        curl_close($this->ch);
    }



}