<?php
namespace Manager\Controller;
use Think\Controller;

class YueduController extends Controller {
    public function index(){
    	$this->display();
	}
    public function indexDel(){
        $this->display();
    }
	/**
	 * 添加课程页面
	 */
	public function addkecheng(){
		$Model_group=M('music_group','yw_');
		$data_group=$Model_group->field('id,title')->select();
		$this->assign('music_group',$data_group);
		$this->display();
	}


	/**
	 * 编辑课程页面
	 */
	public function editkecheng(){
		$id=I('id/d',0);
		$this->assign('id',$id);
		$Model_group=M('music_group','yw_');
		$data_group=$Model_group->field('id,title')->select();
		$this->assign('music_group',$data_group);

		$Model_genre=M('genre','yd_');
		$data_genre=$Model_genre->field('id,name')->select();
		$this->assign('genre_group',$data_genre);

		$Model_cont=M('con','yd_');
		$data_cont=$Model_cont->field('id,name')->select();
		$this->assign('cont_group',$data_cont);
		/*
		$Modle_kecheng=M('text_kecheng');
		$data_kecheng=$Modle_kecheng->where('id="%d"',$id)->find();
		$this->assign('kecheng_info',$data_kecheng);
		*/
		$m = M();
		$sql = 'select * from yd_kecheng where id='.$id;
		$data = $m->query($sql);

		foreach ($data[0] as $key => $value) {
			$data_kecheng[$key] = $value;
		}
		$this->assign('kecheng_info',$data_kecheng);

		//var_dump($data_kecheng);exit;

		$Model_music_info=M('music_info','yw_');
		$data_music_info=$Model_music_info->where('id="%d"',$data_kecheng['musicid'])->field('groupid')->find();
		$this->assign('groupid_now',$data_music_info['groupid']);
		$this->assign('genre_now',$data_kecheng['genreid']);
		$this->assign('cont_now',$data_kecheng['contid']);

		$this->display();
	}


	/**
	 * 查询当前背景下的音乐
	 */
	public function queryMusic(){
		$groupid=I('music_group_id/d',0);
		$Model=M('music_info','yw_');
		$data=$Model->where('groupid="%d"',$groupid)->field('id,music_name')->select();
		$this->ajaxReturn($data);
	}
	/**
	 * 添加课程信息到数据库
	 */
	public function addKechengInfo(){

		$isadd = I('isadd/d',0);
		$moban = I('moban/s',0);
		$kecheng=I('kecheng/s','');
		$author=I('author/s','');
		$genre=I('genre/d',0);
		$cont=I('cont/d',0);
		$keywords=I('keywords/s','');

		$music_file=I('music_file/d',0);

		$Model=M('kecheng','yd_');

		if($isadd == 0){

			$re=$Model->where('kecheng="%s"',$kecheng)->find();
			//现在不判断()
			//$re = '';
			if(is_array($re)){
				echo '已存在';
			}else{

				// $data['ks_code'] = $kecheng;
				$data['kecheng'] = $kecheng;
				$data['musicid']=$music_file;
				$data['author']=$author;
				$data['genreid']=$genre;
				$data['contid']=$cont;
				$data['moban']=$moban;
				$data['keyword']=$keywords;

				$Model->add($data);

			}
		}else{
			$data['kecheng'] = $kecheng;
			$data['musicid']=$music_file;
			$data['author']=$author;
			$data['genreid']=$genre;
			$data['contid']=$cont;
			$data['moban']=$moban;
			$data['keyword']=$keywords;
			$Model->add($data);
		}

	}

	/**
	 * 编辑课程信息写入数据库
	 */
	public function updateKechengInfo(){
		$id=I('id/d',0);

		$moban = I('moban/s',0);
		$kecheng=I('kecheng/s','');
		$author=I('author/s','');
		$genre=I('genre/d',0);
		$cont=I('cont/d',0);
		$keywords=I('keywords/s','');
		$music_file=I('music_file/d',0);

		$Model=M('kecheng','yd_');


		$data['kecheng'] = $kecheng;
		$data['musicid']=$music_file;
		$data['author']=$author;
		$data['genreid']=$genre;
		$data['contid']=$cont;
		$data['moban']=$moban;
		$data['keyword']=$keywords;

		$Model->where('id="%d"',$id)->save($data);

		echo $kecheng;
	}

	/**
	 * 分页展示课程信息
	 */
	public function fenye(){
		$pageCurrent=I('pageCurrent/d',0);
		$page_size=I('page_size/d',0);
		$author=I('author/s','');
		$keywords=I('keywords/s','');
		$genre=I('genre/s','');
		$cont=I('cont/s','');
		$kecheng=I('kecheng/s','');
		$Model=M();

		// $gradeCode = $this->getGradeCode($grade);
		// $termCode = $term=='上学期'?'0001':'0002';

		$sql_where=' where 1=1 ';

		if($kecheng == ''){
			$sql_where .= '';
		}else{
			$sql_where .= ' and kecheng like "%'.$kecheng.'%"';
		}

		if($author == ''){
			$sql_where .= '';
		}else{
			$sql_where .= ' and author like "%'.$author.'%"';
		}

		if($keywords == ''){
			$sql_where .= '';
		}else{
			$sql_where .= ' and keyword like "%'.$keywords.'%"';
		}

		if($genre == 0){
			$sql_where .= '';
		}else{
			$sql_where .= ' and genreid ='.$genre;
		}

		if($cont == 0){
			$sql_where .= '';
		}else{
			$sql_where .= ' and contid ='.$cont;
		}

		$sql = 'select count(*) as num from yd_kecheng';
		$sql1 = 'select id,kecheng,author,(SELECT g.name FROM yd_genre g WHERE g.id = k.genreid) as gname,(select c.name from yd_con c where c.id = k.contid) as cname,keyword,moban,url from yd_kecheng k';


		$sql_limit=' limit '.($pageCurrent-1)*$page_size.','.$page_size;
		$sql_order=' order by id desc';

		$data_total=$Model->query($sql.$sql_where);

		$total=$data_total[0]['num'];
		$sub_pages=4;
		/* 实例化一个分页对象 */
		Vendor('SubPages');
		$subPages=new \SubPages($page_size,$total,$pageCurrent,$sub_pages);
		$page= $subPages->subPageCss3();
		$data=$Model->query($sql1.$sql_where.$sql_order.$sql_limit);
		//echo $sql1.$sql_where.$sql_order.$sql_limit;
		$word[$page]=$data;
		$this->ajaxReturn($word);
	}
    /**
     * 导入flash写入数据库
     */
    public function  importFlashData(){
    	$id=I('id/d');
    	$url=I('url/s');
    	$Model=M('kecheng','yd_');
    	$da=$Model->where('id="%d"',$id)->field('url')->find();
    	$data['url']=$url;

    	$Model->where('id="%d"',$id)->save($data);

    }



	/**
	 * 删除课程
	 */
	public function delkecheng(){
		$id=I('id/d',0);
		//删除课程主表
		$Model=M('kecheng','yd_');
		$Model->where('id="%d"',$id)->delete();

		$Model->delete($id);
	}

	/**
	 * 复制课程
	 */
	public function copyKecheng(){
		$id=I('id/d',0);
		$Model_kecheng=M('kecheng','yd_');
		$data_kecheng_info=$Model_kecheng->where('id="%d"',$id)->find();

		$data_kecheng_info['kecheng']=$data_kecheng_info['kecheng'].'_复制';
		$nid=$Model_kecheng->add($data_kecheng_info);


	}

	/**
	 * 下载已经上传的flash
	 * @return [type] [description]
	 */
	public function getUpFlash(){
		$id=I('id/d',0);
		$Model_kecheng=M('kecheng','yd_');
		$data=$Model_kecheng->where('id="%d"',$id)->find();
		$filename = $data['kecheng'];
		$url = $data['url'];
		// $filename = iconv('utf-8','gbk',$filename);
		$this->download($url,' '.$filename.'.swf');
	}


	/**
	 * 课程内容管理页面
	 */
	public function kechenginfo(){
		$id=I('id/d',0);
		$Model_kecheng=M('kecheng','yd_');
		$data_kecheng=$Model_kecheng->where('id="%d"',$id)->find();

		$this->assign('kecheng_id',$id);
		$this->assign('zhengwen',$data_kecheng['zhengwen']);
		$this->assign('daodu',$data_kecheng['daodu']);
		$this->display();
	}


	/**
	 * 保存课文内容和识记词汇内容
	 */
	public function addKewen(){
		$kecheng_id=I('kecheng_id/d',0);
		$zhengwen=I('zhengwen/s');
		$daodu=I('daodu/s');
		$Model=M('kecheng','yd_');
	//	echo htmlspecialchars_decode($tncontent);

		$data['zhengwen']=htmlspecialchars_decode($zhengwen);
		$data['daodu']=htmlspecialchars_decode($daodu);

		$Model->where('id="%d"',$kecheng_id)->save($data);
	}
    /**
     * flash导入页面
     */
    public function importFlash(){
    	$id=I('id/d');
    	$this->assign('id',$id);
    	$this->display();
    }


	/**
	 * 下载课程
	 */
	public function flashdown(){

		$kechengid = I("kechengid/d",0);

		$Model_kecheng = M("kecheng",'yd_'); // 实例化User对象
		$data = $Model_kecheng ->where('id =%d',$kechengid)->find();
		if (empty($data['zhengwen'])) {
			exit();
		}
		else
		{
			// $kechengAuth = trim($data["kecheng"]);
			// list($kecheng,$auth) = explode('|', $kechengAuth);
            $kecheng = trim($data["kecheng"]);
            $auth = trim($data["author"]);
		}


        $template = $data['moban'];
        if (empty($template)) {
            exit('模版不能为空！');
        }


		Vendor("fileDirUtil");
		$dir = new \fileDirUtil();

		$todir ='./flash/yuedu/' . $kechengid;
		$dir->unlinkDir($todir);
		$dir->copyDir('./Template/'.$template,$todir,true);

		$Model_music_info=M('music_info','yw_');
		$data_music_info=$Model_music_info->where('id="%d"',$data['musicid'])->field('music_filepath')->find();

        $dir->createDir($todir.'/wav/');

		copy('uploads/'.$data_music_info['music_filepath'], $todir.'/wav/'.basename($data_music_info['music_filepath']));

		$asfile = $todir . '/com/Datas.as';


		//写入AS文件
		$as			= $dir->readsFile($asfile);

		$jsflfile	= $todir . '/run.jsfl';
		//写入AS文件
		$jsfl		= $dir->readsFile($jsflfile);


		$zhengwen	= $data['zhengwen'];
		$daodu		= $data['daodu'];


		$zhengwen	= str_replace("\n", "<br/>", $zhengwen);
		$daodu		= str_replace("\n", "<br/>", $daodu);


		$zhengwen	= str_replace('&nbsp;', "", $zhengwen);
		$zhengwen	= str_replace('&quot;', "'", $zhengwen);


		//var_dump($zhengwen);
		preg_match_all('/style="text-align: center;">(.*?)</',$zhengwen,$out);
		//var_dump($out);exit;
		if(empty($out[0])){
			//非古诗
			$zhengwen	= str_replace('<p><br/></p>', "", $zhengwen);
			$zhengwen	= str_replace('<p>', "", $zhengwen);
			$zhengwen	= str_replace('</p>', "<br/>", $zhengwen);
			$zhengwen   = "<font face='楷体_GB2312' size='34'><textformat indent='68' leading = '20'>".$zhengwen."</textformat></font>";
		}else{
			//古诗
			$zhengwen	= str_replace('style="text-align: center;"', "align='center'", $zhengwen);
			$zhengwen   = "<font face='楷体_GB2312' size='34'><textformat  leading = '20'>".$zhengwen."</textformat></font>";
		}







		$daodu		= str_replace('&nbsp;', "", $daodu);
		$daodu		= str_replace('&quot;', "'", $daodu);
		$daodu		= str_replace('"', "'", $daodu);
		$daodu		= str_replace('<p></p>', "", $daodu);

		$daodu      = "<font face='楷体_GB2312' size='20'><textformat indent='40'>".$daodu."</textformat></font>";

		if(!empty($auth)){
			$auth = '('.$auth.')';
		}
		$biaoti 	= "<p align='center'> <font face='楷体_GB2312' size='40'>".$kecheng."</font> <font face='楷体_GB2312' size='18'>".$auth."</font></p>";
		$as			= str_replace('$$biaoti$$', $biaoti, $as);//标题
		$as			= str_replace('$$zhengwen$$', $zhengwen, $as);//标题
		$as			= str_replace('$$daodu$$', $daodu, $as);//正文,不带断句


		$dir->writeFile($asfile, $as);


		$jsfl = str_replace('$$music1$$', basename($data_music_info['music_filepath']), $jsfl);

		$jsfl = str_replace('$$kecheng$$',$kecheng,$jsfl);
		$dir->writeFile($jsflfile, $jsfl);

		Vendor("PHPZip");
		$Zip = new \PHPZip();
		$zifile = './flash/yuedu/' . $kechengid . '.zip';
		$dir->unlinkFile($zifile);
		$Zip->Zip($todir, $zifile);
        $dir->unlinkDir($todir);

		$this->download ($zifile, ' ' . str_replace('·', '', $kecheng) . '.zip');
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
        unlink($filename);
		exit();
	}


	//添加体裁、修改体裁
	public function genreManage(){
		header('Content-Type:application/json; charset=utf-8');
		$title = I('title/s','');
        $bid =I('bid/d',0);
        $type = I('type/s','');

        $Model=M('genre','yd_');

        $data['name']=trim($title);


        if($type == 'add'){
            $re=$Model->where('name="%s"',$title)->find();
            if(!empty($re)){
                $info['errMsg'] = '名称已存在';
                $info['errCode'] = '0001';
                $info['id']	= 0;
            }else{
                $maxSortId = $Model->max('sortid');
                if(is_null($maxSortId)){
                    $data['sortid'] = 1;
                }else{
                    $data['sortid'] = $maxSortId+1;
                }
                $id=$Model->add($data);

                $info['errMsg'] = 'success';
                $info['errCode'] = '1000';
                $info['id']	= $id;
                $info['sortid'] = $maxSortId+1;
            }
        }elseif($type == 'edit'){
            $data['id'] = $bid;
            $Model->save($data);
            $info['errMsg'] = 'success';
            $info['errCode'] = '1000';
            $info['id']	= 0;
        }else{
            $info['errMsg'] = '参数错误';
            $info['errCode'] = '0002';
            $info['id']	= 0;
        }
        exit(json_encode($info));
	}


	//获取体裁信息
	public function getGenres(){
		$Model=M('genre','yd_');
		$data = $Model->order('sortid')->select();
		$this->ajaxReturn($data);
	}

	//删除体裁
	public function delGenre(){
		$id = I('id/d',0);
		$Model=M('genre','yd_');
		$Model->where('id="%d"',$id)->delete();
		$info['errMsg'] = 'success';
        $info['errCode'] = '1000';
        $this->ajaxReturn($info);
	}

	//更新排序
	public function updatesort(){
    	$sortsInfo = I('sortsInfo/s',0);
    	$sortsInfo = str_replace('&quot;', '"', $sortsInfo);

    	$type = I('type/s','');
    	if($type == 'genre'){
    		$Model = M('genre','yd_');
    	}elseif($type=='cont'){
    		$Model = M('con','yd_');
    	}else{
    		exit('error');
    	}


    	foreach (json_decode($sortsInfo,true) as $v){
    		$data['sortid']=$v['sortid'];
    		$Model->where('id="%d"',$v['id'])->save($data);
    	}
	}

	//添加体裁、修改体裁
	public function contManage(){
		header('Content-Type:application/json; charset=utf-8');
		$title = I('title/s','');
        $bid =I('bid/d',0);
        $type = I('type/s','');

        $Model=M('con','yd_');

        $data['name']=trim($title);


        if($type == 'add'){
            $re=$Model->where('name="%s"',$title)->find();
            if(!empty($re)){
                $info['errMsg'] = '名称已存在';
                $info['errCode'] = '0001';
                $info['id']	= 0;
            }else{
                $maxSortId = $Model->max('sortid');
                if(is_null($maxSortId)){
                    $data['sortid'] = 1;
                }else{
                    $data['sortid'] = $maxSortId+1;
                }
                $id=$Model->add($data);

                $info['errMsg'] = 'success';
                $info['errCode'] = '1000';
                $info['id']	= $id;
                $info['sortid'] = $maxSortId+1;
            }
        }elseif($type == 'edit'){
            $data['id'] = $bid;
            $Model->save($data);
            $info['errMsg'] = 'success';
            $info['errCode'] = '1000';
            $info['id']	= 0;
        }else{
            $info['errMsg'] = '参数错误';
            $info['errCode'] = '0002';
            $info['id']	= 0;
        }
        exit(json_encode($info));
	}


	//获取体裁信息
	public function getConts(){
		$Model=M('con','yd_');
		$data = $Model->order('sortid')->select();
		$this->ajaxReturn($data);
	}

	//删除体裁
	public function delCont(){
		$id = I('id/d',0);
		$Model=M('con','yd_');
		$Model->where('id="%d"',$id)->delete();
		$info['errMsg'] = 'success';
        $info['errCode'] = '1000';
        $this->ajaxReturn($info);
	}


    /**
     * 备注页面
     * @return [type] [description]
     */
    public function mark(){
        $id =I('id/d',0);
        $this->assign('id',$id);
        $this->display();
    }


    public function addmark(){
        $kid =I('kid/d',0);
        $id =I('id/d',0);
        $type = I('type/s','');
        $this->assign('kid',$kid);
        $this->assign('id',$id);
        $this->assign('type',$type);
        $this->display();
    }
    /**
     * 获取版本
     * @return [type] [description]
     */
    public function getBanben(){
        $m=M('ziversion','yw_');
        $data = $m->order('sortid')->select();
        //unset($data[4]);
        array_splice($data,4,1);
        $this->ajaxReturn($data);
    }

    /**
     * 写入数据
     */
    public function addRemark(){
        $grade = I('grade/s','');
        $term = I('term/s','');
        $version = I('version/s','');
        $chapter = I('chapter/s','');
        $kid = I('kid/d',0);
        $type = I('type/s','');
        $id = I('id/d',0);

        $m=M('remark','yd_');

        $data['grade'] = $grade;
        $data['term'] = $term;
        $data['version'] = $version;
        $data['chapter'] = $chapter;


        if($type=="add"){
            $data['kid'] = $kid;
            $m->add($data);
        }else{
            $m->where("id=%d",$id)->save($data);
        }

    }

    /**
     * 获取当前备注内容
     * @return [type] [description]
     */
    public function getNowRemark(){
        $id = I('id/d',0);
        $m = M('remark','yd_');
        $data = $m->where('id="%d"',$id)->find();
        $this->ajaxReturn($data);
    }



    /**
     * 分页展示课程信息
     */
    public function fenyeremark(){
        $pageCurrent=I('pageCurrent/d',0);
        $page_size=I('page_size/d',0);
        $kid=I('kid/s','');

        $Model=M();

        $sql = 'select count(*) as num from yd_remark where kid='.$kid;
        $sql1 = 'select * from yd_remark where kid='.$kid;


        $sql_limit=' limit '.($pageCurrent-1)*$page_size.','.$page_size;
        $sql_order=' order by id desc';

        $data_total=$Model->query($sql);

        $total=$data_total[0]['num'];
        $sub_pages=4;
        /* 实例化一个分页对象 */
        Vendor('SubPages');
        $subPages=new \SubPages($page_size,$total,$pageCurrent,$sub_pages);
        $page= $subPages->subPageCss3();
        $data=$Model->query($sql1.$sql_order.$sql_limit);
        //echo $sql1.$sql_where.$sql_order.$sql_limit;
        $word[$page]=$data;
        $this->ajaxReturn($word);
    }



    //删除备注
    public function delremark(){
        $id = I('id/d',0);
        $m = M('remark','yd_');
        $m->where('id="%d"',$id)->delete();
    }














}


