<?php
namespace Home\Controller;
use Think\Controller;
class CurlController extends Controller {
    //下载纳米盒网站的源码保存到本地txt
    public function nami_txt(){
        set_time_limit(0);
        // $arr=array('1a','1b','2a','2b','3a','3b','4a','4b','5a','5b','6a','6b','cz','gz');
        $arr=array('gz');
        foreach ($arr as $key => $value) {
            echo $value.'<br>';
            $this->namihedom($value);
        }

    }
    public function namihedom($str){
        set_time_limit(0);
        vendor('fileDirUtil');
        $fileDir = new \fileDirUtil();
        $url="http://www.namibox.com/v/click/".$str;


        $htmlDoc = new \DOMDocument;
        $htmlDoc->loadHTMLFile($url);
        $htmlDoc->normalizeDocument();//格式化为标准的dom

        //根据id解析查找dom元素
        $tables_list = $htmlDoc->getElementById('content');
        $lilist= $tables_list->childNodes;
        $length=$lilist->length;



        $book = M('db_nami.book','nm_');

        //echo $length;
        //echo $lilist->item(27)->getAttribute('id');
        for ($i=3; $i <$length ; $i+=2) {
            //echo $lilist->item($i)->getElementsByTagName('div')->item(0)->nodeValue;exit();
            //echo $lilist->item($i)->childNodes->item(3)->childNodes->item(3)->getAttribute('href');exit();

            $childlength=$lilist->item($i)->childNodes->item(3)->childNodes->length;
            //echo $childlength;exit();
            for($j=1;$j<$childlength;$j+=2){
                $banben = $lilist->item($i)->getElementsByTagName('div')->item(0)->nodeValue;

                // $banben = str_replace(array("\r\n", "\r", "\n"), "", $banben);
                // $banben = str_replace(' ', '', $banben);
                // $banben = str_replace('&nbsp;', '', $banben);
                // $banben = str_replace('\r\n', '', $banben);
                // $banben=preg_replace('/\s+/', '', $banben);
                // echo '|'.trim($banben).'|';
                // exit();


                $url2 = 'http://www.namibox.com'.$lilist->item($i)->childNodes->item(3)->childNodes->item($j)->getAttribute('href');

                // echo $url2;exit;
                $tmparr = explode('_', $url2);
                $tmparr2 = explode('（', $tmparr[1]);
                $xueke = $tmparr2[0];
                $banbeninfo = rtrim($tmparr2[1],'）');


                if($str=='gz'){
                    $nx = substr($url2,strripos($url2,'/')-3,3);
                    $nianji = substr($nx,0,2);
                    $xueqi = substr($nx,2,1);
                }else{
                    $nx = substr($url2,strripos($url2,'/')-2,2);
                    $nianji = substr($nx,0,1);
                    $xueqi = substr($nx,1,1);
                }
                //echo $nx . $nianji .$xueqi;

                $ran = substr($url2,strripos($url2,'/')+1,6);

                $path = 'caiji/namihe/new/'.$nianji.'/'.$xueqi;
                $fileDir->createDir($path);
                 $file = $path.'/'.$nianji.$xueqi.$ran.'.txt';
                 echo $url2;


                 //查询版本是否存在
                 $sql_q = 'select * from db_nami.nm_book where id>190 and flag=20 and txt like "%'.$nianji.$xueqi.$ran.'%"';
                 // echo $sql_q;
                 $data_q = $book->query($sql_q);



                 if(empty($data_q)){
                    echo '没有<br>';
                    continue;
                 }


                 $contents = file_get_contents($url2);
                 // $contents = pageCollect($url2);


                 // exit(var_dump($contents));

                 //判断是否收费

                 preg_match_all('/付费(.*?)_请下/',$contents, $isSell, PREG_SET_ORDER);


                 // exit(var_dump($isSell));

                 if($isSell[0][0] == '付费提醒_请下'){
                    $sql_u = 'update db_nami.nm_book set flag=22 where id='.$data_q[0]['id'];
                    $book->execute($sql_u);
                    continue;
                 }


                preg_match_all('/这次我是真(.*?)马上就搞定/',$contents, $isEmpty, PREG_SET_ORDER);

                if(!empty($isEmpty)){
                    $sql_u = 'update db_nami.nm_book set flag=23 where id='.$data_q[0]['id'];
                    $book->execute($sql_u);
                    continue;
                }


                 //echo $banben.$xueke.$xueqi.$nianji.$url2.$file;
                //echo '<br>';
                $fn = fopen($file, 'wb');
                fwrite($fn, $contents);
                fclose($fn);



                $banben = str_replace(array("\r\n", "\r", "\n"), "", $banben);
                $banben = str_replace(' ', '', $banben);
                $banben = str_replace('\r\n', '', $banben);


                $data['nianji'] = $nianji;
                $data['xueqi'] = $xueqi;
                $data['banben'] = trim($banben);
                $data['banbeninfo'] = $banbeninfo;
                $data['xueke'] = $xueke;
                $data['txt'] = $file;
                // $book->add($data);
            }


        }


    }
    public function namihecaiji(){
        header("Content-type: text/html; charset=utf-8");
        set_time_limit(0);

        vendor('fileDirUtil');
        $fileDir = new \fileDirUtil();
        //$arr=array('1a','1b','c');
        $url = 'http://www.namibox.com/v/click/1b';
        $contents = pageCollect($url);

        //$contents=mb_convert_encoding($contents, 'UTF-8', 'gb2312');

        $file = 'caiji/namihe/menu.txt';
        // $fn = fopen($file, 'wb');
        // fwrite($fn, $contents);
        // fclose($fn);

        //  $contents = file_get_contents($file);

        $str = str_replace(array("\r\n", "\r", "\n"), "", $contents);

        preg_match_all('/;<\/div>(.*?)<div.*?<a href="(.*?)" targets="_blank"  >.*?<a href="(.*?)" targets="_blank"  >.*?<a href="(.*?)" targets="_blank"  >/', $str, $out,PREG_SET_ORDER);


        var_dump($out);
        echo trim($out[0][1]);
        //$book = M('db_nami.book','nm_');

    }

/**
 * 微讲台图片采集a
 * @return [type] [description]
 */
    public function aaa(){
        set_time_limit(0);
        vendor('fileDirUtil');
         $fileDir = new \fileDirUtil();
         $urlArr=$fileDir->readFile2array('caiji/test.txt');
         //var_dump($urlArr);exit();

         foreach ($urlArr as $key => $value) {
             $tmparr=explode(' ', $value);
             //var_dump($tmparr);
             $dir='caiji/'.$tmparr[0].'/'.$tmparr[1];

             //dir='caiji/语文/一年级';
             //echo $dir;
             $dir=iconv('utf-8', 'gbk', $dir);
             //echo $dir;

             $fileDir->createDir($dir);
             $rttId=$tmparr[2];
             $contents = curl_post($rttId,1);


            $contents=str_replace('\r\n', '', $contents);
            $contents=str_replace('\\', '', $contents);
            //echo $contents;exit();
            //preg_match_all("/<img src=\"(.*?)\" \/><\/a>(.*?)<span title=\"(.*?)\">/",$contents, $out, PREG_SET_ORDER);
            preg_match_all("/', '(.*?)'\);\"><img(.*?)<span title=\"(.*?)\">/",$contents, $out, PREG_SET_ORDER);
            preg_match_all("/\"totalPages\":(.*?)}/",$contents, $out2, PREG_SET_ORDER);

            //var_dump($out);
            $dir=iconv('gbk', 'utf-8', $dir);
            foreach ($out as $key => $value) {

                $picurl='http://www.weijiangtai.com'.$value[1];
                $picname=$value[3];

                //echo $picurl.'||'.$picname;exit;

                $ext=pathinfo($picurl,PATHINFO_EXTENSION);


                $picname=$dir.'/'.$picname.'_'.rand(1000,9999).'.'.$ext;
                //$picname='caiji/yuanma/'.$picname.'_'.rand(1000,9999).'.'.$ext;

                //echo $picurl.$picname;
                $img = file_get_contents($picurl);

                $picname=iconv('utf-8', 'gb2312', $picname);
                //echo $picname;
                file_put_contents($picname, $img);
                //getImage($picurl,$picname);

            }
           //echo $out2[0][1];exit();
            if($out2[0][1]>1){

                for($i=2;$i<=$out2[0][1];$i++){
                    $contents = curl_post($rttId,$i);
                    //echo $contents;exit();
                    vendor('fileDirUtil');
                    $fileDir = new \fileDirUtil();
                    //$fileDir->createDir('caiji/语文/一年级');
                    $contents=str_replace('\r\n', '', $contents);
                    $contents=str_replace('\\', '', $contents);
                    //echo $contents;exit();

                    //preg_match_all("/<img src=\"(.*?)\" \/><\/a>(.*?)<span title=\"(.*?)\">/",$contents, $out, PREG_SET_ORDER);
                    preg_match_all("/', '(.*?)'\);\"><img(.*?)<span title=\"(.*?)\">/",$contents, $out, PREG_SET_ORDER);

                    foreach ($out as $key => $value) {
                        $picurl='http://www.weijiangtai.com'.$value[1];
                        $picname=$value[3];

                        $ext=pathinfo($picurl,PATHINFO_EXTENSION);
                        $picname=$dir.'/'.$picname.'_'.rand(1000,9999).'.'.$ext;

                        //echo $picurl.$picname;
                        $img = file_get_contents($picurl);

                        $picname=iconv('utf-8', 'gb2312', $picname);
                        //echo $picname;
                        file_put_contents($picname, $img);
                        //getImage($picurl,$picname);

                    }
                }
            }


         }


    }

  /*
    *www.shef.ac.uk请求网站获取健康数据
    */
    public function healthGet(){
        set_time_limit(0);
        $m = M('hlinfo');
        //$resArr = $m->where('result1=21.3')->order('id desc')->select();
        $resArr = $m->order('id desc')->select();
        //$resArr = $m->where('id=120')->order('id desc')->select();
        //var_dump($resArr);exit;
        foreach($resArr as $v){
          //  if($v['id']!=5){continue;}

$url = 'http://www.shef.ac.uk/FRAX/tool.aspx?lang=chs';
    $ch = curl_init ();
                // //cookie相关设置
    $cookie_file    =    tempnam('./caiji/tmp','cookie');
    date_default_timezone_get('PRC');
    curl_setopt($ch,CURLOPT_COOKIESESSION,TRUE);
    curl_setopt($ch,CURLOPT_COOKIEFILE,'cookiefile');
    curl_setopt($ch,CURLOPT_COOKIEJAR,$cookie_file);//设置Cookie信息保存在指定的文件中
    //curl_setopt($ch,CURLOPT_COOKIE,session_name().'='.session_id());
       curl_setopt ( $ch, CURLOPT_URL, $url );
    curl_setopt ( $ch, CURLOPT_HEADER, 0 );
    curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
    $content = curl_exec ( $ch );
 curl_close ( $ch );
//var_dump($content);
preg_match_all('/ContentPlaceHolder1_hdnAuthCode" value="(.*?)"/',$content, $outtt, PREG_SET_ORDER);
//file_put_contents('caiji/1111.txt',$content);
//var_dump($outtt);
//exit;
$code = $outtt[0][1];

            var_dump($v);
            $id = $v['id'];
            $uid = $v['e1'];//用户id
            $name = $v['e2'];//用户名字
            $age = $v['e3'];//年龄
            $height = $v['e4'];//身高
            $weight = $v['e5'];//体重
            $e6 = $v['e6'];//既往骨折史
            $e7 = $v['e7'];//父母髋部骨折史
            $e8 = $v['e8'];//抽烟行为
            $e9 = $v['e9'];//饮酒
            $e10 = $v['e10'];//继发性骨质疏松症
            $e11 = $v['e11'];//风湿性关节炎
            $e12 = $v['e12'];//肾上腺皮质激素服用
            $e13 = $v['e13'];//L2-4骨密度不用
            $e14 = $v['e14'];//L2-4T值 不用
            $e15 = $v['e15'];//eck(股骨颈)骨密度
            $e16 = $v['e16'];//股骨颈)T值
            $e17 = $v['e17'];//新发骨折

            //$this->changeStr($e6);
           // echo $e6;
            //var_dump($this->changeStr($e6));exit;
            $e6Arr = $this->changeStr($e6);
            $e7Arr = $this->changeStr($e7);
            $e8Arr = $this->changeStr($e8);
            $e9Arr = $this->changeStr($e9);
            $e10Arr = $this->changeStr($e10);
            $e11Arr = $this->changeStr($e11);
            $e12Arr = $this->changeStr($e12);

            echo '既往骨折史='.$e6Arr[0].'母腕骨骨折='.$e7Arr[0].'吸烟='.$e8Arr[0].'上腺皮质激素='.$e12Arr[0].'风湿性关节炎='.$e11Arr[0].'继发性骨质疏松症='.$e10Arr[0];



        // 参数数组
        $data = array (
                'ctl00$ScriptManager1' => 'ctl00$ContentPlaceHolder1$updResult|ctl00$ContentPlaceHolder1$btnCalculate' ,
                'ScriptManager1_TSM' => ';;AjaxControlToolkit, Version=4.1.40412.0, Culture=neutral, PublicKeyToken=28f01b0e84b6d53e:en-US:acfc7575-cdee-46af-964f-5d85d9cdcf92:ea597d4b:b25378d2' ,
                '__EVENTTARGET' => '' ,
                '__EVENTARGUMENT' => '' ,
                'language' => 'chs',
                'ctl00$ContentPlaceHolder1$hdnethnicity' => '2' ,
                'ctl00$ContentPlaceHolder1$hdnpreviousfracture' => $e6Arr[0] ,//既往骨折史 0没有 1有
                'ctl00$ContentPlaceHolder1$hdnpfracturehip' => $e7Arr[0] ,//父母腕骨骨折
                'ctl00$ContentPlaceHolder1$hdncurrentsmoker' => $e8Arr[0],//吸烟
                'ctl00$ContentPlaceHolder1$hdnglucocorticoids' => $e12Arr[0],//肾上腺皮质激素
                'ctl00$ContentPlaceHolder1$hdnarthritis' => $e11Arr[0],//风湿性关节炎
                'ctl00$ContentPlaceHolder1$hdnosteoporosis' => $e10Arr[0],//继发性骨质疏松症
                'ctl00$ContentPlaceHolder1$hdnalcohol' => $e9Arr[0],//酒精
                'ctl00$ContentPlaceHolder1$hdnbmd' => 'N/A',//BMD 骨密度 没有是N/A
                'ctl00$ContentPlaceHolder1$hdnthescore' => 'undefined', //undefined
                'ctl00$ContentPlaceHolder1$hdnsex' => '1', // 0 male男/ 1 female女
                'ctl00$ContentPlaceHolder1$hdnbmi' => '', //为空不用管
                'ctl00$ContentPlaceHolder1$hdnAuthCode' => $code, //884fd276012d2493fc46743b4608c912
                'ctl00$ContentPlaceHolder1$nameid' => '',//空不用管
                'ctl00$ContentPlaceHolder1$toolage' => '58',//年龄
                'ctl00$ContentPlaceHolder1$toolagehidden' => '58',//年龄
                'ctl00$ContentPlaceHolder1$year' => '',
                'ctl00$ContentPlaceHolder1$month' => '',
                'ctl00$ContentPlaceHolder1$day' => '',
                'ctl00$ContentPlaceHolder1$sex' => 'female',//性别 male/female
                'ctl00$ContentPlaceHolder1$toolweight' => $weight,//体重
                'ctl00$ContentPlaceHolder1$toolweighthidden' => $weight,//体重
                'ctl00$ContentPlaceHolder1$ht' => $height,//身高
                'ctl00$ContentPlaceHolder1$toolheighthidden' => $height,//身高
                'ctl00$ContentPlaceHolder1$facture' => $e6Arr[1],//既往骨折史 no  yes
                'ctl00$ContentPlaceHolder1$facture_hip' => $e7Arr[1], //父母腕骨骨折 no
                'ctl00$ContentPlaceHolder1$smoking' => $e8Arr[1],//吸烟 no
                'ctl00$ContentPlaceHolder1$glu' => $e12Arr[1],//肾上腺皮质激素
                'ctl00$ContentPlaceHolder1$rhe_art' => $e11Arr[1],//风湿性关节炎
                'ctl00$ContentPlaceHolder1$sec_ost' => $e10Arr[1],//继发性骨质疏松症
                'ctl00$ContentPlaceHolder1$alcohol' => $e9Arr[1],//酒精
                'dxa' => 'N/A',
                'ctl00$ContentPlaceHolder1$bmd_input' => '',
                '__VIEWSTATE' => '/wEPDwULLTExNDA4NDI3OTgPZBYCZg9kFgRmD2QWBAICDxUBGGNzcy9zdHlsZS5jc3M/MTQzMDIyMDMxMGQCBg8VAiFqcy9qcXVlcnktMS43LjEubWluLmpzPzE0MzAyMjAzMTYbanMvanNyZXNvdXJjZS5qcz8xNDMwMjIwMzE4ZAIBD2QWBgIBDxYCHgRUZXh0BeYJPG9wdGlvbiB2YWx1ZT0iZW4iID7oi7Hor608L29wdGlvbj48b3B0aW9uIHZhbHVlPSJhciIgPumYv+aLieS8r+ivrTwvb3B0aW9uPjxvcHRpb24gdmFsdWU9ImJlIiA+5a2f5Yqg5ouJPC9vcHRpb24+PG9wdGlvbiB2YWx1ZT0iY2hzIiBzZWxlY3RlZD0nc2VsZWN0ZWQnPueugOWMliDkuK3mloc8L29wdGlvbj48b3B0aW9uIHZhbHVlPSJjaHQiID7kvKDnu58g5Lit5paHPC9vcHRpb24+PG9wdGlvbiB2YWx1ZT0iY3IiID7lhYvnvZflnLDkupo8L29wdGlvbj48b3B0aW9uIHZhbHVlPSJjeiIgPuaNt+WFizwvb3B0aW9uPjxvcHRpb24gdmFsdWU9ImRhIiA+5Li56bqm55qEPC9vcHRpb24+PG9wdGlvbiB2YWx1ZT0iZGUiID7lvrfor608L29wdGlvbj48b3B0aW9uIHZhbHVlPSJkdSIgPuiNt+WFsOS6ujwvb3B0aW9uPjxvcHRpb24gdmFsdWU9ImVzIiA+54ix5rKZ5bC85Lqa6K+tPC9vcHRpb24+PG9wdGlvbiB2YWx1ZT0iZmEiID7ms6Lmlq/or608L29wdGlvbj48b3B0aW9uIHZhbHVlPSJmaSIgPuiKrOWFsDwvb3B0aW9uPjxvcHRpb24gdmFsdWU9ImZyIiA+5rOV6K+tPC9vcHRpb24+PG9wdGlvbiB2YWx1ZT0iZ3IiID7luIzohYror608L29wdGlvbj48b3B0aW9uIHZhbHVlPSJpYyIgPuWGsOWymzwvb3B0aW9uPjxvcHRpb24gdmFsdWU9ImluIiA+5Y2w5bqm5bC86KW/5LqaPC9vcHRpb24+PG9wdGlvbiB2YWx1ZT0iaXQiID7mhI/lpKfliKk8L29wdGlvbj48b3B0aW9uIHZhbHVlPSJqcCIgPuaXpeivrTwvb3B0aW9uPjxvcHRpb24gdmFsdWU9ImtvIiA+6Z+p5Zu9PC9vcHRpb24+PG9wdGlvbiB2YWx1ZT0ibGkiID7nq4vpmbblrps8L29wdGlvbj48b3B0aW9uIHZhbHVlPSJubyIgPuaMquWogTwvb3B0aW9uPjxvcHRpb24gdmFsdWU9InBvIiA+5rOi5YWw6K+tPC9vcHRpb24+PG9wdGlvbiB2YWx1ZT0icHIiID7okaHokITniZnor63vvIjokaHokITniZnvvIk8L29wdGlvbj48b3B0aW9uIHZhbHVlPSJwdCIgPuiRoeiQhOeJmTwvb3B0aW9uPjxvcHRpb24gdmFsdWU9InJvIiA+572X6ams5bC85LqaPC9vcHRpb24+PG9wdGlvbiB2YWx1ZT0icnMiID7kv4Q8L29wdGlvbj48b3B0aW9uIHZhbHVlPSJzZSIgPueRnuWFuOivrTwvb3B0aW9uPjxvcHRpb24gdmFsdWU9InNsIiA+5pav5rSb5LyQ5YWLPC9vcHRpb24+PG9wdGlvbiB2YWx1ZT0ic3AiID7opb/nj63niZnor608L29wdGlvbj48b3B0aW9uIHZhbHVlPSJ0aCIgPuazsOWbvTwvb3B0aW9uPjxvcHRpb24gdmFsdWU9InR1IiA+5Zyf6ICz5YW2PC9vcHRpb24+ZAICD2QWCgIPDxYCHgVzdHlsZQULd2lkdGg6NDBweDtkAhEPFgIfAQULd2lkdGg6NDBweDtkAhIPFgIfAQULd2lkdGg6MzBweDtkAhMPFgIfAQULd2lkdGg6MzBweDtkAikPZBYCZg9kFgRmDw8WAh8ABQborqHnrpdkZAIBDw8WAh4HVmlzaWJsZWdkFgxmDw8WAh8ABQlCTUk6IDIxLjNkZAIBDw8WAh8ABQrmsqHmnIkgQk1EZGQCAg8PFgIfAAUDMy4yZGQCAw8PFgIfAAUDMC4zZGQCBA8WAh8CaGQCBg8WAh8CaGQCAw8WAh8ABaUJPGEgaHJlZj0iP2xhbmc9ZW4iID7oi7Hor608L2E+IHwgPGEgaHJlZj0iP2xhbmc9YXIiID7pmL/mi4nkvK/or608L2E+IHwgPGEgaHJlZj0iP2xhbmc9YmUiID7lrZ/liqDmi4k8L2E+IHwgPGEgaHJlZj0iP2xhbmc9Y2hzIiBjbGFzcz0nc2VsZWN0ZWRMYW5nJz7nroDljJYg5Lit5paHPC9hPiB8IDxhIGhyZWY9Ij9sYW5nPWNodCIgPuS8oOe7nyDkuK3mloc8L2E+IHwgPGEgaHJlZj0iP2xhbmc9Y3IiID7lhYvnvZflnLDkupo8L2E+IHwgPGEgaHJlZj0iP2xhbmc9Y3oiID7mjbflhYs8L2E+IHwgPGEgaHJlZj0iP2xhbmc9ZGEiID7kuLnpuqbnmoQ8L2E+IHwgPGEgaHJlZj0iP2xhbmc9ZGUiID7lvrfor608L2E+IHwgPGEgaHJlZj0iP2xhbmc9ZHUiID7ojbflhbDkuro8L2E+IHwgPGEgaHJlZj0iP2xhbmc9ZXMiID7niLHmspnlsLzkupror608L2E+IHwgPGEgaHJlZj0iP2xhbmc9ZmEiID7ms6Lmlq/or608L2E+IHwgPGEgaHJlZj0iP2xhbmc9ZmkiID7oiqzlhbA8L2E+IHwgPGEgaHJlZj0iP2xhbmc9ZnIiID7ms5Xor608L2E+IHwgPGEgaHJlZj0iP2xhbmc9Z3IiID7luIzohYror608L2E+IHwgPGEgaHJlZj0iP2xhbmc9aWMiID7lhrDlsps8L2E+IHwgPGEgaHJlZj0iP2xhbmc9aW4iID7ljbDluqblsLzopb/kupo8L2E+IHwgPGEgaHJlZj0iP2xhbmc9aXQiID7mhI/lpKfliKk8L2E+IHwgPGEgaHJlZj0iP2xhbmc9anAiID7ml6Xor608L2E+IHwgPGEgaHJlZj0iP2xhbmc9a28iID7pn6nlm708L2E+IHwgPGEgaHJlZj0iP2xhbmc9bGkiID7nq4vpmbblrps8L2E+IHwgPGEgaHJlZj0iP2xhbmc9bm8iID7mjKrlqIE8L2E+IHwgPGEgaHJlZj0iP2xhbmc9cG8iID7ms6LlhbDor608L2E+IHwgPGEgaHJlZj0iP2xhbmc9cHIiID7okaHokITniZnor63vvIjokaHokITniZnvvIk8L2E+IHwgPGEgaHJlZj0iP2xhbmc9cHQiID7okaHokITniZk8L2E+IHwgPGEgaHJlZj0iP2xhbmc9cm8iID7nvZfpqazlsLzkupo8L2E+IHwgPGEgaHJlZj0iP2xhbmc9cnMiID7kv4Q8L2E+IHwgPGEgaHJlZj0iP2xhbmc9c2UiID7nkZ7lhbjor608L2E+IHwgPGEgaHJlZj0iP2xhbmc9c2wiID7mlq/mtJvkvJDlhYs8L2E+IHwgPGEgaHJlZj0iP2xhbmc9c3AiID7opb/nj63niZnor608L2E+IHwgPGEgaHJlZj0iP2xhbmc9dGgiID7ms7Dlm708L2E+IHwgPGEgaHJlZj0iP2xhbmc9dHUiID7lnJ/ogLPlhbY8L2E+IGQYAQUeX19Db250cm9sc1JlcXVpcmVQb3N0QmFja0tleV9fFhAFHmN0bDAwJENvbnRlbnRQbGFjZUhvbGRlcjEkc2V4MQUeY3RsMDAkQ29udGVudFBsYWNlSG9sZGVyMSRzZXgyBStjdGwwMCRDb250ZW50UGxhY2VIb2xkZXIxJHByZXZpb3VzZnJhY3R1cmUxBStjdGwwMCRDb250ZW50UGxhY2VIb2xkZXIxJHByZXZpb3VzZnJhY3R1cmUyBSdjdGwwMCRDb250ZW50UGxhY2VIb2xkZXIxJHBmcmFjdHVyZWhpcDEFJ2N0bDAwJENvbnRlbnRQbGFjZUhvbGRlcjEkcGZyYWN0dXJlaGlwMgUoY3RsMDAkQ29udGVudFBsYWNlSG9sZGVyMSRjdXJyZW50c21va2VyMQUoY3RsMDAkQ29udGVudFBsYWNlSG9sZGVyMSRjdXJyZW50c21va2VyMgUqY3RsMDAkQ29udGVudFBsYWNlSG9sZGVyMSRnbHVjb2NvcnRpY29pZHMxBSpjdGwwMCRDb250ZW50UGxhY2VIb2xkZXIxJGdsdWNvY29ydGljb2lkczIFJGN0bDAwJENvbnRlbnRQbGFjZUhvbGRlcjEkYXJ0aHJpdGlzMQUkY3RsMDAkQ29udGVudFBsYWNlSG9sZGVyMSRhcnRocml0aXMyBSdjdGwwMCRDb250ZW50UGxhY2VIb2xkZXIxJG9zdGVvcG9yb3NpczEFJ2N0bDAwJENvbnRlbnRQbGFjZUhvbGRlcjEkb3N0ZW9wb3Jvc2lzMgUiY3RsMDAkQ29udGVudFBsYWNlSG9sZGVyMSRhbGNvaG9sMQUiY3RsMDAkQ29udGVudFBsYWNlSG9sZGVyMSRhbGNvaG9sMmdYq7q7xepMH1ArO+HZ63Ti5aaCSkpKCqFijCofbyNh',
                '__VIEWSTATEGENERATOR' => 'E5DF20DC',
                '__EVENTVALIDATION' => '/wEWKwLaqqtdApez+YEIAqCr1OMNAoqBw4AMAvDsxJkEAqjh0vQEAurn2+4MAsW3sN0IApbDyMkGArPtz5wMAtSo38gCAty8su8PAvzehKYNAtSo26cBAon1lGoC4dLkxA8C/b20wQsC75WLpwUC9/3fsQsC9Oa3uQECoKbkiQoCwbqcuA4Czd3R4AYCireL+QYCs7rmjwoC8MTpggMCkuS+4QUC5YrFqgYCpfj16QUChIuT3gcCxPmjnQQC4PCp8A8CoIKZswwCy9LE5gwCi6D0pQ8CzpOf/wwCjuGvvA8Cj6DRiwMCz9LhSALOseDFDwKOw9CGDAKesaK/AgLq7cf7CkdrRwapG3rsTTxNMn0MhxZcbOeYOCubQNFTTmywIo++',
                '__ASYNCPOST' => 'true',
                'ctl00$ContentPlaceHolder1$btnCalculate' => urlencode('计算')

        // 'password' => 'password'
        );
//var_dump($data);

$post_fields = '';
$header[0]='Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
$header[1]='Content-Type:application/x-www-form-urlencoded; charset=utf-8';
//$header[2]='Cookie:lang=chs; ASP.NET_SessionId=qgevqagtitgvh5e0t2mognbn; _ga=GA1.3.1446649157.1455765782';
$header[3]='Host:www.shef.ac.uk';
$header[4]='Referer:http://www.shef.ac.uk/FRAX/tool.aspx?lang=chs';
$header[5]='X-Requested-With:XMLHttpRequest';
$header[6]='User-Agent:Mozilla/5.0 (Windows NT 6.3; rv:36.0) Gecko/20100101 Firefox/36.04';
$header[7]='X-MicrosoftAjax:Delta=true';
$header[8]='Accept-Language:zh-Hans-CN,zh-Hans;q=0.8,en-US;q=0.5,en;q=0.3';
$header[9] = 'Accept-Encoding:gzip, deflate';
// $header[0] = 'Accept-Language:zh-Hans-CN,zh-Hans;q=0.8,en-US;q=0.5,en;q=0.3';
// $header[1] = 'Content-Type:application/x-www-form-urlencoded; charset=utf-8';
// $header[2] = 'X-MicrosoftAjax:Delta=true';
// $header[3] = 'X-Requested-With:XMLHttpRequest';
// $header[4] = 'Cookie:lang=chs; ASP.NET_SessionId=2ltjdltq00xigfmsrqr1ld55; _ga=GA1.3.1446649157.1455765782';
// $header[5] = 'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
// $header[6] = 'Host:www.shef.ac.uk';
// $header[7] = 'Referer:http://www.shef.ac.uk/FRAX/tool.aspx?lang=chs';
// $header[8] = 'User-Agent:Mozilla/5.0 (Windows NT 6.3; rv:36.0) Gecko/20100101 Firefox/36.04';
// $header[9] = 'Accept-Encoding:gzip, deflate';


        $ch = curl_init ();
        // print_r($ch);
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_POST, 1 );
        curl_setopt ( $ch, CURLOPT_HEADER, 0 );
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);




        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($ch, CURLOPT_ENCODING ,'gzip');
        curl_setopt ( $ch, CURLOPT_POSTFIELDS,  http_build_query($data) );

        curl_setopt($ch, CURLOPT_TIMEOUT, 20);//定义超时时间


     //不获取body信息
        curl_setopt($ch, CURLOPT_NOBODY, 0);
        $return = curl_exec ( $ch );
        $errorCode = curl_errno($ch);
        //var_dump($errorCode);
        curl_close ( $ch );

        if(0 !== $errorCode) {
            var_dump($errorCode);
            return false;
        }


        //echo $return ;exit;
        var_dump($return);


        //file_put_contents('caiji/1111.txt',$return);


        preg_match_all('/class="bmi">BMI:(.*?)</',$return, $out, PREG_SET_ORDER);
        //VAR_DUMP($out);

        $result = $out[0][1];

        //return $result;

        echo $result;
        exit;
// if($v['id']==10)exit;
                $aa['result1']=$result;
        $aa['date']=date("Y-m-d H:i:s");
        $m->where('id="%d"',$v['id'])->save($aa);
        sleep(10);

     }//循环结束


}

    function changeStr($str){
        if($str == 1){
            $data[0]='0';
            $data[1]='no';
        }else{
            $data[0]='1';
            $data[1]='yes';
        }
        return $data;
    }



    public function caiji_single(){
        //http://gbjc.bnup.com/网站采集

        set_time_limit(0);
        vendor('fileDirUtil');
        $fileDir = new \fileDirUtil();

        $url='http://gbjc.bnup.com/eduresource.php?action=showcatalog&subjectid=2246&id=2282';
        $contents=pageCollect($url);

        $filepath='caiji/yuanma/b.txt';
        //echo 'ab';
        //var_dump($contents);exit();
        preg_match_all("/<tr><td><li><a href=\"(.*?)\" target=\"_blank\">/",$contents, $out, PREG_SET_ORDER);
       // var_dump($out);
       // echo $out[0][1];exit();

        foreach ($out as $key => $value) {
            $url_tmp='http://gbjc.bnup.com/'.$value[1];
            //echo $url_tmp.'<br>';
            $contents_tmp=pageCollect($url_tmp);

             preg_match_all("/href=\"\/data\/upload\/(.*?)\"><img/",$contents_tmp, $out_tmp, PREG_SET_ORDER);

            //var_dump($out_tmp);exit();
             foreach ($out_tmp as $k => $v) {
                 $pic='http://gbjc.bnup.com/data/upload/'.$v[1];

                 $str_tmp=$fileDir->readsFile($filepath);
                // echo $pic;
                //echo strpos($str_tmp,$pic);
               // $pic='http://gbjc.bnup.com/data/upload/month_201103/czyw7x020_bDgGq4.jpg';
               // echo strpos($str_tmp,$pic);
                if(strpos($str_tmp,$pic)==''){
                    //echo 'cc';
                    $fileDir->writeFile($filepath,$pic);
                }else{
                   // echo 'dd';
                }
               // exit();
                //  $tmparr=explode($str_tmp, $pic);

                //  if(count($tmparr)>1){
                //     echo $pic.'重复<br>';
                //  }else{
                //      $fileDir->writeFile($filepath,$pic);
                // }
             }

        }

    }

    public function xiazai(){
        //http://gbjc.bnup.com/网站采集下载
        set_time_limit(0);
        vendor('fileDirUtil');
        $fileDir = new \fileDirUtil();
        $urlArr=$fileDir->readFile2array('caiji/yuanma/b.txt');
        foreach ($urlArr as $key=>$v){
           // echo $v;
            preg_match_all("/czyw7x(.*?)_/",$v,$out,PREG_SET_ORDER);
            echo $out[0][1].'<br>';
            $filename=pathinfo($v,PATHINFO_EXTENSION);
            //echo $filename;
            $filename='caiji/yuanma/'.$out[0][1].'.'.$filename;


            $img = file_get_contents($v);

            file_put_contents($filename, $img);
        }
    }


    /**
     *
     *采集样本
     **/
    public function caiji(){
    	$url='http://gbjc.bnup.com/eduresource.php?action=showcatalog&id=2254&subjectid=2246';





    	vendor('fileDirUtil');
    	$fileDir = new \fileDirUtil();
    	$urlArr=$fileDir->readFile2array('caiji/yuanma/b.txt');

    	foreach ($urlArr as $key=>$v){
    		$url=$v;
    		if(check_remote_file_exists($url)){
    			$contents=pageCollect($url);

    			preg_match_all("/border=0 hspace=0 alt=\"\" src=\"(.*?)\" onload=resizepic\(this\)/",$contents, $out, PREG_SET_ORDER);

    			$out=str_replace('../../..', 'http://www.ditu.com.cn', $out[0][1]);

    			//$filename=pathinfo($out,PATHINFO_BASENAME);
    			//$filename='caiji/yuanma/'.$filename;
    			$filename=pathinfo($out,PATHINFO_EXTENSION);
    			$filename='caiji/yuanma/'.($key+1).'.'.$filename;
    			//getImage($url,$filename);

    			$img = file_get_contents($out);
    			file_put_contents($filename, $img);
    			echo $filename.'下载成功！<br>';
    		}else {
    			echo $url;
    			echo '不存在<br>';
    		}



    	}



    //	$fileDir->writeFile($filepath, $contents);
    }



    public function caiji123(){

        set_time_limit(0);
        $url='http://www.weijiangtai.com/pxs/resourse/tool/index.htm';


        vendor('fileDirUtil');
        $fileDir = new \fileDirUtil();

        $filepath = 'genre.txt';
        $contents=pageCollect($url);

        var_dump($contents);exit();
        preg_match_all("/border=0 hspace=0 alt=\"\" src=\"(.*?)\" onload=resizepic\(this\)/",$contents, $out, PREG_SET_ORDER);



        foreach ($urlArr as $key=>$v){
            $url=$v;
            if(check_remote_file_exists($url)){
                $contents=pageCollect($url);

                preg_match_all("/border=0 hspace=0 alt=\"\" src=\"(.*?)\" onload=resizepic\(this\)/",$contents, $out, PREG_SET_ORDER);

                $out=str_replace('../../..', 'http://www.ditu.com.cn', $out[0][1]);

                //$filename=pathinfo($out,PATHINFO_BASENAME);
                //$filename='caiji/yuanma/'.$filename;
                $filename=pathinfo($out,PATHINFO_EXTENSION);
                $filename='caiji/yuanma/'.($key+1).'.'.$filename;
                //getImage($url,$filename);

                $img = file_get_contents($out);
                file_put_contents($filename, $img);
                echo $filename.'下载成功！<br>';
            }else {
                echo $url;
                echo '不存在<br>';
            }



        }



    //  $fileDir->writeFile($filepath, $contents);
    }

//解析dom

public function getContentFromDom(){
    $url="http://www.weijiangtai.com/pxs/resourse/tool/index.htm";
    $htmlDoc = new \DOMDocument;
    $htmlDoc->loadHTMLFile($url);
    $htmlDoc->normalizeDocument();//格式化为标准的dom

    //根据id解析查找dom元素
    $tables_list = $htmlDoc->getElementById('menuPanel');

    //查找当前dom元素的下一级的子元素
    $lilist= $tables_list->childNodes;

    //查找当前dom元素的下一级的子元素的个数(length/2才是真实的个数)
    $li = $tables_list->childNodes->length;

    $str='';


    echo $lilist->item(2)->getElementsByTagName('ul')->item(0)->childNodes->item(0)->getElementsByTagName('div')->item(0)->getElementsByTagName('span')->item(1)->nodeValue;exit();

    //因为长度是实际的2倍，所以每次加2,因为是从第2个元素开始,所以$i=2
    for($i=2;$i<$li;$i+=2){
       // echo $lilist->item($i)->getElementsByTagName('div')->item(0)->getElementsByTagName('span')->item(1)->nodeValue;

        $childlilength= $lilist->item($i)->getElementsByTagName('ul')->item(0)->childNodes->length;

        for($j=0;$j<$childlilength;$j+=2){
            $str.= $lilist->item($i)->getElementsByTagName('div')->item(0)->getElementsByTagName('span')->item(1)->nodeValue;
            $str.= ' ';
            $str.= $lilist->item($i)->getElementsByTagName('ul')->item(0)->childNodes->item($j)->getElementsByTagName('div')->item(0)->getElementsByTagName('span')->item(1)->nodeValue;

            $ddd=$lilist->item(2)->getElementsByTagName('ul')->item(0)->childNodes->item(0)->getElementsByTagName('div')->item(0)->getAttribute('onclick');

            $tmparr=explode("'", $ddd);
            $str.= ' ';
            $str.= $tmparr[3];
            $str.= "\r\n";
        }
    }
echo $str;

$txt='caiji/hahaha.txt';

$fp=fopen($txt, 'wb');
fwrite($fp, $str);
fclose($fp);




}

    /**
     * [DiscuzPost description]
     * [Discuz论坛post提交文章]
     */
    public function DiscuzPost(){
                $discuz_url = 'http://localhost/Discuz_X2/upload/';//论坛地址
                $login_url = $discuz_url .'member.php?mod=logging&action=login';//登录页地址

                $post_fields = array();
                //以下两项不需要修改
                $post_fields['loginfield'] = '用户名';
                $post_fields['loginsubmit'] = 'true';
                //用户名和密码，必须填写
                $post_fields['username'] ='justzb';
               // $post_fields['username'] = $users[array_rand($users,1)];
                $post_fields['password'] = '123456';
                //安全提问
                $post_fields['questionid'] = 0;
                $post_fields['answer'] = '';
                //@todo验证码
                $post_fields['seccodeverify'] = '';

                //获取表单FORMHASH
                $ch = curl_init($login_url);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $contents = curl_exec($ch);
                curl_close($ch);
                preg_match('/<input\s*type="hidden"\s*name="formhash"\s*value="(.*?)"\s*\/>/i', $contents, $matches);
                if(!empty($matches)) {
                        $formhash = $matches[1];
                } else {
                        die('Not found the forumhash1.');
                }



                //POST数据，获取COOKIE,cookie文件放在网站的temp目录下
                $cookie_file = tempnam('/tmp','cookie');

                $ch = curl_init($login_url);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
                curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
                curl_exec($ch);
                curl_close($ch);

                //取到了关键的cookie文件就可以带着cookie文件去模拟发帖,fid为论坛的栏目ID
                $send_url = $discuz_url."forum.php?mod=post&action=newthread&fid=2";


                $ch = curl_init($send_url);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
                $contents = curl_exec($ch);
                curl_close($ch);

                //这里的hash码和登陆窗口的hash码的正则不太一样，这里的hidden多了一个id属性
                preg_match('/<input\s*type="hidden"\s*name="formhash"\s*id="formhash"\s*value="(.*?)"\s*\/>/i', $contents, $matches);
                if(!empty($matches)) {
                        $formhash = $matches[1];
                } else {
                        die('Not found the forumhash.');
                }


                $post_data = array();
                //帖子标题
                $post_data['subject'] = 'ddd';
               // $post_data['subject'] = $val['title'];
                //帖子内容
                $post_data['message'] = 'eeee发大水发烧发烧发大水发烧发烧发大水法发大水法';
               // $post_data['message'] = $val['content'];
                $post_data['topicsubmit'] = "yes";
                $post_data['extra'] = '';
                //帖子标签
                $post_data['tags'] = '';
               // $post_data['tags'] = $post_data['subject'];
                //帖子的hash码，这个非常关键！假如缺少这个hash码，discuz会警告你来路的页面不正确
                $post_data['formhash']=$formhash;


                $ch = curl_init($send_url);
                curl_setopt($ch, CURLOPT_REFERER, $send_url);       //伪装REFERER
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
                curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                $contents = curl_exec($ch);
                curl_close($ch);

                //清理cookie文件
                unlink($cookie_file);
    }



//登录采集
function logcur(){

$cookie_file    =    tempnam('./caiji','cookie');
$login_url        =    'http://bbs.php100.com/login.php';
$post_fields    =    'cktime=31536000&step=2&pwuser=justzb&pwpwd=370b987182';


//header的相关信息
// $header[0]='Host:www.imooc.com';
// $header[1]='Referer:http://www.imooc.com/';
// $header[2]='X-Requested-With:XMLHttpRequest';

$ch = curl_init($login_url);
curl_setopt($ch, CURLOPT_HEADER, 0);
//curl_setopt($ch, CURLOPT_HTTPHEADER, $header);//加入header信息，如果有需要的话
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
curl_exec($ch);
curl_close($ch);


$url='http://bbs.php100.com/userpay.php';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
$contents = curl_exec($ch);

curl_close($ch);

echo $contents;
 preg_match_all("/style=\"line-height:1.1;font-family:Georgia;\">(.*?)<\/span>/",$contents, $out, PREG_SET_ORDER);

var_dump($out);
}


//POST请求标准例子
//带验证码的登录
function loginVerify(){
    //初始化变量
    $cookie_file = "tmp.cookie";
    $login_url = "http://xxx.com/logon.php";
    $verify_code_url = "http://xxx.com/verifyCode.php";

    echo "正在获取COOKIE...\n";
    $curlj = curl_init();
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






function logcur3(){

$cookie_file = tempnam('./caiji','cookie');
//$login_url = 'http://www.imooc.com/user/login';
$login_url = 'http://www.imooc.com/passport/user/login';
$post_fields = 'username=justzb@126.com&password=370b987182&remember=1&referer=http://www.imooc.com';

// $header[0]='Host:www.imooc.com';
// $header[1]='Referer:http://www.imooc.com/';
// $header[2]='X-Requested-With:XMLHttpRequest';
// $header[3]='User-Agent:Mozilla/5.0 (Windows NT 6.3; WOW64; rv:38.0) Gecko/20100101 Firefox/38.0';
// $header[4]='cookie:imooc_uuid=22a6ac86-ed23-4888-af81-6d600332bfb5; imooc_isnew=1; imooc_isnew_ct=1453270463; cvde=569f25bf0bd34-3; Hm_lvt_f0cfcccd7b1393990c78efdeebff3968=1453270441; Hm_lpvt_f0cfcccd7b1393990c78efdeebff3968=1453270988; IMCDNS=0; PHPSESSID=5v1j41lr41nr69jhchmop589t6';


//获取cookie
$ch = curl_init($login_url);
curl_setopt($ch, CURLOPT_HEADER, 0);
//curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
//curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
$re=curl_exec($ch);
curl_close($ch);

$re=json_decode($re,true);
//var_dump($re);
//echo $re['data']['url'][0];
//exit();
// $header2[0]='Referer:http://www.imooc.com/';
// $header2[1]='X-Requested-With:XMLHttpRequest';
// $header2[2]='User-Agent:Mozilla/5.0 (Windows NT 6.3; WOW64; rv:38.0) Gecko/20100101 Firefox/38.0';
// $header2[3]='cookie:imooc_uuid=45aac8ef-88cb-452a-82fc-3123685ecd02; imooc_isnew=1; imooc_isnew_ct=1453207607; Hm_lvt_f0cfcccd7b1393990c78efdeebff3968
// =1453207605,1453218022,1453251364; IMCDNS=0; last_login_username=justzb%40126.com; cvde=569edb2680888-12
// ; Hm_lpvt_f0cfcccd7b1393990c78efdeebff3968=1453290931; PHPSESSID=a54ccp4ffik1o0fv8ocqljruv2';

$ua=$re['data']['url'][0]."&callback=131&_=131311";
$ch = curl_init($ua);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, 0);
//curl_setopt($ch, CURLOPT_HTTPHEADER, $header2);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
//$referer_check_1 = 'http://www.imooc.com/';
//curl_setopt($ch, CURLOPT_REFERER, $login_url);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
$result_check_1 = curl_exec($ch);
curl_close($ch);
//echo '<br>';
//echo $result_check_1;

//var_dump($result_check_1);exit();



// $ch = curl_init($re['data']['url'][1]);
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// curl_setopt($ch, CURLOPT_HEADER, true);
// curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
// curl_setopt($ch, CURLOPT_POST, true);
// $referer_check_1 = 'http://www.imooc.com/';
// curl_setopt($ch, CURLOPT_REFERER, $referer_check_1);
// curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
// curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
// curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
// $result_check_2 = curl_exec($ch);
// curl_close($ch);

// $cookie_file1 = tempnam('./caiji','cookie1');

// $ch = curl_init($re['data']['url'][0]);
// curl_setopt($ch, CURLOPT_HEADER, 0);
// //curl_setopt($ch, CURLOPT_HTTPHEADER, $header2);
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// //curl_setopt($ch, CURLOPT_POST, 1);
// //curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
// curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file1);
// $re2=curl_exec($ch);
// curl_close($ch);




// exit();

// //验证码处理
// $verifyurl='http://www.imooc.com/passport/user/verifycode';
// $curl = curl_init();
// curl_setopt($curl, CURLOPT_URL, $verifyurl);
// curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie_file);
// curl_setopt($curl, CURLOPT_HEADER, 0);
// curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
// $img = curl_exec($curl);
// curl_close($curl);

// //保存到本地
// $fp = fopen("caiji/verifyCode.jpg","w");
// fwrite($fp,$img);
// fclose($fp);

// sleep(20);


//采集需要登录页面的信息
$url='http://www.imooc.com/user/setuserinfo';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
$contents = curl_exec($ch);
curl_close($ch);


echo $contents;

}
function getMillisecond() {
    list($t1, $t2) = explode(' ', microtime());
    return $t2  .  ceil( ($t1 * 1000) );
}



function logcur2(){

$cookie_file    =    tempnam('./caiji','cookie');
//$login_url        =    'http://www.imooc.com/user/login';
//$login_url        =    'http://reg.163.com/login.jsp?type=1&product=mail126&url=http://entry.mail.126.com/cgi/ntesdoor?hid%3D10010102%26lightweight%3D1%26language%3D0%26style%3D-1';

$login_url='https://mail.126.com/entry/cgi/ntesdoor?df=mail126_letter&from=web&funcid=loginone&iframe=1&language=-1&passtype=1&product=mail126&verifycookie=-1&net=failed&style=-1&race=-2_-2_-2_db&uid=justzb@126.com&hid=10010102';
//$login_url        =    'https://mail.126.com/entry/cgi/ntesdoor?df=mail126_letter&from=web&funcid=loginone&iframe=1&language=-1&passtype=1&product=mail126&verifycookie=-1&net=failed&style=-1&race=-2_-2_-2_db&uid=justzb@126.com&hid=10010102';
//$login_url        =    'http://www.imooc.com/passport/user/login';
//$post_fields    =    'username=justzb@126.com&password=370b987182&remember=1';
$post_fields    =    'username=justzb@126.com&password=763958048!@#456&savelogin=0&url2=http://mail.126.com/errorpage/error126.htm';


$ch = curl_init($login_url);
 date_default_timezone_get('PRC');
curl_setopt($ch, CURLOPT_HEADER, 0);
//curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
curl_setopt($ch,CURLOPT_COOKIE,session_name().'='.session_id());
curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
curl_exec($ch);

//检查是否有错误
if(curl_errno($curl)) {
    exit('Curl error: ' . curl_error($curl));
}

curl_close($ch);



echo 'aaa';
exit();
$url='http://www.imooc.com/user/setuserinfo';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
$contents = curl_exec($ch);

curl_close($ch);

echo $contents;exit();
 preg_match_all("/<img src=\"(.*?)\" alt=\"头像\"/",$contents, $out, PREG_SET_ORDER);

var_dump($out);
}


function wangyi(){
 $url='http://mail.126.com/entry/cgi/ntesdoor?df=mail126_letter&from=web&funcid=loginone&iframe=1&language=-1&passtype=1&product=mail126&verifycookie=-1&net=failed&style=-1&race=-2_-2_-2_db&uid=justzb@126.com&hid=10010102';
//$url='http://reg.163.com/login.jsp?type=1&product=mail126&url=http://entry.mail.126.com/cgi/ntesdoor?hid%3D10010102%26lightweight%3D1%26language%3D0%26style%3D-1';

$cookie_file    =    tempnam('./caiji','cookie');

$post_fields='username=justzb@126.com&savelogin=0&url2=http://mail.126.com/errorpage/error126.htm&password=763958048!@#456&password=';

$header[0]='Cookie:starttime=1453296673642; logType=; nts_mail_user=justzb:-1:1; df=mail126_letter; JSESSIONID=972CA1172818780C35893FE691720A65
; SID=0ef6915d-cee6-4379-9b2a-8fc54f56e94d; P_INFO=justzb@126.com|1453269878|0|mail126|11&19|hen&1453269777
&mail126#hen&410100#10#0#0|138120&0|mail126|justzb@126.com; S_INFO=1453269878|0|#3&100#|justzb@126.com
; NTES_SESS=9RcraFnLbVVgRJ1b5MUrdHTtdqPO3FDuLAxRkPsal.Ya8Ftg8d_0OTBWLe0EjVK7EJcvhVsUJxLQ4zSntLI5FtYh
ELl4AwP9nGBSXRP5N77DjZldKnGFkgKfHFLdvY4wOSFpbrpEEQDXPog1FsSRl1uuukpDDReDTAmWlHnHtbmc3bHcNG1B6P0seYNWDWBcFjzLi0Mt
.QcyT; mail_upx=c7bj.mail.126.com|c1bj.mail.126.com|c2bj.mail.126.com|c3bj.mail.126.com|c4bj.mail.126
.com|c5bj.mail.126.com|c6bj.mail.126.com; mail_upx_nf=; mail_idc=""; Coremail=1453269878367%RBlpfaAAqwNVdyUwSaAAToUElhRVMlPa
%g1a60.mail.126.com; MAIL_MISC="justzb@126.com"; cm_last_info="dT1qdXN0emIlNDAxMjYuY29tJmQ9aHR0cCUzQ
SUyRiUyRm1haWwuMTI2LmNvbSUyRmpzNiUyRm1haW4uanNwJTNGc2lkJTNEUkJscGZhQUFxd05WZHlVd1NhQUFUb1VFbGhSVk1sU
GEmcz1SQmxwZmFBQXF3TlZkeVV3U2FBQVRvVUVsaFJWTWxQYSZoPWh0dHAlM0ElMkYlMkZtYWlsLjEyNi5jb20lMkZqczYlMkZtY
WluLmpzcCUzRnNpZCUzRFJCbHBmYUFBcXdOVmR5VXdTYUFBVG9VRWxoUlZNbFBhJnc9bWFpbC4xMjYuY29tJmw9LTEmdD0tMSZuPWZhaWxlZA
=="; MAIL_SESS=9RcraFnLbVVgRJ1b5MUrdHTtdqPO3FDuLAxRkPsal.Ya8Ftg8d_0OTBWLe0EjVK7EJcvhVsUJxLQ4zSntLI5F
tYhELl4AwP9nGBSXRP5N77DjZldKnGFkgKfHFLdvY4wOSFpbrpEEQDXPog1FsSRl1uuukpDDReDTAmWlHnHtbmc3bHcNG1B6P0seYNWDWBcFjzLi0Mt
.QcyT; MAIL_SINFO="1453269878|0|#3&100#|justzb@126.com"; MAIL_PINFO="justzb@126.com|1453269878|0|mail126
|11&19|hen&1453269777&mail126#hen&410100#10#0#0|138120&0|mail126|justzb@126.com"; secu_info=1; mail_entry_sess
=02e90c39aba11efa0eb40f56fd2c3e5ae5887955c18d4b1d4f2e77aca901c87fc2ab02a14f6322072ba91c5e33cc07f9808
a20da6b15346b82ce2da8e27800ec02ff06a2556c82abd9382544558ec5a1e5da18d462b8da59a22c68295b19180da0e846c
11c2c10443373610735bcdfa6bb8d76a889276f570cdd7aef70afec16dd2841528d19c209015f69f02ee399633e81482fbe2a1759a5705df18007f69aaeb7bb9f126f253fb236526e87fdc03595e1c2dfb7b15dc9e59bd27909025719
; locale=""; Coremail.sid=RBlpfaAAqwNVdyUwSaAAToUElhRVMlPa; mail_style=js6; mail_uid=justzb@126.com;
 mail_host=mail.126.com; MailMasterPopupTips=1453269876636';
 $header[1]='Host:mail.126.com';
 $header[2]='Referer:http://mail.126.com/';
 $header[3]='User-Agent:Mozilla/5.0 (Windows NT 10.0; WOW64; rv:43.0) Gecko/20100101 Firefox/43.0';








   $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);

    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//SSL证书认证
   // curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);//严格认证
    //curl_setopt($curl, CURLOPT_CAINFO,$cacert_url);//证书地址


curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
$re=curl_exec($ch);

//检查是否有错误
if(curl_errno($ch)) {
    exit('Curl error: ' . curl_error($ch));
}

curl_close($ch);


echo $re;

var_dump($re);exit();









    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);


    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);

    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);

    $re=curl_exec($ch);
    curl_close($ch);
    var_dump($re);
}



    public function lbaidu(){
        $url='http://www.baidu.com';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
            //是否获取头信息
        curl_setopt($ch, CURLOPT_HEADER, 0);
        //获取body信息
        curl_setopt($ch, CURLOPT_NOBODY, 0);
        //多方网页使用了gzip压缩，那么获取的内容将有可能为乱码,加入gzip解析,就不乱码了
        //curl_setopt($ch, CURLOPT_ENCODING ,'gzip');

        $contents = curl_exec($ch);

        //检查是否有错误
        if(curl_errno($curl)) {
            //exit('Curl error: ' . curl_error($curl));
        }

        curl_close($ch);

        //file_put_contents('caiji/1111.txt',$contents);
        //echo  $contents;
    }

    /**
     * [91助手apk信息采集]
     * @return [type] [description]
     */

    public function apk91(){
        set_time_limit(0);
        vendor("simple_html_dom");


        for($ii=1;$ii<=3;$ii++){
            $url = "http://apk.91.com/soft/27_".$ii."_5";
            //echo $url;exit;
            // 新建一个Dom实例
            $html = new \simple_html_dom();
            // 从url中加载
            $html->load_file($url);

            $re = $html->find('div.topic_before');

            //查找结果为数组,数组中嵌套对象
            //$html->clear();


            for($i=0;$i<count($re);$i++){
                $res = $re[$i]->children;
                $aa = $res[0]->attr;
                //echo $aa['href'].'<br>';

                $urlInfo = 'http://apk.91.com'.$aa['href'];
                //echo $urlInfo;continue;//详情页面地址

                $this->tes($urlInfo);
                sleep(10);
            }//第一个应用信息结束

        }//第一页结束

    }//程序结束

    //91apk详情页面
    public function tes($url){
        //$url = "http://apk.91.com/Soft/Android/com.canxing.ringphone-1.html";
        vendor("simple_html_dom");
        // 新建一个Dom实例
        $html = new \simple_html_dom();

        // 从url中加载
        $html->load_file($url);

        $re = $html->find('div.s_intro_txt');
        $aa = $re[0]->children;
        $name = $aa[0]->find('h1',0)->innertext;

        //var_dump($aa);exit;
        $lis = $aa[2]->find('li');

        $banben = $lis[0]->innertext;
        $down = $lis[1]->innertext;
        $filesize = $lis[2]->innertext;
        $gujian = $lis[3]->innertext;
        $fenxiangriqi = $lis[4]->innertext;
        $fenxiangzhe = $lis[5]->innertext;
        $phone = $lis[6]->innertext;
        $email = $lis[7]->innertext;
        $kaifashang = $lis[8]->innertext;
        $biaoqian = $lis[9]->innertext;

       echo str_replace(' ','',$banben).'|'.str_replace(' ','',$down).'|'.str_replace(' ','',$filesize).'|'.str_replace(' ','',$gujian).'|'.$fenxiangriqi.'|'. strip_tags(str_replace(' ','',$fenxiangzhe)).'|'.$phone.'|'.strip_tags(str_replace(' ','',$email)).'|'.$kaifashang.'|'.strip_tags(str_replace(' ','',$biaoqian)).'<br>';
        //exit;
       //$html->clear();
    }


    public function zcfgSh(){
        set_time_limit(0);
        $url = "http://www.ciac.sh.cn/zcfg_fl.aspx?lb=0301";
        vendor("simple_html_dom");
        $html = new \simple_html_dom();

        // 从url中加载
        $html->load_file($url);

        $re = $html->find('table.Listbody');
        $aa = $re[0]->children;
        $td = $aa[0]->children;//td
        $a = $td[0]->children;//a
        $title = $a[0]->innertext;
        $arr = $a[0]->attr;
        $href = $arr['onclick'];
        $href = str_replace(array("news('","');"),'',$href);
        echo $title.'<br>';
        echo $href;
        $file = file_get_contents('http://www.ciac.sh.cn/newsdata/'.$href);
        file_put_contents('caiji/1.pdf',$file);
        //var_dump($a[0]->attr);

    }

    /**
     * 汽车网文章采集
     * @return [type] [description]
     */
    public function qichewang(){
        set_time_limit(0);
        $index = 1;
        vendor('PHPExcel');
        $objExcel = new \PHPExcel();
        $objWriter = new \PHPExcel_Writer_Excel2007($objExcel);

        $objExcel->setActiveSheetIndex(0);
        $objActSheet = $objExcel->getActiveSheet();
        $objActSheet->setTitle('文章表');

        $objActSheet->setCellValue('A1','标题');
        $objActSheet->setCellValue('B1','来源');
        $objActSheet->setCellValue('C1','作者');
        $objActSheet->setCellValue('D1','浏览数');
        $objActSheet->setCellValue('E1','更新时间');
        $objActSheet->setCellValue('F1','正文无html');
        $objActSheet->setCellValue('G1','正文有html');
        $objActSheet->setCellValue('H1','附件');
        for($ii=1;$ii<=2;$ii++){
           //$url ='http://www.catarc.org.cn/News.aspx?id1=45&page=1';
           $url ='http://www.catarc.org.cn/News.aspx?id1=2&page='.$ii;

            vendor("simple_html_dom");
            $html = new \simple_html_dom();

            // 从url中加载
            $html->load_file($url);

            $re = $html->find('table#ctl00_ctl00_ContentPlaceHolder1_ContentPlaceHolder1_dlnews2');

            //echo 'sssss';exit;
            $aa = $re[0]->children;
            for($i=0;$i<count($aa);$i++){
                $index++;
                //echo $index.'<br>';
                $td = $aa[$i]->children;
                $a = $td[0]->find('a',0)->attr;
                $href = $a['href'];
                //$href = 'NewsDetails.aspx?ID=2651';
                //echo 'http://www.catarc.org.cn/'.$href.'<br>';
                $contents = file_get_contents('http://www.catarc.org.cn/'.$href);
                //标题
                preg_match_all('/<span id="ctl00_ctl00_ContentPlaceHolder1_ContentPlaceHolder1_lblNews_title"><b><font color="Red">(.*?)</',$contents, $out1, PREG_SET_ORDER);
                //来源
                preg_match_all('/<span id="ctl00_ctl00_ContentPlaceHolder1_ContentPlaceHolder1_lblNews_ComeFrom">(.*?)</',$contents, $out2, PREG_SET_ORDER);
                //作者
                preg_match_all('/<span id="ctl00_ctl00_ContentPlaceHolder1_ContentPlaceHolder1_lblNews_Author">(.*?)</',$contents, $out3, PREG_SET_ORDER);
                //浏览数
                preg_match_all('/<span id="ctl00_ctl00_ContentPlaceHolder1_ContentPlaceHolder1_lblNews_AllHits">(.*?)</',$contents, $out4, PREG_SET_ORDER);
                //更新时间
                preg_match_all('/<span id="ctl00_ctl00_ContentPlaceHolder1_ContentPlaceHolder1_lblNews_WriteTime">(.*?)</',$contents, $out5, PREG_SET_ORDER);
                //附件
                //preg_match_all('/附件：(.*?)<a href="(.*?)">(.*?)<\/a>/',$contents, $out7, PREG_SET_ORDER);
                preg_match_all('/<a href="\/Upload(.*?)"/',$contents, $out7, PREG_SET_ORDER);

                $str = '';
                for($j=0;$j<count($out7);$j++){
                    $fujian = 'http://www.catarc.org.cn/Upload'.$out7[$j][1];
                    //echo $fujian.'<br>';
                    //echo pathinfo($fujian,PATHINFO_BASENAME);exit;
                    $dir = str_replace('http://www.catarc.org.cn','caiji/qiche',$fujian);
                    $dir = str_replace(pathinfo($fujian,PATHINFO_BASENAME),'',$dir);
                    $dir = iconv('utf-8','gbk',$dir);
                    //echo $dir;exit;
                    $this->createDir($dir);
                    $filename = urldecode(pathinfo($fujian,PATHINFO_BASENAME));
                    $filename = iconv('utf-8','gbk',$filename);
                    httpdown($fujian,$dir.'/'.$filename);
                    $fujian = urldecode($fujian);
                    $str .= $fujian.'<br>';
                }


                //正文
                $contents = str_replace(array("\r\n", "\r", "\n"), "", $contents);

                preg_match_all('/<div class="contents">(.*?)<\/div>                              &nbsp;<\/td>/',$contents, $out6, PREG_SET_ORDER);
                $zhengwen = rtrim($out6[0][0],'&nbsp;</td>');
                //file_put_contents('caiji/aaa.txt',rtrim($out6[0][0],'&nbsp;</td>'));

                //var_dump($out1);exit;

                $title = $out1[0][1];
                $from = $out2[0][1];
                $author = $out3[0][1];
                $views = $out4[0][1];
                $uptime = $out5[0][1];

                echo $title.'|'.$from.'|'.$author.'|'.$views.'|'.$uptime.'|'.strip_tags($zhengwen).'<br>';
                //exit;



                $objActSheet->setCellValue('A'.$index,$title);
                $objActSheet->setCellValue('B'.$index,$from);
                $objActSheet->setCellValue('C'.$index,$author);
                $objActSheet->setCellValue('D'.$index,$views);
                $objActSheet->setCellValue('E'.$index,$uptime);
                $noHtml = preg_replace('/\s+/','',strip_tags($zhengwen));
                $noHtml = str_replace('&nbsp;',' ',$noHtml);
                $noHtml = urldecode($noHtml);
                $objActSheet->setCellValue('F'.$index,$noHtml);
                $objActSheet->setCellValue('G'.$index,$zhengwen);
                $objActSheet->setCellValue('H'.$index,$str);

                //exit;
            }//一页中的每一条结束
        }//循环每一页结束
        $filename = "caiji/page.xlsx";
        $objWriter->save($filename);


    }


    //创建文件夹
    public function createDir($aimUrl, $mode = 0777) {
        $aimUrl = str_replace('', '/', $aimUrl);
        $aimDir = '';
        $arr = explode('/', $aimUrl);
        foreach ($arr as $str) {
            $aimDir .= $str . '/';
            if (!file_exists($aimDir)) {
                mkdir($aimDir, $mode);
            }
        }
    }



    /**
     * 站长之家音效素材采集
     * @return [type] [description]
     */
    public function zzzj(){
        vendor("simple_html_dom");
        // 新建一个Dom实例
        $html = new \simple_html_dom();

        // 从url中加载
        $html->load_file('http://sc.chinaz.com/yinxiao/ZhanZhengYinXiao.html');

        $re = $html->find('div.yxfenlei');

        $ul = $re[0]->children;
        $li = $ul[0]->children;
        $href = $ul[0]->find('a');
        //echo count($href);
        for($i=0;$i<count($href);$i++){
            //var_dump($res);exit();
            $urlArr = $href[$i]->attr;
            $url = $urlArr['href'];
            $text = $href[$i]->innertext;
            //var_dump($urlArr);exit;
            file_put_contents('caiji/zzzjaudio.txt', $text.'|'.$url.PHP_EOL, FILE_APPEND);
        }
    }

    public function zzzjaudio(){
        set_time_limit(0);
        vendor('fileDirUtil');
        $fileDir = new \fileDirUtil();
        $textArr = $fileDir->readFile2array('caiji/zzzjaudio.txt');
        //var_dump($textArr);
        vendor("simple_html_dom");
        $html = new \simple_html_dom();

        foreach($textArr as $v){
            $tmpArr = explode('|',$v);
            $genreName = $tmpArr[0];
            $genreUrl = 'http://sc.chinaz.com'.$tmpArr[1];
            //echo $genreName.'|'.$genreUrl.'<br>';
            $genreNameToGbk = iconv('utf-8', 'gbk', $genreName);
            $fileDir->createDir('caiji/zzzj/'.$genreNameToGbk);
            //echo $genreUrl;
            $html->load_file($genreUrl);

            $pageObj = $html->find('input#btngo');
            $pageArr = $pageObj[0]->attr;
            $pageStr = $pageArr['onclick'];
            $pageStrArr = explode(',',$pageStr);
            $maxPage = str_replace(array("'",")"),'',$pageStrArr[2]);

            for($i=1; $i<=$maxPage; $i++){
                //循环当前分类的每一页内容
                $i==1?$pageUrl=$genreUrl:$pageUrl=str_replace('.html', '_'.$i.'.html', $genreUrl);
                //echo $pageUrl."<br>";
                $html->load_file($pageUrl);
                $res = $html->find('div.music_block');
                //var_dump($res);
                //echo count($res);//div的个数40个
                foreach ($res as  $vv) {
                    //循环下载当前页中的mp3
                    $p = $vv->find('p');
                    $aArr = $p[0]->attr;
                    $mp3Url = $aArr['thumb'];
                    $mp3Name = strip_tags($p[1]->innertext);
                    $mp3NameToGbk = iconv('utf-8', 'gbk', $mp3Name);
                    //echo $mp3Url.'|'.$mp3Name.'<br>';
                    //var_dump($aArr);exit;
                    $mp3NewName = 'caiji/zzzj/'.$genreNameToGbk.'/'.$mp3NameToGbk.'_'.rand(10,99).'.'.getExt($mp3Url);
                    //echo $mp3NewName;exit;
                    httpdown($mp3Url,$mp3NewName);
                }
                //当前页的mp3下载完成
                $html->clear();
            }
            //当前分类的所有页下载完成

        }//所有分类下载完成

    }

    public function science_get(){
        set_time_limit(0);
        vendor('phpQuery.phpQuery');
        \phpQuery::$defaultCharset="utf-8";

        $baseUrl = 'http://kp1.cdvcloud.com';
        $dir = 'caiji/科学素质/农民';
        $dir = iconv('utf-8','gbk',$dir);
        if(!is_dir($dir)){
            mkdir($dir, 0777, true);
        }

        $totalpage = 3;

        for($i=1; $i<=$totalpage; $i++){
            $i==1?$pageurl = 'http://kp1.cdvcloud.com/farmer/index.html': $pageurl ='http://kp1.cdvcloud.com/farmer/index_'.$i.'.html';
            \phpQuery::newDocumentFile($pageurl);
            $li = pq('.list_div')->find('a');

            foreach($li as $v){
                //echo pq($v)->attr('href').'<br>';
                $url = $baseUrl.pq($v)->attr('href');
                $content = file_get_contents($url);
                preg_match_all("/window.location.href = '(.*?)'/", $content, $out, PREG_SET_ORDER);
                if(empty($out[0][1])){
                    echo $url.'没有匹配到php播放页';
                }
                $videoUrl = $baseUrl.$out[0][1];

                $videoUrl = str_replace('&amp;','&',$videoUrl);
                $result = file_get_contents($videoUrl);
                if($result){
                    preg_match_all('/swithsrc = "(.*?)"/', $result, $videoArr, PREG_SET_ORDER);
                    preg_match_all('/<h5>(.*?)<\/h5>/', $result, $titleArr, PREG_SET_ORDER);
                    $hd = $videoArr[0][1];//高清URL
                    $sd = $videoArr[1][1];//标清URL

                    $titleHd = $titleArr[0][1].'_hd_'.mt_rand(10,99).'.mp4';
                    $titleSd = $titleArr[0][1].'_sd_'.mt_rand(10,99).'.mp4';
                    $titleHd = iconv('utf-8','gbk',$titleHd);
                    $titleSd = iconv('utf-8','gbk',$titleSd);
                    httpdown($hd, $dir.'/'.$titleHd, 120);
                    httpdown($sd, $dir.'/'.$titleSd, 120);

                      $filesize = filesize($dir.'/'.$titleHd);
                      if($filesize == 0){
                        httpdown($hd, $dir.'/'.$titleHd, 120);
                      }

                      $filesize1 = filesize($dir.'/'.$titleSd);
                      if($filesize1 == 0){
                        httpdown($sd, $dir.'/'.$titleSd, 120);
                      }
                }else{
                    echo $videoUrl.'|can not get<br/>';
                }


            }


        }
    }

    /**
     * 有伴网下载http://www.youban.com/
     * @return [type] [description]
     */
    public function youban(){
        set_time_limit(0);
        vendor('phpQuery.phpQuery');
        \phpQuery::$defaultCharset="utf-8";
        $url = 'http://www.youban.com/renzhi-t4321-s1-p1.html';

        $baseUrl = 'http://www.youban.com';
        $dir = 'caiji/有伴网/四只小兔学拼音';
        $dir = iconv('utf-8','gbk',$dir);
        if(!is_dir($dir)){
            mkdir($dir, 0777, true);
        }

        $totalpage = 1;
        for($i=1; $i<=$totalpage; $i++){
            \phpQuery::newDocumentFile($url);
            $li = pq('.HotMainbox')->find('li');
            foreach($li as $v){
                $a = pq($v)->find('a');
                $aUrl = $a->attr('href');
                $content = file_get_contents($aUrl);
                preg_match_all('/quality="high" src="(.*?)"/', $content, $out );
                preg_match_all('/<h1>(.*?)<\/h1>/', $content, $out2 );
                $swfUrl = $out[1][0];
                $title = $out2[1][0];
                $swfTitle = $title.'.swf';
                $swfTitle = iconv('utf-8','gbk',$swfTitle);

                //echo $swfUrl.'<br/>'.$dir.'/'.$swfTitle;exit;
                httpdown($swfUrl, $dir.'/'.$swfTitle, 120);

                $filesize = filesize($dir.'/'.$swfTitle);
                if($filesize == 0){
                    unlink($dir.'/'.$swfTitle);
                    httpdown($swfUrl, $dir.'/'.$swfTitle, 120);
                }
            }
        }
    }




    public function getMagicVideo(){
        $url = 'http://game.hfghjg.com.cn/mxsp/data.php';
        $para = array('sentence' =>'大大大大');
        $para = http_build_query($para);
        $ch = curl_init();
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_HEADER, 0 ); // 过滤HTTP头
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT,10);
        curl_setopt($ch,CURLOPT_POST,true); // post传输数据
        curl_setopt($ch,CURLOPT_POSTFIELDS,$para);// post传输数据

        $responseText = curl_exec($ch);
        $errorCode = curl_errno($ch);
        if(0 !== $errorCode) {
            var_dump($errorCode);
            exit;
        }
        curl_close($ch);
        $data = json_decode($responseText);
        var_dump($data);
    }



   public function curl_post($page){
        echo '第'.$page.'页开始<br/>';
        ob_flush();
        flush();
        //加入头部信息
        $header[]='Cookie:ASP.NET_SessionId=ytx5so5v5pdki11oiuiigkz3; Hm_lvt_3f1a54c5a86d62407544d433f6418ef5=1465983450,1466007451,1466037490,1466144146; Hm_lpvt_3f1a54c5a86d62407544d433f6418ef5=1466148553; _gscu_2116842793=65983386whjli029; _gscs_2116842793=t6615145181qw3813; _gscbrs_2116842793=1';
        $header[] = 'User-Agent:Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.84 Safari/537.36';
        //参数信息
        $para = array (
                'Param' => '案件类型:行政案件,一级案由:行政案由,法院层级:基层法院,法院地域:浙江省,中级法院:浙江省杭州市中级人民法院' ,
                'Index' => $page ,
                'Page' => '20' ,
                'Order' => '法院层级' ,
                'Direction' => 'asc'
        // 'password' => 'password'
        );
        $para = http_build_query($para);
        $url='http://wenshu.court.gov.cn/List/ListContent';
        $ch = curl_init();
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);//SSL证书认证
        // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);//严格认证
        // curl_setopt($ch, CURLOPT_CAINFO,$cacert_url);//证书地址
        curl_setopt($ch, CURLOPT_HEADER, 0 ); // 过滤HTTP头
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT,10);
        curl_setopt($ch,CURLOPT_POST,true); // post传输数据
        curl_setopt($ch,CURLOPT_POSTFIELDS,$para);// post传输数据
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);//头部信息

        //$ip = mt_rand(101,999).'.'.mt_rand(101,999).'.'.mt_rand(101,999).'.'.mt_rand(101,999);

        // $iparr[] =  '120.194.3.98';
        // $iparr[] =  '218.28.20.138';
        // $iparr[] =  '1.192.121.169';
        // $iparr[] =  '116.255.184.25';
        // $iparr[] =  '171.221.100.19';
        // $iparr[] =  '123.161.133.229';
        // $iparr[] =  '144.255.148.110';
        // $iparr[] =  '112.195.57.221';
        // $iparr[] =  '61.174.40.245';

        //$ip = $iparr[mt_rand(0,8)];
        //$ip = '120.194.3.98';
        //curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-FORWARDED-FOR:".$ip, "CLIENT-IP:".$ip));
        $responseText = curl_exec($ch);
        var_dump( curl_error($ch) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
        $errorCode = curl_errno($ch);
        if(0 !== $errorCode) {
            //var_dump($errorCode);
            //exit;
            $responseText = '';
        }
        curl_close($ch);
        //var_dump($responseText);
        //exit;

        if(empty($responseText)||is_null($responseText)||$responseText=='"[]"'){
            $responseText = '';

            echo '<b>第'.$page.'页出问题了!</b><br>';
            file_put_contents('caiji/12345689.txt',$page."|",FILE_APPEND);
            //exit;
        }
        $responseText = str_replace('\\n','$$',$responseText);
        $responseText = str_replace('\\','',$responseText);
        $responseText = str_replace('$$','\\n',$responseText);



        $responseText = trim($responseText, '"');
        var_dump($responseText);
        //file_put_contents('caiji/aaaa.txt',$responseText);

        $responseText = str_replace('{"','@@@',$responseText);
        $responseText = str_replace('":"','###',$responseText);
        $responseText = str_replace('"}','!!!',$responseText);
        $responseText = str_replace('","','%%%',$responseText);

        $responseText = str_replace('"','&&&',$responseText);

        $responseText = str_replace('@@@','{"',$responseText);
        $responseText = str_replace('###','":"',$responseText);
        $responseText = str_replace('!!!','"}',$responseText);
        $responseText = str_replace('%%%','","',$responseText);



        $arr = json_decode($responseText,true);
        var_dump($arr);
        exit;
        //echo count($arr);exit;
        $dir = 'caiji/anjia/'.$page;
        //echo $dir;

        mkdir($dir,0777,true);
        for($i=1;$i<count($arr);$i++){
            //$count++;
            //var_dump($arr[$i]);
            //echo $i,$count.'<br>';
            if(empty($arr)){
                echo '第'.$page.'页出问题了!';
                exit;
                continue;
            }
            $a = $arr[$i]['裁判要旨段原文'];
            $b = $arr[$i]['DocContent'];
            $c = $arr[$i]['案件类型'];
            $d = $arr[$i]['裁判日期'];
            $e = $arr[$i]['案件名称'];
            $f = $arr[$i]['文书ID'];
            $g = $arr[$i]['审判程序'];
            $h = $arr[$i]['案号'];
            $m = $arr[$i]['法院名称'];

            $a = str_replace('&&&','"',$a);
            $b = str_replace('&&&','"',$b);
            $c = str_replace('&&&','"',$c);
            $d = str_replace('&&&','"',$d);
            $e = str_replace('&&&','"',$e);
            $f = str_replace('&&&','"',$f);
            $g = str_replace('&&&','"',$g);
            $h = str_replace('&&&','"',$h);
            $m = str_replace('&&&','"',$m);



            file_put_contents($dir.'/'.$i.'.txt','裁判要旨段原文:'.$a."\n",FILE_APPEND);
            file_put_contents($dir.'/'.$i.'.txt','DocContent:'.$b."\n",FILE_APPEND);
            file_put_contents($dir.'/'.$i.'.txt','案件类型:'.$c."\n",FILE_APPEND);
            file_put_contents($dir.'/'.$i.'.txt','裁判日期:'.$d."\n",FILE_APPEND);
            file_put_contents($dir.'/'.$i.'.txt','案件名称:'.$e."\n",FILE_APPEND);
            file_put_contents($dir.'/'.$i.'.txt','文书ID:'.$f."\n",FILE_APPEND);
            file_put_contents($dir.'/'.$i.'.txt','审判程序:'.$g."\n",FILE_APPEND);
            file_put_contents($dir.'/'.$i.'.txt','案号:'.$h."\n",FILE_APPEND);
            file_put_contents($dir.'/'.$i.'.txt','法院名称:'.$m."\n",FILE_APPEND);
            //exit();
        }
        sleep(mt_rand(6,10));
    }


    public function abc(){
        set_time_limit(0);
        //处理错误页码
        $arr = array(30,43,55,57,61);
        foreach($arr as $v){
            $this->curl_post($v);
        }
        exit;
        //处理错误页码结束

        for($i=59;$i<90;$i++){
            $this->curl_post($i);
        }

    }



    /**
     * 汉字笔画采集
     * @DateTime 2016-07-04T10:06:30+0800
     * @return   [type]                   [description]
     */
    public function bihua_caiji(){
        header('content-type:text/html;charset=utf-8');
        set_time_limit(0);
        vendor('phpQuery.phpQuery');
        \phpQuery::$defaultCharset="utf-8";

        $dir = 'caiji/hanzi';

        $m = M('chengyu');
        $n = M('bihua');
        $ziArr = $m->where('id>16349')->select();
        foreach($ziArr as $v){
            $zi = $v['zi'];

            echo '当前汉字为-->'.$zi.'<--<br/>';
            ob_flush();
            flush();

            $b = str_replace('%','',urlencode($zi));
            $c = strtolower($b);

            $url = 'http://bihua.51240.com/'.$c.'__bihuachaxun/';
            //开始匹配
            //echo $url;exit;
            // $content = file_get_contents($url);

            // preg_match_all('/<td bgcolor="#F5F5F5" align="center">笔画<\/td>(.*?)<\/tr>/s',$content,$out);
            // preg_match_all('/<img src="(.*?)"/',$out[1][0],$imgArr);
            // var_dump($imgArr);exit;
            // foreach($imgArr[1] as $v){
            //     $gifurl = str_replace('http://f.51240.com/file','',$v);
            // }
            //
            \phpQuery::newDocumentFile($url);
            $img = pq('table')->find('img');
            $ziArr = array();
            foreach($img as $k=>$v){
                $imgurl = pq($v)->attr('src');
                $gifurl = str_replace('http://f.51240.com/file','',$imgurl);
                //var_dump($gifurl);exit;
                $ziArr[$k]['img'] = $gifurl;

                if(!file_exists($dir.$gifurl)){
                    httpdown($imgurl, $dir.$gifurl, 120);
                }

            }


            $remakObj = pq('table')->eq(1)->find('tr')->eq(5)->find('td')->eq(1);
            $remark = pq($remakObj)->html();
            $remark = str_replace('<span class="charu_yc_url"> 更多：<a href="http://www.51240.com/" target="_blank">http://www.51240.com/</a> </span>','',$remark);
            //var_dump($remark);exit;
            $remark = rtrim($remark,'、');
            $data = explode('、',$remark);
            foreach($data as $k=>$v){
               $ziArr[$k]['remark'] = $v;
            }

            //var_dump($ziArr);exit;
            foreach($ziArr as $k=>$v){
                $ziinfo['zi'] = $zi;
                $ziinfo['bihua'] = $v['remark'];
                $ziinfo['img'] = $v['img'];
                $ziinfo['sort'] = $k+1;

                $n->add($ziinfo);
            }

        }


    }



    public function getPinyin(){
        header('content-type:text/html;charset=utf-8');
        set_time_limit(0);
        vendor('Curl_class');
        $cur = new \Curl();

        $m = M('cidian');
        $data = $m->field('id,ciyu')->where("id>75844")->select();
        foreach($data as $v){
            $id = $v['id'];
            $ciyu = $v['ciyu'];
            $url = 'http://www.51pinyin.com/GetData.ashx?url=index';
            $fields['name'] = $ciyu;
            //echo $ciyu;


            $re = $cur->post($url, $fields);
            //var_dump($re);
            $m->where('id="%d"',$id)->setField('pinyin',$re);
            echo $id.'->'.$ciyu.'->'.$re.'<br>';
            //sleep(3);
            ob_flush();
            flush();
            echo '<script>window.scrollTo(0,document.body.scrollHeight);</script>';
        }

        $cur->quit();
    }



    //获取字典
    public function getZidian(){
        header('content-type:text/html;charset=utf-8');
        set_time_limit(0);
        vendor('Curl_class');

        $cur = new \Curl();
        $m = M('chengyu','t_');
        $ziArr = $m->select();
         // var_dump($ziArr);exit;

        $count = 1;
        $table = M('hanzi_info','hl_');
        for($i=0;$i<count($ziArr);$i++){
            $zi = $ziArr[$i]['zi'];

            $isempty = $table->where('name="%s"',$zi)->find();
            //var_dump($isempty);exit;
            if(!empty($isempty)){
                continue;
            }


            $zi = iconv('utf-8','gbk',$zi);
            $url = 'http://s.diyifanwen.com/ZdSearch.asp?Query='.urlencode($zi).'&Submit=%D7%D6%B5%E4%B2%E9%D1%AF&Search=SWord';

            $header[] = 'Cookie:ASPSESSIONIDCSBCTCSQ=PHEMAJNDLKHOCEJGFJDKHHGD; Hm_lvt_3a5e11b41af918022c823a8041a34e78=1470013422,1470123723; Hm_lpvt_3a5e11b41af918022c823a8041a34e78=1470125361';
            $header[] = 'User-Agent:Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36';
            $header[] = 'Referer:http://zd.diyifanwen.com/zidian/Y/0931810441915291614.htm';
            $data = $cur->get($url,'',$header);


            if($data == 'err'){
                $count++;
                sleep(5*$count);
                $i--;
                continue;
            }else{
                $count = 1;
            }


            $data = iconv('gbk','utf-8',$data);
            // var_dump($data);

            preg_match_all('/<li id="py">(.*?)li>/',$data,$out1);
            preg_match_all('/<li id="bs">(.*?)li>/',$data,$out2);
            preg_match_all('/<li id="bh">(.*?)li>/',$data,$out3);
            preg_match_all('/<div class="on">(.*?)<\/div>/',$data,$out4);
            preg_match_all('/<div class="off">(.*?)<\/div>/',$data,$out5);


            $pyarr = preg_replace('/<script>(.*?)<\/script>/','',$out1[0][0]);
            $py = strip_tags($pyarr);
            $bs = strip_tags($out2[0][0]);
            $bh = strip_tags($out3[0][0]);
            $jbjs = $out4[1][0];
            $xxjs = $out5[1][0];

            $py = str_replace('拼音：','',$py);
            $bs = str_replace('部首：','',$bs);
            $bh = str_replace('笔画数：','',$bh);
            // var_dump($py);
            // var_dump($bs);
            // var_dump($bh);
            // var_dump($jbjs);
            // var_dump($xxjs);
            // exit;
            $re['name'] = $ziArr[$i]['zi'];
            $re['bushou'] = $bs;
            $re['bihua'] = $bh;
            $re['jbjieshi'] = $jbjs;
            $re['xxjieshi'] = $xxjs;
            $table->add($re);
            echo '<script>document.body.innerHTML="";</script>';
            echo $ziArr[$i]['zi'].'添加成功！';
            ob_flush();
            flush();
            sleep(3);
        }



    }









}