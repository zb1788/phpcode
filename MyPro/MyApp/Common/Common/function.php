<?php
/**
 *批量修改文件夹或者文件名
 *修改规则 需修改程序
 */

function rename_all($dir,$type,$ext=''){
	header('content-type:text/html;charset=utf-8');
	set_time_limit(0);
	vendor('fileDirUtil');
	$fileDir = new \fileDirUtil();

	$dir = iconv('utf-8', 'gbk', $dir);

	$dirTemp = $dir.'/tmp/';
	if(is_dir($dirTemp)){
		exit('临时文件夹已存在！');
	}else{
		mkdir($dirTemp,'0777');
	}


	//目录重命名
	if($type == 'dir'){
		$list = $fileDir->dirNodeTree($dir);
		foreach($list as $v){
			if($v<=140){
				continue;
			}
			$newName = $v - 1;
			$fileDir->moveDir($dir."/".$v,$dirTemp."/".$newName);
		}
	}

	if($type == 'file'){
		$list = $fileDir->dirList($dir,$ext);
		foreach($list as $v){
			$newName = rtrim(basename($v,$ext),'.');
			if($newName<=140){
				continue;
			}
			$newName -= 1;
			rename($v,$dirTemp.'/'.$newName.'.'.$ext);
		}
	}



}



function base64EncodeImage ($image_file) {
    $base64_image = '';
    $image_info = getimagesize($image_file);
    $image_data = fread(fopen($image_file, 'r'), filesize($image_file));
    $base64_image = 'data:' . $image_info['mime'] . ';base64,' . chunk_split(base64_encode($image_data));
    return $base64_image;
}

function base64_image_content($base64_image_content,$path){
    //匹配出图片的格式
    if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)){
        $type = $result[2];
        $new_file = $path."/".date('Ymd',time())."/";
        echo $new_file;
        if(!file_exists($new_file)){
            //检查是否有该文件夹，如果没有就创建，并给予最高权限
            mkdir($new_file, 0700);
        }
        $new_file = $new_file.time().".{$type}";
        if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_image_content)))){
            return '/'.$new_file;
        }else{
            return false;
        }
    }else{
        return false;
    }
}



/**
 * 获取文件后缀名
 * @param string $fileName
 * @return string
 */
function getExt($fileName){
	return strtolower(pathinfo($fileName,PATHINFO_EXTENSION));
}
//获取带中文的文件名
function get_basename($filename){
    return preg_replace('/^.+[\\\\\\/]/', '', $filename);
}

/*
 *----------------------------------------------------------------------------------------------------------------
 *字符串处理相关函数
 * ---------------------------------------------------------------------------------------------------------------
 */

/**
 * 针对没有加双引号的json字符串的处理
 * @DateTime 2016-07-07T21:39:26+0800
 * @param    [type]                   $str  [description]
 * @param    boolean                  $mode [description]
 * @return   [type]                         [description]
 */
function ext_json_decode($str, $mode=false){
  if(preg_match('/\w:/', $str)){
    $str = preg_replace('/(\w+):/is', '"$1":', $str);
  }
  return json_decode($str, $mode);
}


/**
 * 返回唯一字符串
 * @return string
 */
function getUniName(){
	return md5(uniqid(microtime(true),true));
}

/**
 * 分割字符串(支持中英文混合)
 * @param  string $string  [源字符串]
 * @param  string $type    [返回值的类型:array|string]
 * @param  string $charset [源字符串编码,默认utf-8]
 * @return [type]          [description]
 */
function split_str($string, $type='array', $charset='utf-8'){
	//通过ord()函数获取字符的ASCII码值，如果返回值大于 127则表示为中文字符的一半，再获取后一半组合成一个完整字符
    $flag = false;
    if(strtolower($charset) == 'utf-8'){
    	//如果utf-8环境
    	$flag = true;
    	$string = iconv('utf-8', 'gbk', $string);//由于ord函数是在gbk下才能使用,所以如果是中文先转化为gbk再处理
    }

    if(strtolower($charset) != 'gbk' && strtolower($charset) != 'utf-8')
    {
    	exit('参数不合法!');
    }

    //把字符串转化为ascii码存入数组,如果是中文是由两个ASCII码组成，英文是一个
    $length = strlen($string);
    $result = array();
    for($i=0; $i<$length; $i++){
        if(ord($string[$i])>127){
        	$result[] = ord($string[$i]).' '.ord($string[++$i]);
        }else{
            $result[] = ord($string[$i]);
        }
    }

    if(strtolower($type) == 'array'){
    	//如果返回值要数组
    	$str = '';
	    foreach($result as $v){
	        if(empty(strstr($v,' '))){
	            $tmpstr = chr($v);
	            if($flag){
	            	$tmpstr = iconv('gbk', 'utf-8', $tmpstr);
	            }
	            $data[] = $tmpstr;
	        }else{
	            list($a,$b) = explode(' ',$v);
	            $tmpstr = chr($a).chr($b);
	            if($flag){
	            	$tmpstr = iconv('gbk', 'utf-8', $tmpstr);
	            }
	            $data[] = $tmpstr;
	        }
	    }
	    return $data;
    }elseif(strtolower($type) == 'string'){
    	$data = array();
	    foreach($result as $v){
	        if(empty(strstr($v,' '))){
	            $str .= chr($v);
	        }else{
	            list($a,$b) = explode(' ',$v);
	            $str .= chr($a).chr($b);
	        }
	    }
	    $str=iconv('gbk', 'utf-8', $str);
	    return $str;
    }else{
    	exit('参数不合法！');
    }
}

/**
 * 统计字符串中每个字符出现的次数(中文特殊字符慎用)
 * @param  [String] $str [传入字符串]
 * @return [Array]      [数组]
 */
function str2arrOrder($str){
	$testArr = str_split($test); //分割字符串
	$sortArr = array_count_values($testArr);//统计数组中所有值出现的次数
	arsort($sortArr); //排序
	return $sortArr;
}
/*
 *----------------------------------------------------------------------------------------------------------------
 *日期处理相关函数
 * ---------------------------------------------------------------------------------------------------------------
 */


/**
 *获取指定时间时分秒
 */

function dateformate($num){
	$hour = floor($num/3600);
	$minute = floor(($num-3600*$hour)/60);
	$second = floor((($num-3600*$hour)-60*$minute)%60);
}
/**
 * 获取某年的每周第一天和最后一天
 * @param  [int] $year [年份]
 * @return [arr]       [每周的周一和周日]
 */
function get_week($year) {
    $year_start = $year . "-01-01";
    $year_end = $year . "-12-31";
    $startday = strtotime($year_start);
    if (intval(date('N', $startday)) != '1') {
        $startday = strtotime("next monday", strtotime($year_start)); //获取年第一周的日期
    }
    $year_mondy = date("Y-m-d", $startday); //获取年第一周的日期

    $endday = strtotime($year_end);
    if (intval(date('W', $endday)) == '7') {
        $endday = strtotime("last sunday", strtotime($year_end));
    }

    $num = intval(date('W', $endday));
    for ($i = 1; $i <= $num; $i++) {
        $j = $i -1;
        $start_date = date("Y-m-d", strtotime("$year_mondy $j week "));

        $end_day = date("Y-m-d", strtotime("$start_date +6 day"));

        $week_array[$i] = array (
            str_replace("-",
            ".",
            $start_date
        ), str_replace("-", ".", $end_day));
    }
    return $week_array;
}

/**
 * 获取IP
 */
function get_ip(){
	$realip='';
	if (isset($_SERVER)) {
		if (isset($_SERVER[HTTP_X_FORWARDED_FOR])) {
			$realip = $_SERVER[HTTP_X_FORWARDED_FOR];
		} elseif (isset($_SERVER[HTTP_CLIENT_IP])) {
			$realip = $_SERVER[HTTP_CLIENT_IP];
		} else {
			$realip = $_SERVER[REMOTE_ADDR];
		}
	} else {
		if (getenv("HTTP_X_FORWARDED_FOR")) {
			$realip = getenv("HTTP_X_FORWARDED_FOR");
		} elseif (getenv("HTTP_CLIENT_IP")) {
			$realip = getenv("HTTP_CLIENT_IP");
		} else {
			$realip = getenv("REMOTE_ADDR");
		}
	}
	return $realip;
}
/**
 *获取当前url
 */
function get_url() {
    $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
    $php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
    $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
    $relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : $path_info);

    return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
 }

/**
 * 随机从数组取若干个数元素组成新数组
 * @param  [Array]  $array  [源数组]
 * @param  [int]  $total  [取的数量]
 * @param  boolean $unique [是否先对数组去重]
 * @return [Array]          [description]
 */
function unique_array($array, $total, $unique = true){
	$newArray = array();
	if((bool)$unique){
		$array = array_unique($array);
	}
	shuffle($array);
	$length = count($array);
	for($i = 0; $i < $total; $i++){
		if($i < $length){
			$newArray[] = $array[$i];
		}
	}
	return $newArray;
}






/**
 * -----------------------------------------------------------
 * 邮箱发送
 */
function SendMail($address,$title,$message)
{
	$mail=new \PHPMailer();
	// 设置PHPMailer使用SMTP服务器发送Email
	$mail->IsSMTP();
	// 设置邮件的字符编码，若不指定，则为'UTF-8'
	$mail->CharSet='UTF-8';
	// 添加收件人地址，可以多次使用来添加多个收件人
	$mail->AddAddress($address);
	// 设置邮件正文
	$mail->Body=$message;
	// 设置邮件头的From字段。
	$mail->From='370987182@qq.com';
	// 设置发件人名字
	$mail->FromName='xt';
	// 设置邮件标题
	$mail->Subject=$title;
	// 设置SMTP服务器。
	$mail->Host='smtp.qq.com';
	// 设置为“需要验证”
	$mail->SMTPAuth=true;
	// 设置用户名和密码。
	$mail->Username='370987182@qq.com';
	$mail->Password='zzvcom!@#456';
	// 发送邮件。
	return($mail->Send());
}





/**
 * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
 * @param $para 需要拼接的数组
 * return 拼接完成以后的字符串
 */
function createLinkstring($para) {
	$arg  = "";
	while (list ($key, $val) = each ($para)) {
		$arg.=$key."=".$val."&";
	}
	//去掉最后一个&字符
	$arg = substr($arg,0,count($arg)-2);

	//如果存在转义字符，那么去掉转义
	if(get_magic_quotes_gpc()){$arg = stripslashes($arg);}

	return $arg;
}
/**
 * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串，并对字符串做urlencode编码
 * @param $para 需要拼接的数组
 * return 拼接完成以后的字符串
 */
function createLinkstringUrlencode($para) {
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
/**
 * 除去数组中的空值和签名参数
 * @param $para 签名参数组
 * return 去掉空值与签名参数后的新签名参数组
 */
function paraFilter($para) {
	$para_filter = array();
	while (list ($key, $val) = each ($para)) {
		if($key == "sign" || $key == "sign_type" || $val == "")continue;
		else	$para_filter[$key] = $para[$key];
	}
	return $para_filter;
}
/**
 * 对数组排序
 * @param $para 排序前的数组
 * return 排序后的数组
 */
function argSort($para) {
	ksort($para);
	reset($para);
	return $para;
}
/**
 * 写日志，方便测试（看网站需求，也可以改成把记录存入数据库）
 * 注意：服务器需要开通fopen配置
 * @param $word 要写入日志里的文本内容 默认值：空值
 */
function logResult($word='') {
	$fp = fopen("log.txt","a");
	flock($fp, LOCK_EX) ;
	fwrite($fp,"执行日期：".strftime("%Y%m%d%H%M%S",time())."\n".$word."\n");
	flock($fp, LOCK_UN);
	fclose($fp);
}

/**
 * 远程获取数据，POST模式
 * 注意：
 * 1.使用Crul需要修改服务器中php.ini文件的设置，找到php_curl.dll去掉前面的";"就行了
 * 2.文件夹中cacert.pem是SSL证书请保证其路径有效，目前默认路径是：getcwd().'\\cacert.pem'
 * @param $url 指定URL完整路径地址
 * @param $cacert_url 指定当前工作目录绝对路径
 * @param $para 请求的数据
 * @param $input_charset 编码格式。默认值：空值
 * return 远程输出的数据
 */
function getHttpResponsePOST($url, $cacert_url, $para, $input_charset = '') {

	if (trim($input_charset) != '') {
		$url = $url."_input_charset=".$input_charset;
	}
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);//SSL证书认证
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);//严格认证
	curl_setopt($curl, CURLOPT_CAINFO,$cacert_url);//证书地址
	curl_setopt($curl, CURLOPT_HEADER, 0 ); // 过滤HTTP头
	curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
	curl_setopt($curl,CURLOPT_POST,true); // post传输数据
	curl_setopt($curl,CURLOPT_POSTFIELDS,$para);// post传输数据
	$responseText = curl_exec($curl);
	//var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
	curl_close($curl);

	return $responseText;
}

/**
 * 远程获取数据，GET模式
 * 注意：
 * 1.使用Crul需要修改服务器中php.ini文件的设置，找到php_curl.dll去掉前面的";"就行了
 * 2.文件夹中cacert.pem是SSL证书请保证其路径有效，目前默认路径是：getcwd().'\\cacert.pem'
 * @param $url 指定URL完整路径地址
 * @param $cacert_url 指定当前工作目录绝对路径
 * return 远程输出的数据
 */
function getHttpResponseGET($url,$cacert_url) {
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_HEADER, 0 ); // 过滤HTTP头
	curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);//SSL证书认证
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);//严格认证
	curl_setopt($curl, CURLOPT_CAINFO,$cacert_url);//证书地址
	$responseText = curl_exec($curl);
	//var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
	curl_close($curl);

	return $responseText;
}

/**
 * 实现多种字符编码方式
 * @param $input 需要编码的字符串
 * @param $_output_charset 输出的编码格式
 * @param $_input_charset 输入的编码格式
 * return 编码后的字符串
 */
function charsetEncode($input,$_output_charset ,$_input_charset) {
	$output = "";
	if(!isset($_output_charset) )$_output_charset  = $_input_charset;
	if($_input_charset == $_output_charset || $input ==null ) {
		$output = $input;
	} elseif (function_exists("mb_convert_encoding")) {
		$output = mb_convert_encoding($input,$_output_charset,$_input_charset);
	} elseif(function_exists("iconv")) {
		$output = iconv($_input_charset,$_output_charset,$input);
	} else die("sorry, you have no libs support for charset change.");
	return $output;
}
/**
 * 实现多种字符解码方式
 * @param $input 需要解码的字符串
 * @param $_output_charset 输出的解码格式
 * @param $_input_charset 输入的解码格式
 * return 解码后的字符串
 */
function charsetDecode($input,$_input_charset ,$_output_charset) {
	$output = "";
	if(!isset($_input_charset) )$_input_charset  = $_input_charset ;
	if($_input_charset == $_output_charset || $input ==null ) {
		$output = $input;
	} elseif (function_exists("mb_convert_encoding")) {
		$output = mb_convert_encoding($input,$_output_charset,$_input_charset);
	} elseif(function_exists("iconv")) {
		$output = iconv($_input_charset,$_output_charset,$input);
	} else die("sorry, you have no libs support for charset changes.");
	return $output;
}


/*
 * ----------------------------------------------------------------------------------------
* md5function
*/
/**
 * 签名字符串
 * @param $prestr 需要签名的字符串
 * @param $key 私钥
 * return 签名结果
 */
function md5Sign($prestr, $key) {
	$prestr = $prestr . $key;
	return md5($prestr);
}

/**
 * 验证签名
 * @param $prestr 需要签名的字符串
 * @param $sign 签名结果
 * @param $key 私钥
 * return 签名结果
 */
function md5Verify($prestr, $sign, $key) {
	$prestr = $prestr . $key;
	$mysgin = md5($prestr);

	if($mysgin == $sign) {
		return true;
	}
	else {
		return false;
	}
}



/*
 * 阿里支付结束
 * -----------------------------------------------------------------------------------------------
 */




/**
 * 打包文件和文件夹
 * $path传入要打包的文件路径
 */
function filesToZip($path){
	$zip=new \ZipArchive();
	$ran=getUniName();//生成唯一字符串
	$zifile = 'download/'.$ran.'.zip';//打包后的压缩包名称
	if($zip->open($zifile, \ZipArchive::OVERWRITE)=== TRUE){
		addFileToZip($path, $zip); //调用方法，对要打包的根目录进行操作，并将ZipArchive的对象传递给方法
		$zip->close(); //关闭处理的zip文件
	}
	return $zifile;
}
function addFileToZip($path,$zip){
	$handler=opendir($path); //打开当前文件夹由$path指定。
	while(($filename=readdir($handler))!==false){
		if($filename != "." && $filename != ".."){//文件夹文件名字为'.'和‘..’，不要对他们进行操作
			if(is_dir($path."/".$filename)){// 如果读取的某个对象是文件夹，则递归
				addFileToZip($path."/".$filename, $zip);
			}else{ //将文件加入zip对象
				//echo $path."/".$filename.'<br>';
				$dirArr=explode('/', $path);//打包目录默认从根目录开始,就需要把要打包的文件夹上级目录都去掉
				$dir=$dirArr[0].'/';//要去掉的上级目录，默认去掉一级，如果需要多级，就取数组前几位
				$a=substr($path."/".$filename,strlen($dir));//截掉上级目录
				//echo $a;
				//$zip->addFile($path."/".$filename);
				$zip->addFile($path."/".$filename,$a);//打包文件到新的目录
			}
		}
	}
	@closedir($path);
}
/**
 * 可以指定下载显示的文件名，并自动发送相应的Header信息
 * 交互式下载
 * 如果指定了content参数，则下载该参数的内容
 * @static
 * @access protected
 * @param string $filename 下载文件名
 * @param string $showname 下载显示的文件名
 * @param integer $expire  下载内容浏览器缓存时间
 * @return void
 * @throws ThinkExecption
 */
 function download ($filename, $showname='',$expire=180) {
	if(file_exists($filename)){
		$length = filesize($filename);
	}elseif(is_file(UPLOAD_PATH.$filename)){
		$filename = UPLOAD_PATH.$filename;
		$length = filesize($filename);
	}else {
		throw_exception($filename.L('下载文件不存在！'));
	}
	if(empty($showname)){
		$showname = $filename;
	}
	$showname = preg_replace('/^.+[\\\\\\/]/', '', $showname);
	//$showname = basename($showname);
	if(empty($filename)){
		$type = mime_content_type($filename);
	}else{
		$type = "application/octet-stream";
	}
	ob_end_clean();
	//发送Http Header信息 开始下载
	header("content-type:text/html; charset=utf-8");
	header("Pragma: public");
	header("Cache-control: max-age=".$expire);
	//header('Cache-Control: no-store, no-cache, must-revalidate');
	header("Expires: " . gmdate("D, d M Y H:i:s",time()+$expire) . "GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s",time()) . "GMT");
	//下面一行就是改动的地方，即用iconv("UTF-8","GB2312//TRANSLIT",$showname)系统函数转换编码为gb2312
	//如果是UTF-8编码需要进行转码，如果默认GBK则不用转码
	header("Content-Disposition: attachment; filename=". iconv("UTF-8","gb2312",$showname));
	header("Content-Length: ".$length);
	header("Content-type: ".$type);
	header('Content-Encoding: none');
	header("Content-Transfer-Encoding: binary" );
	ob_clean();
	readfile($filename);
	//exit();
}


/**
 * 采集网页源代码
 * @param string $url 网页地址
 */
function pageCollect($url){

	 $ch = curl_init();

	 curl_setopt($ch, CURLOPT_URL, $url);

	 $header[0]='Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8';
	 $header[1]='Accept-Encoding:gzip, deflate, sdch';
	 $header[2]='Accept-Language:zh-CN,zh;q=0.8';
	 $header[3]='Cache-Control:max-age=0';
	 $header[4]='Connection:keep-alive';
	 $header[5]='Host:www.namibox.com';
	 $header[6]='Upgrade-Insecure-Requests:1';
	 $header[7]='User-Agent:Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36';
	 $header[8]='Cookie:CNZZDATA1260570495=1261432844-1476344467-null%7C1482155687';
	 //加入请求的头部信息
	 // curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	 //是否获取头信息
	 curl_setopt($ch, CURLOPT_HEADER, 0);
	 //不获取body信息
	 curl_setopt($ch, CURLOPT_NOBODY, 0);
	 //多方网页使用了gzip压缩，那么获取的内容将有可能为乱码,加入gzip解析,就不乱码了
	 curl_setopt($ch, CURLOPT_ENCODING ,'gzip');
	 //不是post
	 curl_setopt($ch, CURLOPT_POST, 0);

	 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	 //如果有location的话，设置成true可以获取跳转后的页面内容
	 curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
	 $contents = curl_exec($ch);

	 curl_close($ch);


	return $contents;

	//从ftp下载文件
	/**
	$ch = curl_init();
	$url='ftp://192.168.151.127/a.txt';
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 300);
	curl_setopt($ch, CURLOPT_USERPWD, 'boz:boz');//ftp用户名:密码

	$outfile=fopen('dest.txt','wb');//保存文件到本地
	curl_setopt($ch, CURLOPT_FILE, $outfile);

	$content=curl_exec($ch);
	fclose($outfile);

	if(!curl_error($ch)){
		echo 'return: '.$content;
	}else{
		echo 'curl error: '.curl_error(ch);
	}
 */


}


function curl_post($rttId,$start){
	$uri = "http://www.weijiangtai.com/pxs/resourse/tool/indexPages.htm";
	// 参数数组
	$data = array (
	        'rtoId' => '' ,
	        'rttId' => $rttId ,
	        'rthId' => '0' ,
	        'name' => '' ,
	        'type' => '0' ,
	        'level' => '0' ,
	        'start' => $start ,
	        'row' => '50' ,
	// 'password' => 'password'
	);

	$ch = curl_init ();
	// print_r($ch);
	curl_setopt ( $ch, CURLOPT_URL, $uri );
	curl_setopt ( $ch, CURLOPT_POST, 1 );
	curl_setopt ( $ch, CURLOPT_HEADER, 0 );
	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
	$return = curl_exec ( $ch );
	curl_close ( $ch );

	 //$return=mb_convert_encoding($return, 'UTF-8', 'gb2312');
	// return $return;
	 return $return;
	//print_r($return);


}


/**
 * 检测文件是否存在
 * @param unknown $url
 * @return boolean
 */
function check_remote_file_exists($url)
{
	$curl = curl_init($url);
	// 不取回数据
	curl_setopt($curl, CURLOPT_NOBODY, true);
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET'); //不加这个会返回403，加了才返回正确的200，原因不明
	// 发送请求
	$result = curl_exec($curl);
	$found = false;
	// 如果请求没有发送失败
	if ($result !== false)
	{
		// 再检查http响应码是否为200
		$statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		if ($statusCode == 200)
		{
			$found = true;
		}
	}
	curl_close($curl);
	return $found;
}
/**
 * 下载远程图片
 * @param unknown $url
 * @param string $filename
 */
function getImage($url,$filename=""){
	if($url=="")
	{
		return false;
	}
	if($filename=="") {
		$ext=strrchr($url,".");
		$filename=date("dMYHis").$ext;
	}

	ob_start();
	readfile($url);
	$img = ob_get_contents();
	ob_end_clean();
	$size = strlen($img);
	$fp2=@fopen($filename, "a");
	fwrite($fp2,$img);
	fclose($fp2);
	return $filename;
}


/**
 *下载文件
 *后台下载
 *$url文件地址
 *$file文件名称,绝对路径
*/
function httpdown($url, $file="", $timeout=60) {
  $file = empty($file) ? pathinfo($url,PATHINFO_BASENAME) : $file;

  $dir = pathinfo($file,PATHINFO_DIRNAME);
  !is_dir($dir) && @mkdir($dir,0755,true);
  $url = str_replace(" ","%20",$url);


  if(function_exists('curl_init')) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $temp = curl_exec($ch);
    if(@file_put_contents($file, $temp) && !curl_error($ch)) {
      return $file;
    } else {
      return false;
    }
  } else {
    $opts = array(
      "http"=>array(
      "method"=>"GET",
      "header"=>"",
      "timeout"=>$timeout)
    );
    $context = stream_context_create($opts);
    if(@copy($url, $file, $context)) {
      //$http_response_header
      return $file;
    } else {
      return false;
    }
  }
}
//公共函数库

/**
 * 等比缩放函数（以保存的方式实现）
* @param string $picname 被缩放的处理图片源
* @param int $maxx 缩放后图片的最大宽度
* @param int $maxy 缩放后图片的最大高度
* @param string $pre 缩放后图片名的前缀名
* @return String 返回后的图片名称(带路径)，如a.jpg=>s_a.jpg
*/
function imageUpdateSize($picname,$maxx=100,$maxy=100,$pre="s_"){
	$info = getimageSize($picname); //获取图片的基本信息

	$w = $info[0];//获取宽度
	$h = $info[1];//获取高度

	//获取图片的类型并为此创建对应图片资源
	switch($info[2]){
		case 1: //gif
			$im = imagecreatefromgif($picname);
			break;
		case 2: //jpg
			$im = imagecreatefromjpeg($picname);
			break;
		case 3: //png
			$im = imagecreatefrompng($picname);
			break;
		default:
			die("图片类型错误！");
	}

	//计算缩放比例
	if(($maxx/$w)>($maxy/$h)){
		$b = $maxy/$h;
	}else{
		$b = $maxx/$w;
	}

	//计算出缩放后的尺寸
	$nw = floor($w*$b);
	$nh = floor($h*$b);

	//创建一个新的图像源(目标图像)
	$nim = imagecreatetruecolor($nw,$nh);

	//执行等比缩放
	imagecopyresampled($nim,$im,0,0,0,0,$nw,$nh,$w,$h);

	//输出图像（根据源图像的类型，输出为对应的类型）
	$picinfo = pathinfo($picname);//解析源图像的名字和路径信息
	$newpicname= $picinfo["dirname"]."/".$pre.$picinfo["basename"];
	switch($info[2]){
		case 1:
			imagegif($nim,$newpicname);
			break;
		case 2:
			imagejpeg($nim,$newpicname);
			break;
		case 3:
			imagepng($nim,$newpicname);
			break;
	}
	//释放图片资源
	imagedestroy($im);
	imagedestroy($nim);
	//返回结果
	return $newpicname;
}

//测试：
//echo imageUpdateSize("./images/bg.jpg",200,200,"ss_");  //  ./images/s_bg.jpg


/**
 * 为一张图片添加上一个logo图片水印（以保存的方式实现）
 * @param string $picname 被处理图片源
 * @param string $logo 水印图片
 * @param string $pre 处理后图片名的前缀名
 * @return String 返回后的图片名称(带路径)，如a.jpg=>n_a.jpg
 */
function imageUpdateLogo($picname,$logo,$pre="n_"){
	$picnameinfo = getimageSize($picname); //获取图片源的基本信息
	$logoinfo = getimageSize($logo); //获取logo图片的基本信息
	//var_dump($logoinfo);
	//根据图片类型创建出对应的图片源
	switch($picnameinfo[2]){
		case 1: //gif
			$im = imagecreatefromgif($picname);
			break;
		case 2: //jpg
			$im = imagecreatefromjpeg($picname);
			break;
		case 3: //png
			$im = imagecreatefrompng($picname);
			break;
		default:
			die("图片类型错误！");
	}
	//根据logo图片类型创建出对应的图片源
	switch($logoinfo[2]){
		case 1: //gif
			$logoim = imagecreatefromgif($logo);
			break;
		case 2: //jpg
			$logoim = imagecreatefromjpeg($logo);
			break;
		case 3: //png
			$logoim = imagecreatefrompng($logo);
			break;
		default:
			die("logo图片类型错误！");
	}


	//执行图片水印处理
	imagecopyresampled($im,$logoim,$picnameinfo[0]-$logoinfo[0],$picnameinfo[1]-$logoinfo[1],0,0,$logoinfo[0],$logoinfo[1],$logoinfo[0],$logoinfo[1]);

	//也可以添加文字
	//$text='一些文字';
	//#设置水印字体颜色
	//$color = imagecolorallocatealpha($im,27,26,26,20);
	//#设置字体文件路径
	//$fontfile = EMLOG_ROOT."/msyhbd.ttf";
	//imagettftext($im,14,0,740,85,$color,$fontfile,$text);


	//输出图像（根据源图像的类型，输出为对应的类型）
	$picinfo = pathinfo($picname);//解析源图像的名字和路径信息
	$newpicname= $picinfo["dirname"]."/".$pre.$picinfo["basename"];
	switch($picnameinfo[2]){
		case 1:
			imagegif($im,$newpicname);
			break;
		case 2:
			imagejpeg($im,$newpicname);
			break;
		case 3:
			imagepng($im,$newpicname);
			break;
	}
	//释放图片资源
	imagedestroy($im);
	imagedestroy($logoim);
	//返回结果
	return $newpicname;
}

//测试
//echo imageUpdateLogo("./images/bg.jpg","./images/logo.png");

/**
 * 导出excel
 * @param  [array] $data       [要导出数据数组]
 * @param  [array] $headArr    [数据的标题]
 * @param  string $fileName   [文件名]
 * @param  string $sheetName  [sheet名称]
 * @param  [array] $columWidth [每一列的宽度]
 * @return [type]             [description]
 */

/*
---------------------------------------------
$data：数据的格式
array (size=2)
  0 =>
    array (size=3)
      'id' => string '1' (length=1)
      'code' => string '001' (length=3)
      'name' => string '张三' (length=6)
  1 =>
    array (size=3)
      'id' => string '2' (length=1)
      'code' => string '002' (length=3)
      'name' => string '李四' (length=6)
-----------------------------------------------
$headArr：标题的格式
array (size=3)
  0 => string 'ID' (length=2)
  1 => string '编码' (length=6)
  2 => string '姓名' (length=6)
-----------------------------------------------
$columWidth：宽度的格式
array (size=3)
  0 => string '10' (length=2)
  1 => string '15' (length=2)
  2 => string '20' (length=2)
-----------------------------------------------
 */
function output_excel($data, $headArr, $fileName='' ,$sheetName='Sheet1', $columWidth=null){
	vendor('PHPExcel');

	$objExcel = new \PHPExcel();
	$objWriter = new \PHPExcel_Writer_Excel2007($objExcel); //2007格式
	//$objWriter = new \PHPExcel_Writer_Excel5($objExcel); // 用于其他版本格式

	$keyCount = count($headArr);
	if($keyCount>26){
		exit('此函数列数不能超过26列!');
	}else{
		$keyArray = range('A','Z');
	}



	//判断
	if($fileName == ''){
		$fileName = date('YmdHis').mt_rand(1000,9999).'.xlsx';
	}else{
		$ext = strtolower(pathinfo($fileName,PATHINFO_EXTENSION));
		if($ext != 'xlsx'&&$ext != 'xls'){
			echo '文件名后缀必须为xlsx或者xls';
			exit;
		}
	}

	$objExcel->setActiveSheetIndex(0);
	$objActSheet = $objExcel->getActiveSheet();
	$objActSheet->setTitle($sheetName);


	//循环数据的标题数组,设置第一行的标题，以及各行的宽度以及换行等
	foreach($headArr as $k=>$v){
		$colum = $keyArray[$k];
		//设置宽度
		if(empty($columWidth)){
			$objActSheet->getColumnDimension($colum)->setAutoSize(true);
		}else{
			$objActSheet->getColumnDimension($colum)->setWidth($columWidth[$k]);
		}
		//开始设置第一行的标题的值
		$objActSheet->setCellValue($colum.'1', $v);
	}

	//设置某列为文本格式
	//$objActSheet->getStyle('B')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );


	//循环data数据输出到excel
	foreach ($data as $key=>$rows){
		$rows = array_values($rows);
		foreach($rows as $k=>$value ){
			if($keyArray[$k] == 'BB'){
				//某列需要数字不要科学计数法
				$objActSheet->setCellValueExplicit($keyArray[$k].($key+2), $value,\PHPExcel_Cell_DataType::TYPE_STRING);
			}else{
				echo $keyArray[$k].($key+2).'=>'.$value;
				$objActSheet->setCellValue($keyArray[$k].($key+2),$value);
			}
		}

	}

	$fileName = iconv("utf-8", "gb2312", $fileName);
	$objWriter->save($fileName);  //保存到服务器

	ob_end_clean();//清除缓冲区,避免乱码
	$filesize = filesize($fileName);
	header( "Content-Type: application/force-download;charset=utf-8");
	header( "Content-Disposition: attachment; filename= ".$fileName);
	header( "Content-Length: ".$filesize);
	ob_clean();
	readfile($fileName);

	unlink($fileName); //下载完成后删除服务器文件

}

/**
 * csv_get_lines 读取CSV文件中的某几行数据
 * @param $csvfile csv文件路径
 * @param $lines 读取行数
 * @param $offset 起始行数
 * @return array
 * */

//使用方法
/*
		默认从第二行开始读取,第一行为垃圾数据
        $data = csv_get_lines('./caiji/1.csv', 5, 0);
        var_dump($data);
*/
function csv_get_lines($csvfile, $lines, $offset = 0) {
    if(!$fp = fopen($csvfile, 'r')) {
     return false;
    }

    $i = $j = 0;

	 while (false !== ($line = fgets($fp))) {
	  if($i++ < $offset) {
	   continue;
	  }
	  break;
	 }

	 $data = array();

	 while(($j++ < $lines) && !feof($fp)) {
	  $data[] = fgetcsv($fp);
	 }
	 fclose($fp);
    return $data;
}
