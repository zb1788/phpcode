<?php
namespace Home\Controller;
use Think\Controller;
class AjaxController extends Controller {
    public function index(){
        $this->getuserinfo();
    }

    /**
     *请求接口方法1
     */
    function getuserinfo(){
    	//$userid=cookie('username');
    	$userid='jz41010110092381';

        //$url = 'http://tmshenan.czbanbantong.com/tms/interface/queryStudent.jsp?queryType=byNames&usernames='.$userid;
       // $url = 'http://tmshenan.czbanbantong.com/tms/interface/queryStudent.jsp?queryType=byParent&parentAccount='.$userid;
	   

        $para['ad'] = time() . mt_rand(1000,9999);
        $para['at'] = 'u';
        $para['a'] = '01788';
        $para['ut'] = '4';
        $para['c'] ='u_kousuan1';
        $para['ac'] = '34.';
        $para['n'] = 1;
        $para['t'] = date('Y-m-d H:i:s');
        $para['r'] = 'ss';
        $para['i'] = $c;

        $str=createLinkstring($para);
        echo $str;exit();
 
        $ad = time() . mt_rand(1000,9999);
        $at = 'u';
        $a = '01788';
        $ut = '4';
        $c = 'u_kousuan1';
        $ac = '34.';
        $n = 1;
        $t = date('Y-m-d H:i:s');
        $r = 'ss';
        $i = $c;

        $parm = '{"ad":"'.$ad.'","at":"'.$at.'","a":"'.$a.'","ut":"'.$ut.'","c":"'.$c.'","ac":"'.$ac.'","n":"'.$n.'","t":"'.$t.'","r":"'.$r.'","i":"'.$i.'"}';


        // //echo $parm;exit();
        // $url = 'http://ubkw.czbanbantong.com/ub/interface/data_report.jsp?p=' . $parm;

        // //echo $url;exit();


         // vendor('fileDirUtil');
         // $fileDir = new \fileDirUtilabc();
         // echo 'aa';
         // exit();

        // vendor('Test');


        // $tt=new \TestAbc();
        // $tt ->testfun();
        // exit();

  
        $userid='jz41010110092381';
        //$url = 'http://tmshenan.czbanbantong.com/tms/interface/queryStudent.jsp?queryType=byParent&parentAccount='.$userid;
        
        
        $url='http://58.17.9.89/ub/interface/data_report.jsp?p={%22ad%22:%2214528373024319%22,%22at%22:%22u%22,%22a%22:%2236010110000064%22,%22ut%22:%224%22,%22c%22:%22u_kousuan3%22,%22ac%22:%2236.01.01%22,%22n%22:%221%22,%22t%22:%222016-01-15%2013:55:02%22,%22r%22:%22ss%22,%22i%22:%22aaa%22}';

        

        //$url=urlencode($url);

                $curl = curl_init($url);
        //curl_setopt($curl, CURLOPT_HEADER, 0 ); // 过滤HTTP头
        curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);// 显示输出结果

        $responseText = curl_exec($curl);
        var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
        curl_close($curl);

       // $responseText = iconv("GBK", "UTF-8//IGNORE", $responseText);  //查看编码，不一致要转码，否则json无法转化为数组

        $result = json_decode($responseText, true);
        var_dump($result);


exit();

	    
        $json_ret = file_get_contents($url);

        $json_ret = iconv("GBK", "UTF-8//IGNORE", $json_ret);  //查看编码，不一致要转码，否则json无法转化为数组
echo $json_ret;
        $result = json_decode($json_ret, true);
        var_dump($result);
      //  $realname= $result[rtnArray][0][realname];

        //echo $realname;
    }


 
    /**
     *请求接口方法2
     */

    function getuserinfo2(){
		$userid='01788';
        $url = 'http://tmshenan.czbanbantong.com/tms/interface/queryStudent.jsp?queryType=byNames&usernames='.$userid;
        $curl = curl_init($url);
		//curl_setopt($curl, CURLOPT_HEADER, 0 ); // 过滤HTTP头
		curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);// 显示输出结果

		$responseText = curl_exec($curl);
		//var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
		curl_close($curl);

		$responseText = iconv("GBK", "UTF-8//IGNORE", $responseText);  //查看编码，不一致要转码，否则json无法转化为数组

		$result = json_decode($responseText, true);
		var_dump($result);
        $realname= $result[rtnArray][0][realname];
		echo  $realname;
    }


    /**
     *请求接口方法3
     */

    function getuserinfo3(){
    	
		$userid='01788';
       // $url = '../../Ajax/test?id='.$userid;
		//$url='http://zb.czbanbantong.com:8888/Ajax/test?id='.$userid;
        $curl = curl_init($url);
		//curl_setopt($curl, CURLOPT_HEADER, 0 ); // 过滤HTTP头

$headerArr[User-Agent]='Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.9.2.3) Gecko/20100401 Firefox/3.6.3';
$headerArr[Connection]='keep-alive';

        curl_setopt ($ch, CURLOPT_HTTPHEADER , $headerArr ); 
		curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
		curl_setopt($curl,CURLOPT_BINARYTRANSFER, true) ;

		$responseText = curl_exec($curl);
		var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
		curl_close($curl);

		echo $responseText;
		//$responseText = iconv("GBK", "UTF-8//IGNORE", $responseText);  //查看编码，不一致要转码，否则json无法转化为数组

    }


    public function test(){
    	$aa=I('id');
    	echo $aa;
    }











}