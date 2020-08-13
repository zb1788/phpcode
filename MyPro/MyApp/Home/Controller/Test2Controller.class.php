<?php
namespace Home\Controller;
use Think\Controller;
class Test2Controller extends Controller {


    public function test(){
    $ch = curl_init();
    $url = 'http://game.hfghjg.com.cn/mxsp/data.php';
    $post_fields['sentence'] = '我是万能的';

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // 执行HTTP请求
    curl_setopt($ch , CURLOPT_URL , $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    $res = curl_exec($ch);

    var_dump(json_decode($res));

    }

    public function tt(){

    // $s = '大海';
    // $ch = curl_init();
    // $url = 'http://apis.baidu.com/geekery/music/query?s='.urlencode($s).'&size=10&page=1';
    // //$url = 'http://apis.baidu.com/geekery/music/query?s=%E5%8D%81%E5%B9%B4&size=10&page=1';
    // $url = 'http://apis.baidu.com/geekery/music/playinfo?hash=c23d025ee9ece593abd96d7b97db97b4';
    // $header = array(
    //     'apikey: 606fff0b7a332716798a7b3880857f00',
    // );

   //  $title = '丑八怪';
   //  $name = '薛之谦';
   // $ch = curl_init();
   //  $url = ' http://music.soso.com/music.cgi?ty=getsongurls&w='.urlencode($title).'&pl='.urlencode($name);

header('Content-type:text/html;charset=utf-8');


//配置您申请的appkey
//$appkey = '606fff0b7a332716798a7b3880857f00';

    $word = '新闻';
    $ch = curl_init();
    $url = 'http://apis.baidu.com/txapi/weixin/wxhot?num=10&rand=1&word='.urlencode($word).'&page=1&src=%E4%BA%BA%E6%B0%91%E6%97%A5%E6%8A%A5';
    $header = array(
        'apikey: 606fff0b7a332716798a7b3880857f00',
    );
    // 添加apikey到header
    curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // 执行HTTP请求
    curl_setopt($ch , CURLOPT_URL , $url);
    $res = curl_exec($ch);

    var_dump(json_decode($res,true));
exit;
    $news = json_decode($res,true);
    if($news['msg'] == 'success'){
        $arr = array();
        $newslist = $nes['newslist'];
        shuffle($newslist);
        foreach($newslist as $k=>$v){
            $arr[] = new NewsResponseItem($v['title'], $v['description'], $v['picUrl'], $v['url']);
            if($k == 2){
                break;
            }
        }
        $this->responseNews($arr);
    }else{
        $text = '刚偷看美女洗澡了，再试试？';
    }






    }

    public function abc(){
        $dir = '{"0":[0,1,2,3,4,5,6,7,8],"1":[9,10,11,12,13,14,15,16,17,18,19]}';
        var_dump(json_decode($dir,true));

    }


	public function testXz(){
		set_time_limit(0);
		$url = 'http://rss1henan.czbanbantong.com:9000/material/20160725/20160725094939390943768044525/data.xml?rcode=167a5ae8cabf956040ea677e0a448b03245fe451340b142dcb7cf95d76fe2f25&validate_code=d15700618d32d2378f60754fc2d85965&username=4101091234630003&flashtype=%C3%A8%C2%94%C2%BA%C3%A4%C2%B8%C2%9C%C3%A7%C2%A7%C2%80&ad=410109123463000320181009185651_0&st=vod&ct=0&CLIENTIP=218.28.20.138';
		for($i=0;$i<10000;$i++){
			$starttime = explode(' ',microtime());
			$ran = mt_rand(1,100000);
			$url = $url.'&ran='.$ran;

			$xml = file_get_contents($url);
			$filename = './test/'.$i.'.xml';
    			file_put_contents($filename, $xml);
				$endtime = explode(' ',microtime());
			 $thistime = $endtime[0]+$endtime[1]-($starttime[0]+$starttime[1]);
			 $thistime = round($thistime,3);
			 if($thistime>1){
			  echo "执行".$i."次耗时：".$thistime." 秒。".time().'<br/>';
			ob_flush();
				flush();
			 }
		}
	
	}




}