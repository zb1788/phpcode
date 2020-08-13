<?php
namespace Home\Controller;
use Think\Controller;
class CurlDemoController extends Controller {

	//curl采集，常规的
	/**
	 * [caiji_default description]
	 * [curl采集默认程序]
	 * @return [type]
	 */
	public function caiji_default(){
		$url='http://www.backcountry.com/Store/sitemaps/categoriesIndex.jsp';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		//header需要的时候加上
		$header[0]='Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8';
	 	$header[1]='Accept-Encoding:gzip, deflate, sdch';
	 	//加入请求的头部信息
	 	//curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

	 	//是否获取头信息
	 	curl_setopt($ch, CURLOPT_HEADER, 0);
		//获取body信息
		curl_setopt($ch, CURLOPT_NOBODY, 0);
		//多方网页使用了gzip压缩，那么获取的内容将有可能为乱码,加入gzip解析,就不乱码了
		curl_setopt($ch, CURLOPT_ENCODING ,'gzip');
		//不是post
		curl_setopt($ch, CURLOPT_POST, 0);

		//获取到的页面信息没有输出到页面上，而是当作变量存储
	 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	 	//如果页面会自动跳转有location的话，设置成true可以获取跳转后的页面内容
	 	curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);

	 	$contents = curl_exec($ch);

		//检查是否有错误
		if(curl_errno($curl)) {
		    exit('Curl error: ' . curl_error($curl));
		}

	 	curl_close($ch);

	 	//file_put_contents('caiji/1111.txt',$contents);
		return $contents;


	}


	//simple_html_dom采集源代码
    public function dom_simple(){
    	vendor("simple_html_dom");
		// 新建一个Dom实例
		$html = new \simple_html_dom();

		// 从url中加载
		$html->load_file('http://www.weijiangtai.com/pxs/resourse/tool/index.htm');

		// 从字符串中加载
		//$html->load('<html><body>从字符串中加载html文档演示</body></html>');

		//从文件中加载
		//$html->load_file('path/file/test.html');


		$re = $html->find('ul.list_zy');

		//查找结果为数组,数组中嵌套对象

		//获取a标签的href
		$a = $html->find('a');



		//var_dump($re);
		$res = $re[0]->children;
		for($i=1;$i<count($res);$i++){

			//var_dump($res);exit();
			$aa = $res[$i]->children;
			$name_1 = $aa[0]->find('span',1)->innertext;

			echo $name_1;

			$childs = $aa[2]->find('li');

			foreach($childs as $v ){
				$str = $v->find('div',0)->attr['onclick'];
				$arr = explode("'", $str);
				//var_dump($arr);
				echo $v->find('span',1)->innertext;
				echo $arr[3];

				echo '<br>';


			}


		}





    }

    /*
    *curl POST提交
    */

    public function curl_post(){
    	//加入头部信息
    	$header[0]='Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
		$header[1]='Content-Type:application/x-www-form-urlencoded; charset=utf-8';
		//参数信息
	    $para = array (
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
        $para = http_build_query($para);
    	$url='';
		$ch = curl_init();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);//SSL证书认证
		// curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);//严格认证
		// curl_setopt($ch, CURLOPT_CAINFO,$cacert_url);//证书地址
		curl_setopt($ch, CURLOPT_HEADER, 0 ); // 过滤HTTP头
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT,10);
		curl_setopt($ch,CURLOPT_POST,true); // post传输数据
		curl_setopt($ch,CURLOPT_POSTFIELDS,$para);// post传输数据
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);//头部信息

		$responseText = curl_exec($ch);
		//var_dump( curl_error($ch) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
		$errorCode = curl_errno($ch);
		if(0 !== $errorCode) {
            var_dump($errorCode);
            exit;
        }
		curl_close($ch);
    }

    /**
     * CURL POST发送json对象数据
     * @return [type] [description]
     */
    public function curl_json(){
        $posturl = 'http://119.15.137.115:9999/';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $posturl);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string))
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
        $contents = curl_exec($ch);
        curl_close($ch);
        echo $contents;
    }

    public function curl_get($url){
		$ch = curl_init();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt($ch, CURLOPT_HEADER, 0 ); // 过滤HTTP头
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
		// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);//SSL证书认证
		// curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);//严格认证
		// curl_setopt($ch, CURLOPT_CAINFO,$cacert_url);//证书地址
		$responseText = curl_exec($ch);
		//var_dump( curl_error($ch) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
		$errorCode = curl_errno($ch);
		if(0 !== $errorCode) {
            var_dump($errorCode);
            exit;
        }
		curl_close($ch);

		return $responseText;
    }


    //存取cookie
    public function curl_login(){
	    //初始化变量
	    $cookie_file = tempnam('./caiji/tmp','cookie');
	    $login_url = "http://xxx.com/logon.php";
	    $verify_code_url = "http://xxx.com/verifyCode.php";

	    echo "正在获取COOKIE...\n";
	    $curl = curl_init();
	    $timeout = 5;
	    curl_setopt($curl, CURLOPT_URL, $login_url);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $timeout);
	    curl_setopt($curl,CURLOPT_COOKIEJAR,$cookie_file); //获取COOKIE并存储
	    $contents = curl_exec($curl);
	    curl_close($curl);

	    echo "COOKIE获取完成，正在取验证码...\n";
	    //取出验证码
	    $curl = curl_init();
	    curl_setopt($curl, CURLOPT_URL, $verify_code_url);
	    curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie_file);
	    curl_setopt($curl, CURLOPT_HEADER, 0);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	    $img = curl_exec($curl);
	    curl_close($curl);

	    $fp = fopen("verifyCode.jpg","w");
	    fwrite($fp,$img);
	    fclose($fp);
	    echo "验证码取出完成，正在休眠，20秒内请把验证码填入code.txt并保存\n";
	    //停止运行20秒
	    sleep(20);

	    echo "休眠完成，开始取验证码...\n";
	    $code = file_get_contents("code.txt");
	    echo "验证码成功取出：$code\n";
	    echo "正在准备模拟登录...\n";

	    $post = "username=maben&pwd=hahahaha&verifycode=$code";
	    $curl = curl_init();
	    curl_setopt($curl, CURLOPT_URL, $url);
	    curl_setopt($curl, CURLOPT_HEADER, false);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
	    curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
	    curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie_file);
	    $result=curl_exec($curl);
	    curl_close($curl);

	    //这一块根据自己抓包获取到的网站上的数据来做判断
	    if(substr_count($result,"登录成功")){
	     echo "登录成功\n";
	    }else{
	     echo "登录失败\n";
	     exit;
	    }
    }



    public function curl_login_weibo(){
	    //初始化变量
	    $cookie_file = tempnam('./caiji/tmp','cookie');
	    $login_url = "https://passport.weibo.cn/signin/login?entry=mweibo&res=wel";
	    //$verify_code_url = "http://xxx.com/verifyCode.php";

	    echo "正在获取COOKIE...\n";
	    $curl = curl_init();
	    $timeout = 5;
	    curl_setopt($curl, CURLOPT_URL, $login_url);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $timeout);
	    curl_setopt($curl,CURLOPT_COOKIEJAR,$cookie_file); //获取COOKIE并存储
	    $contents = curl_exec($curl);
	    curl_close($curl);

	    echo "COOKIE获取完成，正在取验证码...\n";
	    //取出验证码
	    $curl = curl_init();
	    curl_setopt($curl, CURLOPT_URL, $verify_code_url);
	    curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie_file);
	    curl_setopt($curl, CURLOPT_HEADER, 0);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	    $img = curl_exec($curl);
	    curl_close($curl);

	    $fp = fopen("verifyCode.jpg","w");
	    fwrite($fp,$img);
	    fclose($fp);
	    echo "验证码取出完成，正在休眠，20秒内请把验证码填入code.txt并保存\n";
	    //停止运行20秒
	    sleep(20);

	    echo "休眠完成，开始取验证码...\n";
	    $code = file_get_contents("code.txt");
	    echo "验证码成功取出：$code\n";
	    echo "正在准备模拟登录...\n";

	    $post = "username=maben&pwd=hahahaha&verifycode=$code";
	    $curl = curl_init();
	    curl_setopt($curl, CURLOPT_URL, $url);
	    curl_setopt($curl, CURLOPT_HEADER, false);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
	    curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
	    curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie_file);
	    $result=curl_exec($curl);
	    curl_close($curl);

	    //这一块根据自己抓包获取到的网站上的数据来做判断
	    if(substr_count($result,"登录成功")){
	     echo "登录成功\n";
	    }else{
	     echo "登录失败\n";
	     exit;
	    }
    }


    /**
     * curl使用代理进行抓取
     * @return [type] [description]
     */
    public function curl_proxy(){
         $ch = curl_init();
         curl_setopt($ch, CURLOPT_URL, "http://blog.51yip.com");
         curl_setopt($ch, CURLOPT_HEADER, false);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, TRUE);
         curl_setopt($ch, CURLOPT_PROXY, 125.21.23.6:8080);
         //url_setopt($ch, CURLOPT_PROXYUSERPWD, 'user:password');如果要密码的话，加上这个
         $result=curl_exec($ch);
         curl_close($ch);
    }






}