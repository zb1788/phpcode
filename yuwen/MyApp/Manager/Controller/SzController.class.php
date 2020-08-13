<?php
namespace Manager\Controller;
use Think\Controller;

/**
* 中心库数据导入控制器 去掉高中版本 2016.02.14
*
* @author         gm
* @since          1.0  2016-01-18
*/
class SzController extends Controller {


	public function getSzHtml(){
		set_time_limit(0);

        $id = I('id/d',0);
        $nianji = I('nianji/s','');
        $xueqi = I('xueqi/s','');
        $banben = I('banben/d',0);


		$m = M();

		//课文同步
		$kecheng = M('kecheng');
        if($id == 0){
            //一本书
            $data = $kecheng->where('nianji="%s" and xueqi="%s" and versionid="%d"',$nianji,$xueqi,$banben)->order('nianji,xueqi,versionid,sortid')->select();
        }else{
            //单个章节
            $data = $kecheng->where('id="%d"',$id)->select();
        }
		// $data = $kecheng->where('nianji="四年级" and xueqi="上学期" and versionid=9')->order('nianji,xueqi,versionid,sortid')->select();

		Vendor("fileDirUtil");
		$dir = new \fileDirUtil();

		foreach($data as $k=>$v){

			$json = "";
			$json2 = "";

			$html = '';

			$nianji = $v['nianji'];
			$xueqi = $v['xueqi'];
			$banben = $this->getVersionName($v['versionid']);
			$kname = $v['kecheng'];


			$nianjiGbk = iconv('utf-8', 'gbk', $nianji);
			$xueqiGbk = iconv('utf-8', 'gbk', $xueqi);
			$banbenGbk = iconv('utf-8', 'gbk', $banben);
			$knameGbk = iconv('utf-8','gbk',$kname);

			if(!is_dir('szT/result/'.$nianjiGbk)){
				mkdir('szT/result/'.$nianjiGbk);
			}

			if(!is_dir('szT/result/'.$nianjiGbk.'/'.$xueqiGbk)){
				mkdir('szT/result/'.$nianjiGbk.'/'.$xueqiGbk);
			}

			if(!is_dir('szT/result/'.$nianjiGbk.'/'.$xueqiGbk.'/'.$banbenGbk)){
				mkdir('szT/result/'.$nianjiGbk.'/'.$xueqiGbk.'/'.$banbenGbk);
			}

			$todir = 'szT/result/'.$nianjiGbk.'/'.$xueqiGbk.'/'.$banbenGbk.'/'.$knameGbk;

			if(!is_dir($todir)){
				mkdir($todir);
			}else{
                $dir->unlinkDir($todir);
                mkdir($todir);
            }
			$dir->copyDir('./szT/sz',$todir,true);


			$sql='SELECT m.zid,m.ziyinid,n.zi,n.pianpang,n.bihuashu,n.shizi,n.xiezi FROM yw_kecheng_hanzi m,yw_zi n WHERE m.zid=n.id AND m.kechengid="%d" ORDER BY sortid';
    		$data_hanzi=$m->query($sql,$v['id']);
    		// $data_hanzi=$m->query($sql,5882);

    		foreach($data_hanzi as $kk=>$vv){
    			$zid = $vv['zid'];
    			$ziyinid = $vv['ziyinid'];
    			$zi = $vv['zi'];

    			$ziGbk = iconv('utf-8', 'gbk', $zi);

    			if(file_exists('./szT/zigif/'.$ziGbk.'.gif')){
    				copy('./szT/zigif/'.$ziGbk.'.gif',$todir.'/'.$ziGbk.'.gif');
    			}else{
    				file_put_contents('sz.txt', 'no gif|'.$zi);
    			}


    			$arr = $this->queryHanziInfo($zid,$ziyinid,$todir);

    			$json .= "var base_".$zid."_".$ziyinid." = ".json_encode($arr['duoyin']).";\r\n";

    			if($kk == 0){
    				$html .= '<a name="hanzi" onclick="hanziClick(this);" zid="'.$zid.'" ziyinid="'.$ziyinid.'" class="on"><b>'.$ziGbk.'</b></a>';
    			}else{
    				$html .= '<a name="hanzi" onclick="hanziClick(this);" zid="'.$zid.'" ziyinid="'.$ziyinid.'" ><b>'.$ziGbk.'</b></a>';
    			}

	    		foreach($arr['json2'] as $kkk=>$vvv){
	    			$json2 .= "var duoyin_".$vvv['id']." = ".json_encode($vvv).";\r\n";
	    		}
    		}


    		// var_dump($json2);
    		// var_dump($json);
    		// var_dump($html);

			$indexHtml = $todir.'/index.html';
			//写入AS文件
			$as = $dir->readsFile($indexHtml);

			$as = str_replace('$$name$$', $knameGbk, $as);//标题
			$as = str_replace('$$html$$', $html, $as);//标题
			$as = str_replace('$$json$$', $json, $as);//标题
			$as = str_replace('$$json2$$', $json2, $as);//标题

			$dir->writeFile($indexHtml, $as);
    		// exit;
    		// echo $kname.'OK<br>';
		    // ob_flush();
		    // flush();
            if($id !==0){
                Vendor("PHPZip");
                $Zip = new \PHPZip();
                $zifile = './szT/result/' . $id . '.zip';
                if(file_exists($zifile)){
                    $dir->unlinkDir($zifile);
                }

                $Zip->Zip($todir, $zifile);

                $this->download ($zifile, ' ' . $nianji.'-'.$banben.'-'.$xueqi.'-'.str_replace('·', '', $kname) . '.zip');
                exit;
            }
		}

        Vendor("PHPZip");
        $Zip = new \PHPZip();
        $zifile = './szT/result/' . $id . '.zip';
        if(file_exists($zifile)){
            $dir->unlinkDir($zifile);
        }
        $Zip->Zip('szT/result/'.$nianjiGbk.'/'.$xueqiGbk.'/'.$banbenGbk, $zifile);
        $this->download ($zifile, ' ' . $nianji.'-'.$banben.'-'.$xueqi . '.zip');

	}

    public function queryHanziInfo($zid,$ziyinid,$todir){

    	$m=M();
    	$sql='SELECT n.id,m.pianpang,m.bihuashu,m.shizi,m.xiezi,n.pinyin,n.wav,n.zaoju1,n.zaoju2,n.zaoju3,n.zuci1,n.zuci2,n.zuci3,n.ziyi1,n.ziyi2,n.ziyi3 FROM yw_zi m,yw_ziyin n WHERE m.id=n.zid AND n.id="%d"  AND m.id="%d"';
    	$data=$m->query($sql,$ziyinid,$zid);

    	$mp3 = str_replace('.wav','.mp3',basename($data[0]['wav']));

		if(file_exists('./szT/audio/'.$mp3)){
			copy('./szT/audio/'.$mp3,$todir.'/'.$mp3);
		}else{
			file_put_contents('sz.txt', 'no audio|'.$mp3);
		}



    	$tmp=$this->checkDuoyin($zid, $ziyinid,$todir);

    	$duoyin['duoyin']=$tmp['data'];
    	$duoyin['normal']=$data;

    	$str2 = $this->queryDuoyin($zid);

    	$arr['duoyin'] = $duoyin;
    	$arr['json2'] = $str2;
    	return $arr;
    }

    /**
     * 测试是否多音字
     */
    private function checkDuoyin($zid,$ziyinid,$todir){
    	$sql='SELECT id,pinyin,wav FROM yw_ziyin WHERE id<>"%d" AND zid="%d"';
    	$m=M();
    	$data=$m->query($sql,$ziyinid,$zid);

    	if(!empty($data)){
    		$arr['zid'] = $zid;
    		foreach($data as $v){
	    		$mp3 = str_replace('.wav','.mp3',basename($v['wav']));
	    		if(file_exists('./szT/audio/'.$mp3)){
					copy('./szT/audio/'.$mp3,$todir.'/'.$mp3);
				}else{
					file_put_contents('sz.txt', 'no audio|'.$mp3);
				}
    		}
    	}

    	$arr['data'] = $data;
    	return  $arr;
    }


    /**
     * 查询多音字信息
     */
    public function queryDuoyin($zid){
    	$m=M('ziyin');
    	$data=$m->where('zid="%d"',$zid)->field('id,pinyin,zaoju1,zaoju2,zaoju3,zuci1,zuci2,zuci3,ziyi1,ziyi2,ziyi3')->select();

    	return $data;
    }

	public function getVersionName($id){
		if($id == 2){
			$name = '人教版';
		}else if($id == 3){
			$name = '冀教版';
		}else if($id == 4){
			$name = '北京出版社';
		}else if($id == 5){
			$name = '北师大版';
		}else if($id == 7){
			$name = '山东教育出版社';
		}else if($id == 8){
			$name = '教科版（五四制、六三制）';
		}else if($id == 9){
			$name = '教科版（五四制）';
		}else if($id == 10){
			$name = '教科版（六三制）';
		}else if($id == 12){
			$name = '湖北教育出版社';
		}else if($id == 13){
			$name = '湖南教育出版社';
		}else if($id == 14){
			$name = '苏教版';
		}else if($id == 15){
			$name = '语文A版';
		}else if($id == 16){
			$name = '语文S版';
		}else if($id == 17){
			$name = '长春出版社';
		}else if($id == 18){
			$name = '西师大版';
		}
		return $name;
	}


    /**
     * 可以指定下载显示的文件名，并自动发送相应的Header信息
     * 如果指定了content参数，则下载该参数的内容
     * @static
     * @access protected
     * @param string $filename 下载文件名
     * @param string $showname 下载显示的文件名
     * @param integer $expire  下载内容浏览器缓存时间
     * @return void
     * @throws ThinkExecption
     */
    protected  function download ($filename, $showname='',$expire=180) {
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
        $showname = basename($showname);
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
        header("Content-Disposition: attachment; filename=". iconv("UTF-8","gb2312",$showname));
        header("Content-Length: ".$length);
        header("Content-type: ".$type);
        header('Content-Encoding: none');
        header("Content-Transfer-Encoding: binary" );
        ob_clean();
        readfile($filename);
        exit();
    }








}