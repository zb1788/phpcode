<?php
namespace Home\Controller;
use Think\Controller;
class TestController extends Controller {
    public function testnow(){
        $t1 = microtime(true);
        $t2 =microtime();
        echo $t1.'<br>'.$t2;exit();
        for($i=0;$i<=100000000;$i++){

        }

        $t2 = microtime(true);
        echo '耗时'.round($t2-$t1,3).'秒';
    }
    public function test(){
        $this->display();
    }
    public function ddd(){
//     	$data=M('letters')->select();
//     	var_dump($data);
//     	$json=json_encode($data);
//     	var_dump($json);

    	$letters=array();
    	$letters['look']='a.png';
    	$letters['pic']='a.png';

    	$arr['a']=$letters;

    	$letters['look']='b.png';
    	$letters['pic']='b.png';
    	$arr['b']=$letters;

    	var_dump(json_encode($arr));
    }
    public function ttp(){
    	vendor('jpgraph.jpgraph');
    	//vendor('jpgraph.jpgraph_bar');
    	vendor('jpgraph.jpgraph_line');









 }

    public function tes(){
$data='[{"Name":"a1","Number":"123","Contno":"000","QQNo":""},{"Name":"a1","Number":"123","Contno":"000","QQNo":""},{"Name":"a1","Number":"123","Contno":"000","QQNo":""}]';
var_dump(json_decode($data)) ;
var_dump(json_decode($data,true)) ;

$json='{ "people": [

{ "firstName": "Brett", "lastName":"McLaughlin", "email": "aaaa" },

{ "firstName": "Jason", "lastName":"Hunter", "email": "bbbb"},

{ "firstName": "Elliotte", "lastName":"Harold", "email": "cccc" }

]}';
var_dump(json_decode($json,true)) ;

$ara = '{"河南省":{"郑州市":["中原区","巩义"]}}';
$ara2 = '{"河南省":[{"郑州市":["中原区","巩义"]}]}';

var_dump(json_decode($ara,true));
var_dump(json_decode($ara2,true));


$aaa='{"name":"中国", "province":[ { "name":"黑龙江", "cities":{ "city":["哈尔滨","大庆"]}}] }';
//var_dump(json_decode($aaa,true));

}

    public function tttt(){
    	$this->display();
    }

    public function test2(){
    	$this->display();
    }
    public function testDemo(){

        echo 'abc';
        //echo '<script>alert("aa");</script>';
        echo 'def';
        //ob_end_clean();//清除缓冲区,避免乱码
        // $filename = 'caiji/1.pdf';
        // $filename=iconv('utf-8', 'gbk', $filename);
        // $filesize = filesize($filename);
        // header( "Content-Type: application/force-download;charset=utf-8");
        //header( "Content-Disposition: attachment; filename= ".$filename);
       // header( "Content-Length: ".$filesize);
        //ob_clean();
        //readfile($filename);
        //exit;

        //$this->assign('ceshi','abc');
        $this->display();
    }


    public function jsonFun(){
    	$json=I('str/s');
    	$json=str_replace('&quot;', '"', $json);
    	foreach (json_decode($json,true) as $v){
    		echo $v['id'];
    		echo $v['val'];
    	}
    }
    public function CreatThmImag(){
    	$imgpath='Uploads/20150930145436327.png';
    	$img = new \Think\Image();
    	$img->open($imgpath);
    	$img->thumb(150, 150);
    	$thumbImg='Uploads/20150930145436327_thm.png';
    	$img->save($thumbImg);
    	echo $thumbImg.'生成成功！';
    }
    public function ceshi(){
    	$str = "John&amp;&#039;Adams&#039;<b>cu</b>";
    	echo $str;
    	echo "<br />";
    	echo htmlspecialchars_decode($str);
    	echo "<br />";
    	echo htmlspecialchars_decode($str, ENT_QUOTES);
    	echo "<br />";
    	echo htmlspecialchars_decode($str, ENT_NOQUOTES);
    }
    public function xieWord(){
		    	$html = '
ˈæbsənt
		';

		    	ob_start();
		    	echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40">';
		    	echo $html;
		    	echo '</html>';
		    	$data = ob_get_contents();
		    	ob_end_clean();
		    	$this->wirtefile('download/1.doc', $data);
    }


    public function save1($path){
    	echo '</html>';
    	$data = ob_get_contents();
    	ob_end_clean();
    	$this->wirtefile($path, $data);
    }

    public function wirtefile ($fn,$data)
    {
    	$fp=fopen($fn,"wb");
    	fwrite($fp,$data);
    	fclose($fp);
    }

    public function aaa(){
    	$fn='caiji/16_mp3.mp3';
        if(file_exists($fn)){
            echo 'a';
            $size = filesize($fn);
            echo $size;
            exit('xx');
        }else{
            echo 'b';
        }

    }

    public function computerinfo(){
    	//获取本机硬盘信息
    	exec('wmic DISKDRIVE get deviceid,Caption,size,InterfaceType 2>&1',$output);
    	print_r($output);
    }





    public function downloadDb(){
        set_time_limit(0);
        ini_set('memory_limit', '256M');

        //根据bookid循环下载每本书的db压缩包

        $m = M();
        $sql = 'select _id from book where _id>214 and _id<500';
        $data = $m->query($sql);

        // $data = array(193,196,202);
        $data = array(38,40,43,44,217,238,244,256,262,265,274,289,298,301,304,379,400,403,430,433,436,442,445);
        foreach($data as $v){
            // $i = $v['_id'];
            $i = $v;
            //用第一个域名下载
            $domain = 'http://kkcyw2.bs2dl.huanjuyun.com/';
            $flag = $this->testUrl($i, $domain);
            if($flag == 1){
                sleep(5);
                continue;
            }else{
                //第二个域名下载
                $domain = 'http://kkcyw2.45603.bs2.huanjuyun.com/';
                $flag = $this->testUrl($i, $domain);
                if($flag == 1){
                    sleep(5);
                    continue;
                }else{
                    sleep(5);
                    echo '<h2>'.$i.'not found</h2><br>';
                    file_put_contents('ERROR.TXT', $i.',', FILE_APPEND);
                }
            }
        }
    }

    //测试下载地址，从5到10循环找到正确的下载地址
    private function testUrl($bookid, $domain){
        $flag = 0;
        for($i=0; $i<10; $i++){
            $url = $domain.'clientdb/test/'.$i.'/book_'.$bookid.'.db.zip';
            echo $url.'<br>';
            ob_flush();
            flush();
            $zip = 'caiji/yuwen100/book_'.$bookid.'.db.zip';
            $re = httpdown($url, $zip,120);
            if(file_exists($re)){
                $size = filesize($re)/1024;
                if($size<5){
                    unlink($re);
                }else{
                    $flag = 1;
                    break;
                }
            }else{
                continue;
            }
        }
        return $flag;
    }

    public function unZip(){

    }








}