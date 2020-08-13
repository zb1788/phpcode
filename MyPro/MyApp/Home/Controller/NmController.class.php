<?php
namespace Home\Controller;
use Think\Controller;
class NmController extends Controller {

	//下载纳米盒的mp3
	public function getPicMp3(){

		set_time_limit(0);
		vendor('fileDirUtil');
        $fileDir = new \fileDirUtil();
		$book = M('db_nami.book','nm_');
		//采集所有
		// $re = $book->where('flag=20')->select();
		/*单独采集一本*/
		$re = $book->where('id=47 or id=72 or id=94')->select();
		//$re = $book->where('flag=11 and bookname like "%鲁科版%"')->select();
		// var_dump($re);exit();
		foreach($re as $v){
			$txt = 'caiji/namihe/old/'.$v['nianji'].'/'.$v['xueqi'].'/'.$v['txt'];
			// echo $txt;exit();
			$contents = file_get_contents($txt);
			// var_dump($contents);exit;
			preg_match_all('/var book_path        = "(.*?)"/',$contents, $out, PREG_SET_ORDER);
			preg_match_all('/var book_mp3         = "(.*?)"/',$contents, $out2, PREG_SET_ORDER);
			preg_match_all('/var imglist         = (.*?)]/',$contents, $out3, PREG_SET_ORDER);
			preg_match_all('/var map_id_mp3      = (.*?);/',$contents, $out4, PREG_SET_ORDER);
			preg_match_all('/"(.*?)"/',$out4[0][1], $mp3Arr, PREG_SET_ORDER);



			$picUrl = 'http://ra.namibox.com/tina/static/'.$out[0][1];
			$mp3Url = 'http://ra.namibox.com/tina/static/'.$out[0][1];
			// echo $picUrl.'<br>'.$mp3Url;exit();

			$arrPic = explode(',', ltrim($out3[0][1],'['));
			//下载图片
			foreach($arrPic as $v){
				$picUrlnow = $picUrl.'/bookshow/'.str_replace(array('"','"'), '', $v);

				$arrPic = explode('_', str_replace('http://ra.namibox.com/','',$picUrlnow));
				//var_dump($arrPic);
				//echo 'caiji/namihe/pic/'.$arrPic[0].'/'.$arrPic[count($arrPic)-1];

				$path = 'caiji/namihe/old/pic/'.$arrPic[0];
				//改名字w
				//$picname = 'caiji/namihe/pic/'.$arrPic[0].'/'.$arrPic[count($arrPic)-1];
				//不改名字
				$picname = 'caiji/namihe/old/pic/'.$arrPic[0].'/'.pathinfo($picUrlnow,PATHINFO_BASENAME);

				$fileDir->createDir($path);
				//echo $picUrlnow.'|'.$path.'|'.$picname.'<br>';exit();
				httpdown($picUrlnow,$picname);
				if(filesize($picname) == 0){
					unlink($picname);
					httpdown($picUrlnow,$picname);
				}

			}

			//下载mp3
			foreach($mp3Arr as $v){
				$mp3Urlnow = $mp3Url.'/'.$v[1];
				$arrMp3 = explode('_', str_replace('http://ra.namibox.com/','',$mp3Urlnow));

				$path = 'caiji/namihe/old/mp3/'.$arrMp3[0];
				$fileDir->createDir($path);
				//改名字
				//$mp3name = 'caiji/namihe/mp3/'.$arrMp3[0].'/'.md5(uniqid(microtime(true),true)).'.mp3';
				//不改名字
				$mp3name = 'caiji/namihe/old/mp3/'.$arrMp3[0].'/'.pathinfo($mp3Urlnow,PATHINFO_BASENAME);
				// echo $mp3name.'<br>';
				// echo $mp3Urlnow.'<br>';exit();
				// $mp3Urlnow = iconv('utf-8','gbk',$mp3Urlnow);

				httpdown($mp3Urlnow,$mp3name);

				if(filesize($mp3name) == 0){
					unlink($mp3name);
					httpdown($mp3Urlnow,$mp3name);
				}

			}
			echo $v['txt'].'Complete!.<br>';
		}


	}

	//纳米盒热区数据
	public function getHot(){
		header('content-type:text/html;charset=utf-8;');
		set_time_limit(0);
		vendor('fileDirUtil');
        $fileDir = new \fileDirUtil();

		$book = M('db_nami.book','nm_');
		//$re = $book->where('xueqi="a" and xueke like "英语"  and bookid_126 IS NOT NULL')->select();
		/*单独处理某一本书*/
		$re = $book->where('id=108')->select();//253

		foreach ($re as $vall){
			$bookid_126=$vall['bookid_126'];
			$txt = 'caiji/namihe/old/'.$vall['nianji'].'/'.$vall['xueqi'].'/'.$vall['txt'];
			$contents = file_get_contents($txt);

			preg_match_all('/var imglist         = (.*?)]/',$contents, $out1, PREG_SET_ORDER);
			preg_match_all('/var map_page_track  = {(.*?)};/',$contents, $out2, PREG_SET_ORDER);
			preg_match_all('/var tracks          = {(.*?)};/',$contents, $out4, PREG_SET_ORDER);
			preg_match_all('/var map_mp3_track   = {(.*?)};/',$contents, $out6, PREG_SET_ORDER);
			preg_match_all('/var map_id_mp3      = {(.*?)};/',$contents, $out8, PREG_SET_ORDER);


			//书页数组
			$arrPic = explode(',', ltrim($out1[0][1],'['));
			//var_dump($arrPic);exit;

			//var_dump($out2[0][1]);exit;
			//获取书页和热点对应关系
			$page_hot = explode(('|'), str_replace('],',']|',$out2[0][1]));
			//var_dump($page_hot);exit;
			$arrPage_hot = array();
			foreach($page_hot as $k => $v){

				preg_match_all('/\[(.*?)\]/',$v, $out3, PREG_SET_ORDER);
				//var_dump($out3[0][1]);
				$tmpArr = explode(',', $out3[0][1]);
				foreach($tmpArr as $kk => $vv){
					$arrPage_hot[$k][$kk] = $vv;
				}
			}
			//var_dump($arrPage_hot);exit;

			//var_dump($out4[0][1]);exit;
			//获取热点数组
			$hot = explode(('|'), str_replace('],',']|',$out4[0][1]));
			$arrHot = array();
			//var_dump($hot);exit;
			foreach($hot as $k => $v){
				preg_match_all('/\[(.*?)\]/',$v, $out5, PREG_SET_ORDER);
				//var_dump($out5[0][1]);exit;
				//$tmpArr = explode(',', $out5[0][1]);
				//var_dump($tmpArr);exit;
				$tmpArr = explode('","', $out5[0][1]);
				//var_dump($tmpArr);
				//$tmpArr[6]='3.92",0,0';把他拆分成三个
				$tmpArr2 = explode(',',str_replace('"','',$tmpArr[6]));
				//var_dump($tmpArr2);
				$tmpArr[0] = str_replace('"','',$tmpArr[0]);
				$tmpArr[6] = $tmpArr2[0];
				$tmpArr[7] = $tmpArr2[1];
				$tmpArr[8] = $tmpArr2[2];
				//var_dump($tmpArr);exit;

				foreach($tmpArr as $kk => $vv){
					$arrHot[$k][$kk] = $vv;
				}
			}
			//var_dump($arrHot);exit;

			//获取MP3和热点对应关系数组
			//var_dump($out6[0][1]);exit;
			$kka = $out6[0][1];
			$abcd = '{'.$kka.'}';

			//var_dump($this->ext_json_decode($abcd,true));exit;

			/**
			$mp3_hot = explode(('|'), str_replace('],',']|',$out6[0][1]));
			*/
			$mp3_hot = $this->ext_json_decode($abcd,true);
			//var_dump($mp3_hot);exit;


			$arrMp3_Hot = array();
			foreach($mp3_hot as $k => $v){
				// preg_match_all('/\[(.*?)\]/',$v, $out7, PREG_SET_ORDER);
				// $tmpArr = explode(',', $out7[0][1]);
				// foreach($tmpArr as $kk => $vv){
				// 	$arrMp3_Hot[$k][$kk] = $vv;
				// }

				foreach ($v as $kk => $vv) {
					$arrMp3_Hot[$k][$kk] = $vv;
				}
			}

			//获取MP3数组
			preg_match_all('/"(.*?)"/',$out8[0][1], $out9, PREG_SET_ORDER);
			$arrMp3 = array();
			foreach($out9 as $k => $v){
				$arrMp3[$k] = $v[1];
			}
			//var_dump($arrMp3);exit;


			//先遍历mp3数组，把MP3信息插入热点里
			foreach($arrMp3 as $k => $v){
				//echo $k.'|'.$v;exit();
				foreach($arrMp3_Hot[$k] as $kk => $vv){
					$arrHot[$vv];
					array_push($arrHot[$vv],$v);

				}

			}
			//var_dump($arrMp3_Hot);exit;
			//var_dump($arrHot);exit;
			//var_dump($arrPage_hot);exit;
			//var_dump($arrPic);exit;

			//遍历书本每一页
			foreach($arrPic as $k => $v){
				$pagenum = str_replace(array('"page_','.jpg"'), '', $v);
				$data['pagenum'] = (int)$pagenum;

				$mulu = str_replace(array($vall['nianji'].$vall['xueqi'],'.txt'), '', $vall['txt']);
				//连接本地数据的字段(126没有)
				// $data['bookid'] = $vall['id'];

				//图片重命名
				$picname = 'caiji/namihe/old/pic/tina/static/d/tape'.$vall['nianji'].$vall['xueqi'].'/'.$mulu.'/'.str_replace('"','',$v);
				$picNewName = 'caiji/namihe/deal/pic/tina/static/d/tape'.$vall['nianji'].$vall['xueqi'].'/'.$mulu.'/'.$pagenum.'.jpg';


				// echo $picname.'|'.$picNewName;exit;


				foreach($arrPage_hot[$k] as $kk => $vv){
					//echo str_replace('"', '', $arrHot[$vv][5]);

					if(empty($arrHot[$vv])){
						continue;
					}
					//开始时间格式化
					$arrBegTime = explode('.', str_replace('"', '', $arrHot[$vv][5]));
					$num = $arrBegTime[0];
					$hour = floor($num/3600);
					$minute = floor(($num-3600*$hour)/60);
					$second = floor((($num-3600*$hour)-60*$minute)%60);
					//echo $hour.':'.$minute.':'.$second;
					$vbeg = ($minute>=10?$minute:'0'.$minute).':'.($second>=10?$second:'0'.$second).':'.($arrBegTime[1]>=100?$arrBegTime[1]:$arrBegTime[1].'0');
					//echo $vbeg;

					//结束时间格式化
					$arrEndTime = explode('.', str_replace('"', '', $arrHot[$vv][6]));
					$num = $arrEndTime[0];
					$hour = floor($num/3600);
					$minute = floor(($num-3600*$hour)/60);
					$second = floor((($num-3600*$hour)-60*$minute)%60);
					//echo $hour.':'.$minute.':'.$second;
					$vend = ($minute>=10?$minute:'0'.$minute).':'.($second>=10?$second:'0'.$second).':'.($arrEndTime[1]>=100?$arrEndTime[1]:$arrEndTime[1].'0');
					//echo $vend;


					/**
					*以前是按照宽度等比例缩放到460后计算的热点位置坐标
					*/
					//获取图片宽高
					// $picInfo = getimagesize($picname);
					// $w_d = $picInfo[0];
					// $h_d = $picInfo[1];

					// //获取图片现在宽高
					// $w_n = 460;
					// $h_n = 460/$w_d*$h_d;

					// $x = str_replace('"', '', $arrHot[$vv][3])*$w_n;
					// $y = str_replace('"', '', $arrHot[$vv][0])*$h_n;
					// $w = (str_replace('"', '', $arrHot[$vv][1])-str_replace('"', '', $arrHot[$vv][3]))*$w_n;
					// $h = (str_replace('"', '', $arrHot[$vv][2])-str_replace('"', '', $arrHot[$vv][0]))*$h_n;

					// //echo round($x).'/'.round($y).'/'.round($w).'/'.round($h).'<br>';continue;
					// if($vall['flag']==0){
					// 	//偶数在左
					// 	if((int)$pagenum%2==0){

					// 	}else{
					// 		$x = $x+460;
					// 	}
					// }else{
					// 	//奇数在左
					// 	if((int)$pagenum%2==0){
					// 		$x = $x+460;
					// 	}else{

					// 	}
					// }

					/*
					*按照宽度等比例缩放到460后计算的热点位置坐标结束
					*/

					//var_dump($arrHot[$vv]);

					//echo $picname;
					//获取图片原始宽高
					$picInfo = getimagesize($picname);

					$w_d = $picInfo[0];
					$h_d = $picInfo[1];

					$x = str_replace('"', '', $arrHot[$vv][3])*$w_d;
					$y = str_replace('"', '', $arrHot[$vv][0])*$h_d;
					$w = (str_replace('"', '', $arrHot[$vv][1])-str_replace('"', '', $arrHot[$vv][3]))*$w_d;
					$h = (str_replace('"', '', $arrHot[$vv][2])-str_replace('"', '', $arrHot[$vv][0]))*$h_d;

					// var_dump($picInfo);
					// var_dump($arrHot[$vv]);
					// echo $x.','.$y.','.$w.','.$h.'<br>';

					// echo round($x,2).','.round($y,2).'|'.round(($x+$w),2).','.round($y,2).'|'.round(($x+$w),2).','.round(($y+$h),2).'|'.round($x,2).','.round(($y+$h),2);
					// exit;
					$mp3name = '/tina/static/d/tape'.$vall['nianji'].$vall['xueqi'].'/'.$mulu.'/'.$arrHot[$vv][9];
					//echo $mp3name.'<br>';

					//var_dump($arrHot[$vv]);
					// $data['x'] = round($x);//X坐标
					// $data['y'] = round($y);//Y坐标
					// $data['w'] = round($w);//w坐标
					// $data['h'] = round($h);//h坐标
					$data['plist'] = round($x,2).','.round($y,2).'|'.round(($x+$w),2).','.round($y,2).'|'.round(($x+$w),2).','.round(($y+$h),2).'|'.round($x,2).','.round(($y+$h),2);
					$data['vbeg'] = $vbeg;//开始时间
					$data['vend'] = $vend;//结束时间
					$data['urlname'] = $mp3name;//mp3路径
					$data['fanyi'] =rtrim(ltrim($arrHot[$vv][4],'"'),'"');//翻译

					//连接本地数据库
					// $data['bookid_126'] = $bookid_126;
					//连接126数据库
					$data['bookid'] = $bookid_126;
					$data['type'] = 1;




					//var_dump($data);
					//连本地数据库
					//$hot_table = M('db_nami.page_hot','nm_');
					//直接连126数据库
					$hot_table = M('book_page_hot','t_','mysql://root:123456@192.168.151.126/db_ebook');

					/*

					//对比txt和数据中的时长一样么
					$hot_table = M('db_nami.page_hot','nm_');
					$hotdb = M('book_page_hot','t_','mysql://root:123456@192.168.151.126/db_ebook');
					$datadb = $hotdb->where('bookid="%s" and pagenum="%d" and vbeg="%s" and vend="%s"',$bookid_126,(int)$pagenum,$vbeg,$vend)->find();

					if(empty($datadb)){
						echo $bookid_126.'|'.(int)$pagenum.'|'.$vbeg.'|'.$vend.'no<br>';
						ob_flush();
					flush();
					$book->where('bookid_126="%s"',$bookid_126)->setField('isend',11);
					}else{
						// echo $bookid_126.'|'.(int)$pagenum.'|'.$vbeg.'|'.$vend.'yes<br>';
					}

					// exit(var_dump($datadb));
					 */

					$hot_table->add($data);
				}
				//exit;
				//重命名图片
				$fileDir->copyFile($picname , $picNewName, true);

			}

			//$data_book['isend']=2;
			//$book->where('id="%d"',$vall['id'])->save($data_book);
			//重命名MP3
			//$this->mp3Rename($vall['id']);
			$this->mp3Rename($bookid_126);

			echo '书本：'.$vall['id'].'处理完毕！<br/>';
			ob_flush();
			flush();

		}


	}

	//MP3重命名单独处理
	public function mp3Rename($id){
		set_time_limit(0);
		vendor('fileDirUtil');
        $fileDir = new \fileDirUtil();
        //本地数据库
		// $hot = M('db_nami.page_hot','nm_');
		//126数据库
		$hot = M('book_page_hot','t_','mysql://root:123456@192.168.151.126/db_ebook');
		$re = $hot->distinct(true)->where('bookid="%s" and isdel=0',$id)->field('urlname')->select();

		// var_dump($re);exit;
		foreach($re as $v){
			$mp3name = 'caiji/namihe/old/mp3'.$v['urlname'];

			$dir = pathinfo($mp3name,PATHINFO_DIRNAME);

			$mp3NewName = $dir.'/'.md5(basename($mp3name,'.mp3')).'.mp3';
			//echo $mp3name.'<br>'.$mp3NewName.'<br>';
			//@rename($mp3name,$mp3NewName);//重命名

			$mp3NewName = STR_REPLACE('caiji/namihe/old/mp3','caiji/namihe/deal/mp3',$mp3NewName);

			//echo $mp3name.'<br>'.$mp3NewName;exit;
			$fileDir->copyFile(iconv('utf-8', 'gbk', $mp3name) , $mp3NewName, true);


			$data['urlname'] = str_replace('caiji/namihe/deal/mp3', '', $mp3NewName);

			$hot->where('urlname="%s"',$v['urlname'])->save($data);
		}


	}

function ext_json_decode($str, $mode=false){
  if(preg_match('/\w:/', $str)){
    $str = preg_replace('/(\w+):/is', '"$1":', $str);
  }
  return json_decode($str, $mode);
}


	/**
	 * 纳米重命名文件夹名字
	 * @DateTime 2016-06-01T10:32:48+0800
	 * @return   [type]                   [description]
	 */
	public function renameDir(){
		header('content-type:text/html;charset=utf-8');
		set_time_limit(0);
		vendor('fileDirUtil');
		$path = 'caiji/namihe/deal/pic/tina/static/d/';
		$dir = new \fileDirUtil();
		$pathArr = $dir->dirNodeTree($path);

		$m = M('db_nami.book','nm_');
		for($i=0; $i<count($pathArr); $i++){
			$tapename = $pathArr[$i];
			$grade = str_replace(array('tape','a'),'',$tapename);

			$bookArr = $dir->dirNodeTree($path.$tapename);


			for($j=0;$j<count($bookArr);$j++){
				$bookname = $bookArr[$j];
				$txt = $grade.'a'.$bookname;

				$sql = 'select bookname from db_nami.nm_book where bookid_126 is not null and txt like "%'.$txt.'%"';
				$data = $m->query($sql);
				if(empty($data)){
					continue;
				}
				$NewbookName = $data[0]['bookname'];

				$NewbookName = iconv('utf-8','gbk',$NewbookName);
				//echo $path.$tapename.'/'.$bookname.'<br/>';exit;
				rename($path.$tapename.'/'.$bookname, $path.$tapename.'/'.$NewbookName);
				echo $path.$tapename.'/'.$bookname.'完成！<br/>';
				ob_flush();
				flush();
			}
		}
	}

	/**
	 * [讯飞数据处理]
	 * @return [type] [description]
	 */
	public function xunfei(){
		header('content-type:text/html;charset=utf-8');
		set_time_limit(0);

		C('DB_HOST','192.168.151.126');
        C('DB_NAME','db_ebook');

    	vendor('fileDirUtil');
    	$dir = new \fileDirUtil();
    	$filePath = 'caiji/xunfei';
    	//$filePath = 'I:/ttn';
    	//$filePath = 'G:\xufeiebook';

    	$dirs = $dir -> dirNodeTree($filePath);//目录数组

    	// $bookid = '000102030204';//书本ID
    	// $pageflag = 0; //0偶数在左，1奇数在左
    	// //定义一个偏移量
    	// $pageOffSet=-1;

    	//var_dump($dirs);exit;
    	$errorinfo = array();//错误信息数组
    	$success = array();//成功信息数组



		for ($i=0;$i<count($dirs);$i++){
			$bookName = $dirs[$i];//当前书本名称

			$txt = $filePath.'/'.$dirs[$i].'/1.txt';
			if(!file_exists($txt)){
				echo $bookName.' txt not EXISTS!<br>';
				ob_flush();
				flush();
				continue;
			}
			//echo $txt;exit;
			$txtArr = $dir->readFile2array($txt);
			$bookid = $txtArr[0];//书本ID
			$pageOffSet = $txtArr[1];//偏移量

			$m_xunfei = M('db_ebook.book_page_hot','t_');

			$m_xunfei->where('bookid="%s"',$bookid)->delete();

			echo $bookid.'开始<br>';
			ob_flush();
			flush();
			echo '<script>window.scrollTo(0,document.body.scrollHeight);</script>';
			file_put_contents('caiji/abcdefg.txt',$bookid.PHP_EOL, FILE_APPEND);
/**
			$rexunfei = $m_xunfei->where('bookid="%s"',$bookid)->find();

			//var_dump($rexunfei);exit;
			if(!empty($rexunfei)){
				//如果数据库有信息说明已经录入，跳过这本书
				echo $bookid.'已存在<br>';
				continue;
			}
*/
			//var_dump($txtArr);exit;
			$dirsPage = $dir->dirNodeTree($filePath.'/'.$dirs[$i],'jpg');//书本图片数组
			//var_dump($dirsPage);exit;
			//遍历图片对应的文件夹，获取热点信息
			for($j=0;$j<count($dirsPage);$j++){
				// if($dirsPage[$j]!=6){
				// 	continue;
				// }
				$path = $filePath.'/'.$dirs[$i].'/'.$dirsPage[$j].'/xml/'.$dirsPage[$j].'.xaml';//图片对应的xml路径
				//echo $path."<br>";
				//echo $path;exit;
				//if(($dirsPage[$j]+$pageOffSet)!==3){continue;}
				//echo $path;exit;
				//$test='caiji/3.xaml';

				$xmlContent = file_get_contents($path);
				//var_dump($xmlContent);
				preg_match_all("/<Rectangle(.*?)>/",$xmlContent, $out, PREG_SET_ORDER);//如果可以匹配到就是有热点数据
				preg_match_all("/<Path(.*?)>/",$xmlContent, $out_path, PREG_SET_ORDER);//如果可以匹配到就是有热点数据
				//preg_match_all("/background=(.*?)eb=/",$xmlContent, $out, PREG_SET_ORDER);
				//var_dump($out);
				if(empty($out)&&empty($out_path)){
					//echo '没有热点';
					continue;
				}else{
					//echo '有热点';exit;
					$mp3Hecheng = '';
					$array_hotlist = array();
					$xmlArr = $dir->readFile2array($path);//获取xml文件的数组

					//根据MP3TXT获取开始结束时间（新版是整个音频，不用了）
					/**
					$mp3txt = $filePath.'/'.$dirs[$i].'/'.$dirsPage[$j].'/'.$dirsPage[$j].'_mp3_timeinfo.txt';//获取MP3txt数组
					//echo $mp3txt."<br>";continue;
					$mp3txtContent = file_get_contents($mp3txt);
					//1-653-b-00:00:000-e-00:00:653
					//正则匹配mp3的开始结束时长
					preg_match_all("/(.*?)-(.*?)-(.*?)-b-(.*?)\r\n/",$mp3txtContent, $out_mp3txt, PREG_SET_ORDER);
					//$mp3DateARR = $dir->readFile2array($mp3txt);
					//var_dump($out_mp3txt);exit;
					$arrMp3Info = array();
					foreach($out_mp3txt as $vinfo){
						$tmpMp3Arr = array();
						$tmpMp3Arr['beg'] = $vinfo[3];
						$tmpMp3Arr['end'] = $vinfo[4];
						$arrMp3Info[$vinfo[1]] = $tmpMp3Arr ;
						// if($vinfo[1]==1114){
						// 	var_dump($arrMp3Info);exit;
						// }
					}
					//var_dump($arrMp3Info);exit;
					**/
					//var_dump($xmlArr);
					//echo count($xmlArr);exit;
					//循环获取txt中的热点信息
					for($k=2;$k<(count($xmlArr)-3);$k=$k+2){
						//var_dump($xmlArr[$k]);
						//echo $xmlArr[$k].'<br>';exit;
						//var_dump(strpos($xmlArr[$k],'Width'));
						// if($k!=16){
						// 	continue;
						// }
						unset($pageHotData);
						$plist='';
						/**
						 临时的
						 */
						//获取当前真实页码
						if($bookid == '000108030205'){
							//七上江苏教育出版,特殊处理
							if($dirsPage[$j]<=45){
								$pagenum = $dirsPage[$j]-12;
							}else if($dirsPage[$j]>45&&$dirsPage[$j]<=134){
								$pagenum = $dirsPage[$j]-11;
							}else if($dirsPage[$j]>134&&$dirsPage[$j]<=161){
								$pagenum = $dirsPage[$j]-10;
							}else{
								$pagenum = $dirsPage[$j]-9;
							}
						}else if($bookid == '00010a030203'){
							if($dirsPage[$j]<=140){
								$pagenum = $dirsPage[$j]-5;
							}else{
								$pagenum = $dirsPage[$j]-6;
							}
						}else if($bookid =='000108030403'){
							if($dirsPage[$j]<=18){
								$pagenum = $dirsPage[$j]-6;
							}else{
								$pagenum = $dirsPage[$j]-6;
							}
						}else if($bookid =='000108030405'){
							if($dirsPage[$j]<=25){
								$pagenum = $dirsPage[$j]-0;
							}else if($dirsPage[$j]>25&&$dirsPage[$j]<=29){
								continue;
							}else{
								$pagenum = $dirsPage[$j]-4;
							}
						}else{
							$pagenum = $dirsPage[$j]+$pageOffSet;
						}



						//if($pagenum !== 3){continue;}

						if(strpos($xmlArr[$k],'Width')){
							//echo '文件夹'.$dirsPage[$j].'中的规则热点<br>';
							//例子
							//<Rectangle Width="289" Height="48" Canvas.Left="86" Canvas.Top="153" eb:HotspotControl.Audio="178" Panel.ZIndex="0">
							preg_match_all('/="(.*?)"/',$xmlArr[$k], $hotArr, PREG_SET_ORDER);//匹配数据

							//var_dump($hotArr);exit;
							$width = $hotArr[0][1];
							$height = $hotArr[1][1];
							$left = $hotArr[2][1];
							$top = $hotArr[3][1];
							$mp3Old = $filePath.'/'.$dirs[$i].'/'.$dirsPage[$j].'/'.$hotArr[4][1].'.mp3';//对应的mp3
							//echo $Mp3Old."<br>";exit;
							//使用合成后的mp3
							// if($mp3Hecheng == $mp3Old){
								// $mp3 = $filePath.'/'.$dirs[$i].'/'.$dirsPage[$j].'/'.$dirsPage[$j].'_mp3.mp3';//对应的mp3

								$mp3 = $mp3Old;

								//echo $mp3;exit;
								// $mp3Hecheng = $mp3;//把当前文件的mp3赋值
								//$mp3Target ='caiji/xunfeimp3/'.$bookid.'/'.$pagenum.'/'.$hotArr[4][1].'.mp3';
								$mp3str = '/xunfeimp3/'.$bookid.'/'.$pagenum.'/'.md5(uniqid(microtime(true),true)).'.mp3';
								$mp3Target ='caiji'.$mp3str;
								//echo $mp3.'<br>'.$mp3Target;exit;

								if(!file_exists($mp3Old)){
									//echo '不存在<br>';
									$err = $bookid.'|'.$pagenum."\r\n";//错误书本和错误页码
									file_put_contents('caiji/nohot.txt',$err,FILE_APPEND);
									//continue;
								}

								if(!file_exists($mp3)){
									continue;
								}

								///拷贝mp3
								//**
								$dir->copyFile($mp3 , $mp3Target, true);
								//*/
							// }


							//echo $mp3;exit;
							//if($pagenum == 15){echo $left;}
							//根据单双页左，来判断右边半页的坐标
							// if($pageflag == 0){
							// 	//偶数在左
							// 	if((int)$pagenum%2==0){

							// 	}else{
							// 		$left = $left + 460;
							// 	}
							// }else{
							// 	//奇数在左
							// 	if((int)$pagenum%2==0){
							// 		$left = $left + 460;
							// 	}else{

							// 	}
							// }

							//if($pagenum == 15){echo $left.'|'; echo round(460/1110*$left);break;}


							//var_dump($hotArr);
							//往数据写入每个热点信息
							// $str ='<hotmusic id="2">'."\n";
							// $str .='<name>requ</name>'."\n";
							// $str .='<link>'.$hotArr[4][1].'.mp3'.'</link>'."\n";
							// $str .='<pX>'.round(460/1110*$left).'</pX>'."\n";
							// $str .='<pY>'.round(650/1570*$top).'</pY>'."\n";
							// $str .='<width>'.round(650/1570*$width).'</width>'."\n";
							// $str .='<height>'.round(460/1110*$height).'</height>'."\n";
							// $str .='<play>00:02:970</play>'."\n";
							// $str .='<stop>00:05:160</stop>'."\n";
							// $str .='</hotmusic>'."\n";

							//file_put_contents('caiji/data.txt', $str,FILE_APPEND);
							//
							$m_xunfei = M('db_ebook.book_page_hot','t_');

							$pageHotData['bookid'] = $bookid;
							$pageHotData['pagenum'] = $pagenum;
							// $pageHotData['px0'] = round(460/1110*$left);
							// $pageHotData['py0'] = round(650/1570*$top);
							// $pageHotData['px1'] = round(460/1110*($left+$width));
							// $pageHotData['py1'] = round(650/1570*$top);
							// $pageHotData['px2'] = round(460/1110*($left+$width));
							// $pageHotData['py2'] = round(650/1570*($top+$height));
							// $pageHotData['px3'] = round(460/1110*$left);
							// $pageHotData['py3'] = round(650/1570*($top+$height));

							//$plist = round(460/1110*$left).','.round(650/1570*$top).'|'.round(460/1110*($left+$width)).','.round(650/1570*$top).'|'.round(460/1110*($left+$width)).','.round(650/1570*($top+$height)).'|'.round(460/1110*$left).','.round(650/1570*($top+$height));


							$plist = round($left).','.round($top).'|'.round($left+$width).','.round($top).'|'.round($left+$width).','.round($top+$height).'|'.round($left).','.round($top+$height);
							$pageHotData['plist'] = $plist;

							$pageHotData['type'] = 1;
							$pageHotData['vbeg'] = "00:00:000";
							$pageHotData['vend'] = "00:00:000";
							// $pageHotData['vbeg'] = $arrMp3Info[$hotArr[4][1]]['beg'];
							// $pageHotData['vend'] = $arrMp3Info[$hotArr[4][1]]['end'];


							//$pageHotData['urlname'] = '/xunfeimp3/'.$bookid.'/'.$pagenum.'/'.$hotArr[4][1].'.mp3';
							$pageHotData['urlname'] = $mp3str;
							$pageHotData['hotmp3'] = ltrim($mp3str,"/");
							$pageHotData['iscut'] = 1;

							//var_dump($pageHotData);continue;


							// if(!empty($arrMp3Info[$hotArr[4][1]])){
							// 	$m_xunfei->add($pageHotData);
							// }

							$m_xunfei->add($pageHotData);

						}else{
							//echo '文件夹'.$dirsPage[$j].'中的不规则热点<br>';
							//例子
							//<Path Data="M372,514 L372,552 101,555 101,604 817,604 818,563 1001,563 999,515Z" eb:HotspotControl.Audio="186" Panel.ZIndex="1">
							preg_match_all('/="(.*?)"/',$xmlArr[$k], $hotArr, PREG_SET_ORDER);//匹配数据
							//var_dump($hotArr);exit;
							$points = $hotArr[0][1];//DATA的值

							// if($mp3Hecheng == ''){
								$mp3Old = $filePath.'/'.$dirs[$i].'/'.$dirsPage[$j].'/'.$hotArr[1][1].'.mp3';//对应的mp3
								//使用合成后的mp3
								// $mp3 = $filePath.'/'.$dirs[$i].'/'.$dirsPage[$j].'/'.$dirsPage[$j].'_mp3.mp3';//对应的mp3

								$mp3 = $mp3Old;

								$mp3Hecheng = $mp3;//把当前文件的mp3赋值
								$mp3str = '/xunfeimp3/'.$bookid.'/'.$pagenum.'/'.md5(uniqid(microtime(true),true)).'.mp3';
								$mp3Target ='caiji'.$mp3str;

								if(!file_exists($mp3Old)){
									//echo '不存在<br>';
									$err = $bookid.'|'.$pagenum."\r\n";//错误书本和错误页码
									file_put_contents('caiji/nohot.txt',$err,FILE_APPEND);
									//continue;
								}
								if(!file_exists($mp3)){
									continue;
								}

								///拷贝mp3
								//**
								$dir->copyFile($mp3 , $mp3Target,true);
								//*/
							// }



							$points = str_replace(array('M','Z','L'),'',$points);
							//echo $points;
							$pointsArr = explode(' ', $points);
							//echo '页码：'.$pagenum.'点数：'.count($pointsArr).'<br>';
							//暂时发现最多有19个点
							//循环每个点
							foreach($pointsArr as $kk=>$v){
								$tmpArr = explode(',',$v);
								$tmp1 = $tmpArr[0];
								$tmp2 = $tmpArr[1];

								//echo round(460/1110*$tmp1).'/'.round(650/1570*$tmp2).'<br>';
								//循环往数据库插入每个点的坐标

								//根据单双页左，来判断右边半页的坐标
								// if($pageflag == 0){
								// 	//偶数在左
								// 	if((int)$pagenum%2==0){

								// 	}else{
								// 		$tmp1 = $tmp1+460;
								// 	}
								// }else{
								// 	//奇数在左
								// 	if((int)$pagenum%2==0){
								// 		$tmp1 = $tmp1 + 460;
								// 	}else{

								// 	}
								// }

								// $pageHotData['px'.$kk] = round(460/1110*$tmp1);
								// $pageHotData['py'.$kk] = round(650/1570*$tmp2);

								//$plist .= round(460/1110*$tmp1).','.round(650/1570*$tmp2).'|';
								$plist .= round($tmp1).','.round($tmp2).'|';
							}

							$m_xunfei = M('db_ebook.book_page_hot','t_');
							$pageHotData['bookid'] = $bookid;
							$pageHotData['pagenum'] = $pagenum;
							$pageHotData['plist'] = rtrim($plist,'|');
							$pageHotData['type'] = 1;
							$pageHotData['vbeg'] = "00:00:000";
							$pageHotData['vend'] = "00:00:000";
							// $pageHotData['vbeg'] = $arrMp3Info[$hotArr[1][1]]['beg'];
							// $pageHotData['vend'] = $arrMp3Info[$hotArr[1][1]]['end'];
							//$pageHotData['urlname'] = '/xunfeimp3/'.$bookid.'/'.$pagenum.'/'.$hotArr[1][1].'.mp3';
							$pageHotData['urlname'] = $mp3str;
							$pageHotData['hotmp3'] = ltrim($mp3str,"/");
							$pageHotData['iscut'] = 1;

							//var_dump($pageHotData);continue;

							// if(!empty($arrMp3Info[$hotArr[1][1]])){
							// 	//**
							// 	$m_xunfei->add($pageHotData);
							// 	//*/
							// }
							$m_xunfei->add($pageHotData);

						}
					}
				}



			}//exit;//一本书结束
		}









	}






}