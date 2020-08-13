<?php
namespace Manager\Controller;
use Think\Controller;

/**
* 单词控制器
*
* @author         gm
* @since          1.0
*/
class KewenController extends CheckController {
	/**
	 * 背景音乐管理
	 */
	public function music(){
		$Model=M('text_music_group');
		$data=$Model->field('id,title')->select();
		$this->assign('music_type',$data);
		$Model_music_info=M();
		$sql="SELECT yp.title,yo.id,yo.music_name,yo.music_filename,yo.music_filepath FROM cn_text_music_info yo,cn_text_music_group yp WHERE yo.groupid=yp.id";
		$data_info=$Model_music_info->query($sql);
		$this->assign('music_info',$data_info);
		$this->display();
	}
	/**
	 * 背景管理
	 */
	public function backInfos(){
		$Model=M('text_music_group');
		$data=$Model->field('id,title')->select();
		$this->assign('music_type',$data);
		$this->display();
	}
	/**
	 * 背景添加
	 */
	public function addmusicbg(){
		$title=I('title/s','');
		$Model=M('text_music_group');
		$num=$Model->where('title="%s"',$title)->find();
		//var_dump($num);
		if(is_array($num)){
			echo '已存在';
		}else{
			$data['title']=$title;
			$id=$Model->add($data);
			echo $id;
		}
	}
	/**
	 * 背景编辑
	 */
	public function editmusicbg(){
		$id=I('id/d',0);
		$title_new=I('title_new/s','');
		$Model=M('text_music_group');
		$data['title']=$title_new;
		$Model->where('id="%d"',$id)->save($data);
	}
	/**
	 * 背景删除
	 */
	public function delmusicbg(){
		$id=I('id/d',0);
		$Model_music_info=M('text_music_info');
		$Model_music_info->where('groupid="%d"',$id)->delete();
		$Model=M('text_music_group');
		$data=$Model->where('id="%d"',$id)->delete();
		echo $data;
	}
	/**
	 * 添加音乐页面
	 */
	public function addMusicFiles(){
		$Model=M('text_music_group');
		$data=$Model->field('id,title')->select();
		$this->assign('music_type',$data);
		$this->display();
	}
	/**
	 * 添加音乐
	 */
	public function addFile(){
		$groupid=I('groupid/d',0);
		$musicname=I('musicname/s','');
		$filename=I('filename/s','');
		$filepath=I('filepath/s','');
		$Model=M('text_music_info');
		$re=$Model->where('music_name="%s"',$musicname)->find();
		if(is_array($re)){
			echo '音乐名称已存在';
		}else{
			$data['groupid']=$groupid;
			$data['music_name']=$musicname;
			$data['music_filename']=$filename;
			$data['music_filepath']=$filepath;
			$id=$Model->add($data);
			echo $id;
		}
	}
	/**
	 * 编辑音乐界面
	 */
	public function editMusicFiles(){
		$id=I('id/d',0);
		$this->assign('id',$id);
		$Modle=M('text_music_info');
		$data=$Modle->where('id="%d"',$id)->find();
		$this->assign('music_info',$data);
		$Model_group=M('text_music_group');
		$data_group=$Model_group->field('id,title')->select();
		$this->assign('music_type',$data_group);
		$this->display();
	}
	/**
	 * 编辑音乐内容保存
	 */
	public function editFile(){
		$id=I('id/d',0);
		$groupid=I('groupid/d',0);
		$musicname=I('musicname/s','');
		$filename=I('filename/s','');
		$filepath=I('filepath/s','');
		$Model=M('text_music_info');
		if ($filename==''){
			$data['groupid']=$groupid;
			$data['music_name']=$musicname;
			$Model->where('id="%d"',$id)->save($data);
		}else{
			$data['groupid']=$groupid;
			$data['music_name']=$musicname;
			$data['music_filename']=$filename;
			$data['music_filepath']=$filepath;
			$Model->where('id="%d"',$id)->save($data);
		}
	}
	/**
	 * 删除音频
	 */
	public function delmusicfile(){
		$id=I('id/d',0);//music_info表的id
		$Model=M('text_music_info');
		$data=$Model->where('id="%d"',$id)->field('music_filepath')->find();
		unlink('uploads/'.$data['music_filepath']);
		$Model->delete($id);

	}
	/**
	 * 切换音乐背景列出当前背景下的音乐
	 */
	public function changeType(){
		$groupid=I('groupid/d',0);
		$Model=M();
		if($groupid==0){
			$sql="SELECT yp.title,yo.id,yo.music_name,yo.music_filename,yo.music_filepath FROM cn_text_music_info yo,cn_text_music_group yp WHERE yo.groupid=yp.id";
			$data=$Model->query($sql);
		}else{
			$sql="SELECT yp.title,yo.id,yo.music_name,yo.music_filename,yo.music_filepath FROM cn_text_music_info yo,cn_text_music_group yp WHERE yo.groupid=yp.id and yo.groupid='%d'";
			$data=$Model->query($sql,$groupid);
		}
		$this->ajaxReturn($data);
	}
	/**
	 * 播放mp3
	 */
	public function playMp3(){
		$filename=I('filename/s',0);
		$filepath=I('filepath/s',0);
		//$filepath='uploads/'.$filepath;
		$this->assign('filename',$filename);
		$this->assign('filepath',$filepath);
		$this->display();
	}
	/**
	 * 课程管理页面
	 */
	public function kecheng(){
		$this->display();
	}

	/**
	 * 查询课程版本
	 * @return [type] [description]
	 */
	public function kcbanben(){
		$nianji = I("nianji/s");
		$xueqi = I("xueqi/s");

		$gradeCode = $this->getGradeCode($nianji);
		$termCode = $xueqi=='上学期'?'0001':'0002';

		$m = M();
		$sql = "select DISTINCT t.r_version,q.detail_name FROM cn_text_kecheng l LEFT JOIN cn_rms_unit t ON l.ks_code=t.ks_code ,cn_rms_dictionary q WHERE t.r_version=q.detail_code AND q.dictionary_code='edition' AND  t.r_grade='".$gradeCode."' and t.r_volume='".$termCode."' AND t.r_subject='0001' ";
		$data = $m->query($sql);

		$this->ajaxReturn($data);
	}

	/**
	 * 查询已有课程单元
	 * @return [type] [description]
	 */
	public function kckecheng(){
		$nianji = I("nianji/s");
		$xueqi = I("xueqi/s");
		$versionid = I("versionid/s");

		$gradeCode = $this->getGradeCode($nianji);
		$termCode = $xueqi=='上学期'?'0001':'0002';


		$m = M();
		$sql = "select l.id,t.ks_name FROM cn_text_kecheng l LEFT JOIN cn_rms_unit t ON l.ks_code=t.ks_code WHERE   t.r_grade='".$gradeCode."' and t.r_volume='".$termCode."' AND t.r_subject='0001' AND t.r_version='".$versionid."' order by t.display_order";

		$data = $m->query($sql);

		$this->ajaxReturn($data);
	}


	public function kallKecheng(){
		$nianji = I('nianji/s');
		$xueqi = I("xueqi/s");
		$versionid = I("versionid/s");

		$gradeCode = $this->getGradeCode($nianji);
		$termCode = $xueqi=='上学期'?'0001':'0002';
		$sql = "select ks_name,ks_code from cn_rms_unit where r_grade='".$gradeCode."' and r_volume='".$termCode."' and r_subject='0001' and r_version='".$versionid."' order by display_order";
		$m = M();
		$data = $m->query($sql);
		$this->ajaxReturn($data);
	}


	public function getGradeCode($gradeName){
		switch($gradeName){
			case '一年级':
				return '0001';
				break;
			case '二年级':
				return  '0002';
				break;
			case '三年级':
				return  '0003';
				break;
			case '四年级':
				return  '0004';
				break;
			case '五年级':
				return  '0005';
				break;
			case '六年级':
				return  '0006';
				break;
		}
	}


	/**
	 * 添加课程页面
	 */
	public function addkecheng(){
		$Model_group=M('text_music_group');
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
		$Model_group=M('text_music_group');
		$data_group=$Model_group->field('id,title')->select();
		$this->assign('music_group',$data_group);

		/*
		$Modle_kecheng=M('text_kecheng');
		$data_kecheng=$Modle_kecheng->where('id="%d"',$id)->find();
		$this->assign('kecheng_info',$data_kecheng);
		*/
		$m = M();
		$sql = 'select l.*,(select p.detail_name from cn_rms_dictionary p where p.detail_code=t.r_grade and p.dictionary_code="grade") as r_grade,(select p.detail_name from cn_rms_dictionary p where p.detail_code=t.r_volume and p.dictionary_code="volume") as r_volume,t.r_version from cn_text_kecheng l,cn_rms_unit t where l.ks_code=t.ks_code and l.id='.$id;
		$data = $m->query($sql);

		foreach ($data[0] as $key => $value) {
			$data_kecheng[$key] = $value;
		}
		$this->assign('kecheng_info',$data_kecheng);

		//var_dump($data_kecheng);exit;

		$Model_music_info=M('text_music_info');
		$data_music_info=$Model_music_info->where('id="%d"',$data_kecheng['musicid'])->field('groupid')->find();
		$this->assign('groupid_now',$data_music_info['groupid']);
		$this->display();
	}
	/**
	 * 查询当前背景下的音乐
	 */
	public function queryMusic(){
		$groupid=I('music_group_id/d',0);
		$Model=M('text_music_info');
		$data=$Model->where('groupid="%d"',$groupid)->field('id,music_name')->select();
		$this->ajaxReturn($data);
	}
	/**
	 * 添加课程信息到数据库
	 */
	public function addKechengInfo(){
		$grade=I('grade/s','');
		$term=I('term/s','');
		$version=I('version/s','');
		$kecheng=I('kecheng/s','');
		$bieming = I('bieming/s');
		//$music=I('music/d',0);
		$music_file=I('music_file/d',0);
		$moban=I('moban/d',0);
		$filepath=I('filepath/s','');
		$filename=I('filename/s','');
		$types=I('types/d',0);
		echo $types;
		//var_dump($types);
		$Model=M('text_kecheng');
		/**
		 * 添加前判断课程是否存在
		 */
		$re=$Model->where('nianji="%s" and xueqi="%s" and banben="%s" and kecheng="%s"',$grade,$term,$version,$kecheng)->find();
		//现在不判断()
		$re = '';
		if(is_array($re)){
			echo '已存在';
		}else{
			/*
			$data['nianji']=$grade;
			$data['xueqi']=$term;
			$data['banben']=$version;
			$data['kecheng']=$kecheng;
			$data['musicid']=$music_file;
			$data['mobanid']=$moban;
			$data['filepath']=$filepath;
			$data['filename']=$filename;
			$data['types']=$types;
			$Model->add($data);
			*/
			$data['ks_code'] = $kecheng;
			$data['title'] = $bieming;
			$data['musicid']=$music_file;
			$data['mobanid']=$moban;
			$data['filepath']=$filepath;
			$data['filename']=$filename;
			$data['types']=$types;
			$Model->add($data);

		}
	}
	/**
	 * 编辑课程信息写入数据库
	 */
	public function updateKechengInfo(){
		$id=I('id/d',0);
		$grade=I('grade/s','');
		$term=I('term/s','');
		$version=I('version/s','');
		$banben = I('banben/s');
		$kecheng=I('kecheng/s','');
		$unit = I('unit/s');
		$bieming = I('bieming/s');
		$music_file=I('music_file/d',0);
		$filepath=I('filepath/s','');
		$filename=I('filename/s','');
		$mobanid=I('moban/d',0);
		$types=I('types/d',0);
		$Model=M('text_kecheng');
		if ($filepath==''){
			/*
			$data['nianji']=$grade;
			$data['xueqi']=$term;
			$data['banben']=$version;
			$data['kecheng']=$kecheng;
			$data['musicid']=$music_file;
			$data['mobanid']=$mobanid;
			$data['types']=$types;
			*/
			$data['ks_code'] = $kecheng;
			$data['title'] = $bieming;
			$data['musicid']=$music_file;
			$data['mobanid']=$mobanid;
			$data['types']=$types;
			$Model->where('id="%d"',$id)->save($data);
		}else{
			/*
			$data['nianji']=$grade;
			$data['xueqi']=$term;
			$data['banben']=$version;
			$data['kecheng']=$kecheng;
			$data['musicid']=$music_file;
			$data['filepath']=$filepath;
			$data['filename']=$filename;
			$data['mobanid']=$mobanid;
			$data['types']=$types;
			*/
			$data['ks_code'] = $kecheng;
			$data['title'] = $bieming;
			$data['musicid']=$music_file;
			$data['filepath']=$filepath;
			$data['filename']=$filename;
			$data['mobanid']=$mobanid;
			$data['types']=$types;
			$Model->where('id="%d"',$id)->save($data);
		}
		echo $grade.'|'.$term.'|'.$banben.'|'.$unit.'|'.$mobanid.'|'.$types;
	}
	/**
	 * 复制课程
	 */
	public function copyKecheng(){
		$id=I('id/d',0);
		$Model_kecheng_info=M('text_kecheng');
		$data_kecheng_info=$Model_kecheng_info->where('id="%d"',$id)->find();
		$data['nianji']=$data_kecheng_info['nianji'];
		$data['xueqi']=$data_kecheng_info['xueqi'];
		$data['banben']=$data_kecheng_info['banben'];
		$data['kecheng']=$data_kecheng_info['kecheng'].'_复制';
		$data['musicid']=$data_kecheng_info['musicid'];
		$data['mobanid']=$data_kecheng_info['mobanid'];
		$data['filepath']=$data_kecheng_info['filepath'];
		$data['filename']=$data_kecheng_info['filename'];
		$data['types']=$data_kecheng_info['types'];
		$nid=$Model_kecheng_info->add($data);
		$Model=M();
		if ($data['mobanid']==2){
			$sql="INSERT INTO cn_text_guwen(kecheng_id,sentence,description) (SELECT '".$nid."',sentence,description FROM cn_text_guwen WHERE kecheng_id='".$id."');";
			$Model->execute($sql);
		}
		$sql1="INSERT INTO cn_text_wonderful(kecheng_id,section) (SELECT '".$nid."',section FROM cn_text_wonderful WHERE kecheng_id='".$id."')";
		$Model->execute($sql1);
		$sql2="INSERT INTO cn_text_keypoint(kecheng_id,step,content,tag,time) (SELECT '".$nid."',step,content,tag,time FROM cn_text_keypoint WHERE kecheng_id='".$id."')";
		$Model->execute($sql2);
		$sql3="INSERT INTO cn_text_kecheng_content(kecheng_id,text_content,words) (SELECT '".$nid."',text_content,words FROM cn_text_kecheng_content WHERE kecheng_id='".$id."')";
		$Model->execute($sql3);
		$sql4="INSERT INTO cn_text_word_info(kecheng_id,word,`explain`,sortid) (SELECT '".$nid."',word,`explain`,sortid FROM cn_text_word_info WHERE kecheng_id='".$id."')";
		$Model->execute($sql4);

	}
	/**
	 * 分页展示课程信息
	 */
	public function fenye(){
		$pageCurrent=I('pageCurrent/d',0);
		$page_size=I('page_size/d',0);
		$grade=I('grade/s','');
		$term=I('term/s','');
		$version=I('version/s','');
		$kecheng=I('kecheng/s','');
		$Model=M();

		$gradeCode = $this->getGradeCode($grade);
		$termCode = $term=='上学期'?'0001':'0002';

		$sql_where='where t.r_subject="0001"';
		if($grade==''){
			$sql_where.='';
		}else{
			//$sql_where.=' and nianji="'.$grade.'"';
			$sql_where.=' and t.r_grade="'.$gradeCode.'" ';
		}
		if($term==''){
			$sql_where.='';
		}else{
			//$sql_where.=' and xueqi="'.$term.'"';
			$sql_where.=' AND t.r_volume="'.$termCode.'" ';
		}
		if($version==''){
			$sql_where.='';
		}else{
			//$sql_where.=' and banben like "%'.$version.'%"';
			$sql_where.=' and t.r_version=(SELECT p.DETAIL_CODE FROM rms_dictionary_detail p WHERE p.detail_name="'.$version.'" and p.dictionary_code="edition") ';
		}
		if($kecheng==''){
			$sql_where.='';
		}else{
			$sql_where.=' and l.title like "%'.$kecheng.'%"';
		}
		//$sql='select count(*) as num from cn_text_kecheng ';
		//$sql1='select * from cn_text_kecheng ';


		$sql = 'SELECT count(l.id) as num FROM cn_text_kecheng l LEFT JOIN cn_rms_unit t ON l.ks_code=t.ks_code ';
		$sql1 = 'SELECT l.id,l.mobanid,l.types,l.filepath,t.ks_name as kecheng,(SELECT p.detail_name FROM rms_dictionary_detail p WHERE p.DETAIL_CODE=t.r_grade and  p.dictionary_code="grade") as nianji,(SELECT p.detail_name FROM rms_dictionary_detail p WHERE p.DETAIL_CODE=t.r_volume and  p.dictionary_code="volume") as xueqi,(SELECT p.detail_name FROM rms_dictionary_detail p WHERE p.DETAIL_CODE=t.r_version and  p.dictionary_code="edition") as banben FROM cn_text_kecheng l LEFT JOIN cn_rms_unit t ON l.ks_code=t.ks_code ';

		//echo $sql1;exit;
		$sql_limit=' limit '.($pageCurrent-1)*$page_size.','.$page_size;
		$sql_order=' order by l.id desc';
		//echo $sql.$sql_where.$sql_limit;exit();
		$data_total=$Model->query($sql.$sql_where);
		//var_dump($data_total);exit();
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
	 * 删除课程
	 */
	public function delkecheng(){
		$id=I('id/d',0);
		//删除课程主表
		$Model=M('text_kecheng');
		$data=$Model->where('id="%d"',$id)->field('filepath')->find();
		$Model->where('id="%d"',$id)->delete();
		//删除课程对应mp3文件
		unlink('upload/'.$data['filepath']);
		//删除cn_text_word_info表
		$Model_word_info=M('text_word_info');
		$Model_word_info->where('kecheng_id="%d"',$id)->delete();
		//删除text_kecheng_content表
		$Model_text_kecheng_content=M('text_kecheng_content');
		$Model_text_kecheng_content->where('kecheng_id="%d"',$id)->delete();
		//删除text_keypoint表
		$Model_step_key=M('text_keypoint');
		$Model_step_key->where('kecheng_id="%d"',$id)->delete();
		//删除text_wonderful表
		$Model_section_wonderful=M('text_wonderful');
		$Model_section_wonderful->where('kecheng_id="%d"',$id)->delete();
		//删除guwen表
		$Model_guwen=M('text_guwen');
		$Model_guwen->where('kecheng_id="%d"',$id)->delete();

		$Model->delete($id);
	}
	/**
	 * 下载课程
	 */
	public function flashdown(){
		// echo 'aa';
		// $str="车，前来接他的伯父指着一片繁华<a href='event:繁华：（城镇、街市）繁荣热闹。'><font color='#ff0000' size='12'>[2]</font></a>、热闹的地方";
		// $pattern = '/<a href=(.*?)a>/';
		// echo $str;
		// $aa = preg_replace($pattern,'',$str);echo $aa;exit;


		$kechengid = I("kechengid/d",0);

		$Model_kecheng = M("text_kecheng"); // 实例化User对象
		$data = $Model_kecheng ->where('id =%d',$kechengid)->find();
		if (empty($data)) {
			exit();
		}
		else
		{
			$nianji = $data["nianji"];
			$xueqi = $data["xueqi"];
			$banben = $data["banben"];
			$kecheng = $data["kecheng"];
		}

		$arr_template =  C('CONST_KEWENFLASH');
		if($data['mobanid']==1){
			$moban='现代文';
		}
		if($data['mobanid']==2){
			$moban='古文';
		}
		$template = $arr_template[$moban];
		if (empty($template)) {
			exit();
		}

		Vendor("fileDirUtil");
		$dir = new \fileDirUtil();

		$todir ='./flash/' . $kechengid;
		$dir->unlinkDir($todir);
		$dir->copyDir('./Template/'.$template,$todir,true);
		copy('uploads/'.$data['filepath'],$todir.'/wav/'.basename($data['filepath']));
		$dir->copyFile('uploads/'.$data['filepath'], $todir.'/wav/',true);

		$Model_music_info=M('text_music_info');
		$data_music_info=$Model_music_info->where('id="%d"',$data['musicid'])->field('music_filepath')->find();
		copy('uploads/'.$data_music_info['music_filepath'], $todir.'/wav/'.basename($data_music_info['music_filepath']));

		$asfile = $todir . '/Datas.as';
		//写入AS文件
		$as = $dir->readsFile($asfile);

		$jsflfile = $todir . '/run.jsfl';
		//写入AS文件
		$jsfl = $dir->readsFile($jsflfile);

// 		$writestring = '' . "\r\n			" ;
// 		$writestring .= 'classText.text = String("'.$nianji.'-'.$banben.'-' . $xueqi . '");' . "\r\n			" ;
// 		$writestring .= 'titleText.text = String("' . $kecheng . '");' . "\r\n			" ;
		$Model_text_kecheng_content=M('text_kecheng_content');
		$data_text_kecheng_content=$Model_text_kecheng_content->where('kecheng_id="%d"',$kechengid)->find();
		$text = $data_text_kecheng_content['text_content'];
		$Model_word_info=M('text_word_info');
		$data_word_info=$Model_word_info->where('kecheng_id="%d"',$kechengid)->select();
		$Model_step_key=M('text_keypoint');
		$data_step_key=$Model_step_key->where('kecheng_id="%d"',$kechengid)->select();
		$Model_section_won=M('text_wonderful');
		$data_section_won=$Model_section_won->where('kecheng_id="%d"',$kechengid)->select();
		$text=str_replace('&nbsp;', "", $text);
		$text=str_replace('&quot;', "'", $text);
		$text=str_replace('"', "'", $text);
		$text=str_replace('<p></p>', "", $text);

			//这个是遍历词语释意,就是现在的注释



			$zhushi = '';
			if($types==1||$types==2){
				$zhushi .= "public var zhushi:String =\"<Chapter><font color='#000000' size='34' face='宋体'><textformat indent='49'><p align='left'>";
			}else{
				$zhushi .= "public var zhushi:String =\"<Chapter><font color='#000000' size='28' face='宋体'><textformat indent='49'><p align='left'>";
			}

			foreach ($data_word_info as $v){
				$zhushi .= "<font size='20'>【".$v['sortid']."】</font>".$v['word']."：".str_replace(array($v['word'].'：',$v['word'].':'), '', $v['explain'])."<br>";
			}
			$zhushi .= "</textformat></font></Chapter>\";";


			//preg_match_all('/font-size: ([0-9]+)px/i',$text,$arr);
			//$size=$arr[1][0];
			//$size为字体大小
			//$text=ereg_replace("<span style='font-size: [0-9]+px;'>","<font size='".$size."'>",$text);
			$text=str_replace('span', 'font',$text);
			$text=str_replace("style='text-align: center;'","align='center'",$text);
			$text=str_replace("style='TEXT-ALIGN: center;'","align='center'",$text);
			$text=str_replace("style='font-size: ","size='",$text);
			$text=str_replace("px;'","'",$text);

			 $text=str_replace('<p><br/></p>','',$text);
			 $text=str_replace('<p><br></p>','',$text);
			 $text=str_replace('<p></br></p>','',$text);
			// $text=str_replace('<p>', '', $text);
			// $text=str_replace('</p>', '<br>', $text);

			 $text=str_replace("<p align='center'><br></p>","<p align='center'></p>",$text);
			 $text=str_replace("<p align='center'><br/></p>","<p align='center'></p>",$text);


			$biaoti = 'public var biaoti:String="'.$data['kecheng'].'";';

			$types=$data['types'];
			if ($types==1){
				//现代文
				//古文已经去掉，现代文保留

				$text_quanwen = preg_replace('/【(.*?)】/','',$text);//把【】标签替换
				foreach ($data_word_info as $v){
					//echo $v['sortid'].'|'.$v['word'];
					$xuhao='【'.$v['sortid'].'】';
					$replace="<font color='#ff0000' size='12'><a href='event:".$v['explain']."'>[".$v['sortid']."]</a></font>";
					//echo $xuhao.'<br/>';
					//echo $replace.'<br/>';
					$text=str_replace($xuhao, $replace, $text);
				}

				$zhengwen ="public var zhengwen:String=\"<font face='宋体' size='34'><p align='center'><b>".$data['kecheng']."</b></p><textformat indent='68'>".$text."<a href=''><font color='#ff0000' size='14'> </font></a></textformat></font>\";";
				//$text_quanwen = $text;
				// $pattern = '/<a href=(.*?)a>/';
				// $text_quanwen = preg_replace($pattern,'',$text);

			}elseif ($types==2){
				//现代诗

				// $text_ndj = str_replace('/' , '' , $text);//不带断句的正文
		  //   	$text_ndj = str_replace('<font>','</font>', $text_ndj);
				$text_quanwen = preg_replace('/【(.*?)】/','',$text);//把【】标签替换为空

				foreach ($data_word_info as $v){
					//echo $v['sortid'].'|'.$v['word'];
					$xuhao='【'.$v['sortid'].'】';
					$replace="<font color='#ff0000' size='12'><a href='event:".$v['explain']."'>[".$v['sortid']."]</a></font>";
					//echo $xuhao.'<br/>';
					//echo $replace.'<br/>';
					$text=str_replace($xuhao, $replace, $text);
					//echo $text;exit;
				}



				$zhengwen ="public var zhengwen:String=\"<font face='宋体' size='34'><p align='center'><b>".$data['kecheng']."</b></p>".$text."<a href=''><font color='#ff0000' size='14'> </font></a></font>\";";
				// $text_quanwen = $text_ndj;
				// $pattern = '/<a href=(.*?)a>/';
				// $text_quanwen = preg_replace($pattern,'',$text);

			}elseif ($types==3){
				//文言文

				$text_ndj = str_replace('</p>' , '<|p>' , $text);
				$text_ndj = str_replace('/' , '' , $text_ndj);//不带断句的正文
				$text_ndj = str_replace('<|p>' , '</p>' , $text_ndj);
		    	$text_ndj = str_replace('<font>','</font>', $text_ndj);

		    	$zhengwen = "public var zhengwen:String=\"<font face='宋体' size='30'><p align='left'><textformat indent='60'>".$text_ndj."<a href=''><font color='#ff0000' size='14'> </font></a></textformat></p></font>\";";
		    	$duanju = "public var duanju:String=\"<font face='宋体' size='30'><p align='left'><textformat indent='60'>".$text."<a href=''><font color='#ff0000' size='14'> </font></a></textformat></p></font>\";";
				$text_quanwen = $text_ndj;

			}else {
				//古诗
				$text_ndj = str_replace('</p>' , '<|p>' , $text);
				$text_ndj = str_replace('/' , '' , $text_ndj);//不带断句的正文
				$text_ndj = str_replace('<|p>' , '</p>' , $text_ndj);
		    	$text_ndj = str_replace('<font>','</font>', $text_ndj);

				$zhengwen = "public var zhengwen:String=\"<font face='宋体' size='36'>".$text_ndj."<a href=''><font color='#ff0000' size='14'> </font></a></font>\";";
				$duanju = "public var duanju:String=\"<font face='宋体' size='36'>".$text."<a href=''><font color='#ff0000' size='14'> </font></a></font>\";";
				$text_quanwen = $text_ndj;

			}



			//我来读内容，识记词汇和精彩语句标红
			$wolaidu = "";
			$wordstr = $data_text_kecheng_content['words'];

			$wordArr = explode('、' , $wordstr);

			//先替换句子的标签
			foreach($data_section_won as $v){
				$text_quanwen = str_replace(trim($v['section']), "<u>".trim($v['section'])."</u>", $text_quanwen);
			}
			//在替换词语的标签
			foreach($wordArr as $v){
				//$text_ndj = str_replace($v, "<font color='#FF0000'>".$v."</font>", $text_ndj);
				$text_quanwen = $this->str_replace_limit(trim($v), "<font color='#FF0000'>".trim($v)."</font>", $text_quanwen,1);
			}
			//var_dump($data_section_won);exit;
			if($types==1||$types==2){
				$wolaidu = "public var wolaidu:String=\"<font face='宋体' size='34'><p align='center'><b>".$data['kecheng']."</b></p><textformat indent='68'>".$text_quanwen."<a href=''><font color='#ff0000' size='14'> </font></a></textformat></font>\";";
			}else if($types==3){
				$wolaidu = "public var wolaidu:String=\"<font face='宋体' size='30'><p align='left'><textformat indent='60'>".$text_quanwen."<a href=''><font color='#ff0000' size='14'> </font></a></textformat></p></font>\";";
			}else{
				$wolaidu = "public var wolaidu:String=\"<font face='宋体' size='36'>".$text_quanwen."<a href=''><font color='#ff0000' size='14'> </font></a></font>\";";
			}

			//var_dump($wordArr);exit;

			//$writestring 标题和正文的内容

			//$zhongdiandu重点读的内容数组
			$zhongdiandu="";
			foreach ($data_step_key as $key=>$v){
				//echo $key.'|'.$v['tag'].'<br/>';
				$arr=explode('-', $v['time']);
				//$zhongdiandu.="zhongdianarr[".$key."][0]=\"<Chapter><font color='#DE4D4F' size='30' face='宋体'>原　文：</font><br><font color='#000000' size='24' face='宋体'><textformat indent='49'>".str_replace("\n", "", trim($v['content']))."</textformat></font><br><font color='#DE4D4F' size='30' face='宋体'>导　读：</font><br><font color='#000000' size='24' face='宋体'><textformat indent='49'>".str_replace("\n", "", trim($v['tag']))."</textformat></font></Chapter>\";\r\n			zhongdianarr[".$key."][1]=\"".$arr[0]."\";\r\n			zhongdianarr[".$key."][2]=\"".$arr[1]."\";\r\n			";
				if($types==1||$types==2){
					//现代文
					$zhongdiandu .= "zhongdianarr[".$key."][0] = \"<Chapter><font color='#000000' size='30' face='宋体'><b><textformat indent='60'>".str_replace("\n", "", trim($v['content']))."</textformat></b></font></Chapter>\";\r\n                ";
					$zhongdiandu .= "zhongdianarr[".$key."][1] = \"<Chapter><font color='#000000' size='30' face='宋体'><b><textformat indent='60'>".str_replace("\n", "", trim($v['tag']))."</textformat></b></font></Chapter>\";\r\n                ";
				}else if($types==3){
					$zhongdiandu .= "zhongdianarr[".$key."][0] = \"<Chapter><font color='#000000' size='28' face='宋体'><b><textformat indent='56'><p align='left'>".str_replace("\n", "", trim($v['content']))."</textformat></b></font></Chapter>\";\r\n                ";
					$zhongdiandu .= "zhongdianarr[".$key."][1] = \"<Chapter><font color='#000000' size='28' face='宋体'><b><textformat indent='56'><p align='left'>".str_replace("\n", "", trim($v['tag']))."</textformat></b></font></Chapter>\";\r\n                ";
				}else{
					$zhongdiandu .= "zhongdianarr[".$key."][0] = \"<Chapter><font color='#000000' size='28' face='宋体'><b><textformat indent='56'><p align='left'>".str_replace("\n", "", trim($v['content']))."</textformat></b></font></Chapter>\";\r\n                ";
					$zhongdiandu .= "zhongdianarr[".$key."][1] = \"<Chapter><font color='#000000' size='28' face='宋体'><b><textformat indent='56'><p align='left'>".str_replace("\n", "", trim($v['tag']))."</textformat></b></font></Chapter>\";\r\n                ";
				}

				$zhongdiandu .="zhongdianarr[".$key."][2] = \"".$arr[0]."\";\r\n                ";
				$zhongdiandu .="zhongdianarr[".$key."][3] = \"".$arr[1]."\";\r\n                ";


			}
			$zhongdiandu=str_replace('&nbsp;', "", $zhongdiandu);

			//重点读的标题数组
			$str='';
			for ($i=0;$i<count($data_step_key);$i++){
				$str.='[],';
			}
			$str=trim($str,',');
			$zhongdianarr='public var zhongdianarr:Array=new Array('.$str.');';
			//echo $zhongdianarr;exit();

			/*

			//识记词汇和精彩语句
			$woyaojiarr="";
			foreach ($data_section_won as $key=>$v){
				$woyaojiarr.="woyaojiarr[".$key."]=\"<Chapter><font color='#DE4D4F' size='30' face='宋体'>识记词汇</font><br><font color='#000000' size='24' face='宋体'><textformat indent='49'>".str_replace("\n", "", trim($data_text_kecheng_content['words']))."</textformat></font><br><font color='#DE4D4F' size='30' face='宋体'>精彩句段</font><br><font color='#000000' size='24' face='宋体'><textformat indent='49'>".str_replace("\n", "", trim($v['section']))."</textformat></font></Chapter>\";\r\n			";
			}
			$woyaojiarr=str_replace('&nbsp;', "", $woyaojiarr);
			//echo $woyaojiarr;
			*/


			//释文  只有古文有
			$Model_guwen=M('text_guwen');
			$data_guwen=$Model_guwen->where('kecheng_id="%d"',$kechengid)->select();
			$shiwenarr="";
			//译文的内容数组
			foreach ($data_guwen as $key=>$v){
				//$shiwenarr.="shiwenarr[".$key."]=\"<Chapter><font color='#DE4D4F' size='30' face='宋体'>原 文</font><br><font color='#000000' size='24' face='宋体'><textformat indent='49'>".$v['sentence']."</textformat></font><br><font color='#DE4D4F' size='30' face='宋体'>译 文</font><br><font color='#000000' size='24' face='宋体'><textformat indent='49'>".str_replace("\n", "", trim($v['description']))."</textformat></font></Chapter>\";\r\n                ";
				if($types==3){
					$shiwenarr .= "yiwen[".$key."][0] = \"<Chapter><font color='#000000' size='28' face='宋体'><b><textformat indent='56'><p align='left'>".$v['sentence']."</textformat></b></font></Chapter>\";\r\n                ";
					$shiwenarr .= "yiwen[".$key."][1] = \"<Chapter><font color='#000000' size='28' face='宋体'><b><textformat indent='56'><p align='left'>".$v['description']."</textformat></b></font></Chapter>\";\r\n                ";
				}else{
					$shiwenarr .= "yiwen[".$key."][0] = \"<Chapter><font color='#000000' size='28' face='宋体'><b><textformat indent='56'><p align='left'>".$v['sentence']."</textformat></b></font></Chapter>\";\r\n                ";
					$shiwenarr .= "yiwen[".$key."][1] = \"<Chapter><font color='#000000' size='28' face='宋体'><b><textformat indent='56'><p align='left'>".$v['description']."</textformat></b></font></Chapter>\";\r\n                ";
				}
			}

			$shiwenarr=str_replace('&nbsp;', "", $shiwenarr);

			//获取译文标题数组
			//例子public var yiwen:Array=new Array([],[],[],[]);
			$str_guwen='';
			for ($i=0;$i<count($data_guwen);$i++){
				$str_guwen.='[],';
			}
			$str_guwen=trim($str_guwen,',');
			$yiwenarr='public var yiwen:Array=new Array('.$str_guwen.');';//译文的标题数组

			$as = str_replace('$$biaoti$$', $biaoti, $as);//标题
			$as = str_replace('$$zhengwen$$', $zhengwen, $as);//正文,不带断句
			$as = str_replace('$$zhushi$$', $zhushi, $as);//词语的解释
			$as = str_replace('$$duanju$$', $duanju, $as);//正文,带断句
			$as = str_replace('$$zhongdianarr$$', $zhongdianarr, $as);//重点读的标题数组
			$as = str_replace('$$zhongdiandu$$', $zhongdiandu, $as);//重点读的内容数组
			//$as = str_replace('$$woyaoji$$', $woyaojiarr, $as);
			$as = str_replace('$$wolaidu$$', $wolaidu, $as);//我来读的内容，词汇和精彩语句标红
			$as = str_replace('$$yiwenarr$$', $yiwenarr, $as);//译文的标题数组替换
			$as = str_replace('$$shiwen$$', $shiwenarr, $as);//译文的内容数组替换

		$dir->writeFile($asfile, $as);

		$jsfl = str_replace('$$music1$$', basename($data['filepath']), $jsfl);
		$jsfl = str_replace('$$music2$$', basename($data_music_info['music_filepath']), $jsfl);
		$jsfl = str_replace('$$kecheng$$',$data['kecheng'],$jsfl);
		$dir->writeFile($jsflfile, $jsfl);

		Vendor("PHPZip");
		$Zip = new \PHPZip();
		$zifile = './flash/' . $kechengid . '.zip';
		$dir->unlinkDir($zifile);
		$Zip->Zip($todir, $zifile);


// 		ob_end_clean();//清除缓冲区,避免乱码
// 		$filename=iconv('utf-8', 'gbk', $zifile);
// 		$filesize = filesize($zifile);
// 		header( "Content-Type: application/force-download;charset=utf-8");
// 		header( "Content-Disposition: attachment; filename=".$zifile);
// 		header( "Content-Length: ".$filesize);
// 		ob_clean();
// 		readfile($zifile);
// 		unlink($zifile);


		$this->download ($zifile, ' ' . $nianji.'-'.$banben.'-'.$xueqi.'-'.str_replace('·', '', $kecheng) . '.zip');
	}

	/**
	*替换字符串，只替换一次
	*/

	function str_replace_limit($search, $replace, $subject, $limit=-1) {

	    if (is_array($search)) {
	        foreach ($search as $k=>$v) {
	        $search[$k] = '`' . preg_quote($search[$k],'`') . '`';
	        }
	    }
	    else {
	        $search = '`' . preg_quote($search,'`') . '`';
	    }

	return preg_replace($search, $replace, $subject, $limit);
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
	/**
	 * 课程内容管理页面
	 */
	public function kechenginfo(){
		$id=I('id/d',0);
		$Model_kecheng=M('text_kecheng');
		$data_kecheng=$Model_kecheng->where('id="%d"',$id)->field('mobanid')->find();
		$this->assign('mobanid',$data_kecheng['mobanid']);
		$Model_word_info=M('text_word_info');
		$data_word_info=$Model_word_info->where('kecheng_id="%d"',$id)->field('id,word,explain,sortid')->select();
		$this->assign('word_info',$data_word_info);
		$Model_step_key=M('text_keypoint');
		$data_step_key=$Model_step_key->where('kecheng_id="%d"',$id)->select();
		$this->assign('zhongdian_info',$data_step_key);
		$Model_section_wonder=M('text_wonderful');
		$data_section_won=$Model_section_wonder->where('kecheng_id="%d"',$id)->select();
		$this->assign('jingcai_info',$data_section_won);
		$Model_text_kecheng_content=M('text_kecheng_content');
		$data_text=$Model_text_kecheng_content->where('kecheng_id="%d"',$id)->find();
		$this->assign('text_info',$data_text);
		$Model_guwen=M('text_guwen');
		$data_guwen=$Model_guwen->where('kecheng_id="%d"',$id)->select();
		$this->assign('guwen_info',$data_guwen);
		$this->assign('kecheng_id',$id);
		$this->display();
	}
	/**
	 * 保存课文内容和识记词汇内容
	 */
	public function addKewen(){
		$kecheng_id=I('kecheng_id/d',0);
		$tncontent=I('tncontent/s','');
		$words=I('words/s',0);
		$Model=M('text_kecheng_content');
	//	echo htmlspecialchars_decode($tncontent);
		//查询数据库是否存在
		$re=$Model->where('kecheng_id="%d"',$kecheng_id)->field('id')->find();
		if (is_array($re)){
			$Model->where('kecheng_id="%d"',$kecheng_id)->delete();
		}
		$data['kecheng_id']=$kecheng_id;
		$data['text_content']=htmlspecialchars_decode($tncontent);
		$data['words']=$words;
		$Model->add($data);
	}


	/**
	 * 添加单词页面
	 */
	public function addWord(){
		$id=I('id/d',0);//传递课程ID
		$this->assign('kecheng_id',$id);
		$this->display();
	}
	/**
	 * 保存添加的单词
	 */
	public function addWord_info(){
		$kecheng_id=I('kecheng_id/d',0);
		$word=I('word/s','');
		$explain=I('explain/s','');
		$Model=M('text_word_info');
		//先查询单词是否存在
		$list=$Model->where('kecheng_id="%d" and word="%s"',$kecheng_id,$word)->find();
		if (is_array($list)){
			//存在
			echo '已存在';
		}else{
			$sort=$Model->where('kecheng_id="%d"',$kecheng_id)->count();
			$data['kecheng_id']=$kecheng_id;
			$data['word']=$word;
			$data['explain']=$explain;
			$data['sortid']=$sort+1;
			$id=$Model->add($data);
			echo $id;
		}
	}
	/**
	 * 返回添加成功的单词信息
	 */
	public function queryWordInfo(){
		$id=I('id/d',0);
		$Model=M('text_word_info');
		$data=$Model->where('id="%d"',$id)->field('id,word,explain,sortid')->find();
		$this->ajaxReturn($data);
	}
	/**
	 * 删除单词
	 */
	public function delword(){
		$id=I('id/d',0);
		$Model=M('text_word_info');
		$Model->delete($id);
	}
	/**
	 * 编辑单词页面
	 */
	public function editWord_info(){
		$wordid=I('wordid/d',0);//传递wordID,编辑资源时候用
		$Model=M('text_word_info');
		$data=$Model->where('id="%d"',$wordid)->field('id,word,explain,sortid')->find();
		$this->assign('word_info',$data);
		$this->display();
	}
	/**
	 * 保存单词信息
	 */
	public function editWord(){
		$wordid=I('wordid/d',0);
		$word=I('word/s',0);
		$explain=I('explain/s','');
		$sortid=I('sortid/s','');
		$Model=M('text_word_info');
		$data['word']=$word;
		$data['explain']=$explain;
		$data['sortid']=$sortid;
		$Model->where('id="%d"',$wordid)->save($data);
		echo $word.'|'.$explain.'|'.$sortid;
	}
	/**
	 * 添加重点语段页面
	 */
	public function addZhongdian(){
		$id=I('id/d',0);//传递课程ID
		$mobanid=I('mobanid/d',0);//传递模版ID
		$this->assign('mobanid',$mobanid);
		$this->assign('kecheng_id',$id);
		$this->display();
	}
	/**
	 * 添加重点语段到数据库
	 */
	public function addText_zhongdian(){
		$kecheng_id=I('kecheng_id/d',0);
		$step=I('step/s','');
		$textContent=I('textContent/s','');
		$textGuide=I('textGuide/s','');
		$timeStep=I('timeStep/s','');
		$Model=M('text_keypoint');
		//判断数据库中是否存在根据时间判断
		$re=$Model->where('kecheng_id="%d" and time="%s"',$kecheng_id,$timeStep)->find();
		if (is_array($re)){
			echo '已存在';
		}else{
			$data['kecheng_id']=$kecheng_id;
			$data['step']=$step;
			$data['content']=$textContent;
			$data['tag']=$textGuide;
			$data['time']=$timeStep;
			$id=$Model->add($data);
			echo $id;
		}
	}
	/**
	 * 查询重点语段内容
	 */
	public function queryTextInfo(){
		$id=I('id/d',0);//传递重点语段ID
		$Model=M('text_keypoint');
		$data=$Model->where('id="%d"',$id)->find();
		$this->ajaxReturn($data);
	}
	/**
	 * 删除重点语段
	 */
	public function delzhongdian(){
		$id=I('id/d',0);//传递重点语段ID
		$Model=M('text_keypoint');
		$Model->delete($id);
	}
	/**
	 * 编辑重点语段页面
	 */
	public function editZhongdian(){
		$id=I('id/d',0);//传递重点语段ID
		$mobanid=I('mobanid/d',0);//传递模版ID
		$this->assign('mobanid',$mobanid);
		$Model=M('text_keypoint');
		$data=$Model->where('id="%d"',$id)->find();
		$this->assign('data',$data);
		$this->display();
	}
	/**
	 * 重点语段内容编辑后保存
	 */
	public function editText_zhongdian(){
		$zhongdianId=I('zhongdianId/d',0);
		$step=I('step/s','');
		$textContent=I('textContent/s','');
		$textGuide=I('textGuide/s','');
		$timeStep=I('timeStep/s','');
		$Model=M('text_keypoint');
		$data['step']=$step;
		$data['content']=$textContent;
		$data['tag']=$textGuide;
		$data['time']=$timeStep;
		$Model->where('id="%d"',$zhongdianId)->save($data);
		echo $step.'|'.$textContent.'|'.$textGuide.'|'.$timeStep;
	}
	/**
	 * 精彩语段添加页面
	 */
	public function addJingcai(){
		$id=I('id/d',0);//传递课程ID
		$this->assign('kecheng_id',$id);
		$this->display();
	}
	/**
	 * 精彩语句添加数据库
	 */
	public function addText_jingcai(){
		$kecheng_id=I('kecheng_id/d',0);
		$textContent=I('textContent/s','');
		$Model=M('text_wonderful');
		//判断数据库中是否存在根据时间判断
		$re=$Model->where('kecheng_id="%d" and section="%s"',$kecheng_id,$textContent)->find();
		if (is_array($re)){
			echo '已存在';
		}else{
			$data['kecheng_id']=$kecheng_id;
			$data['section']=$textContent;
			$id=$Model->add($data);
			echo $id;
		}
	}
	/**
	 * 精彩语句查询
	 */
	public function queryJingcaiInfo(){
		$id=I('id/d',0);
		$Model=M('text_wonderful');
		$data=$Model->where('id="%d"',$id)->find();
		$this->ajaxReturn($data);
	}
	/**
	 * 精彩语句删除
	 */
	public function deljingcai(){
		$id=I('id/d',0);
		$Model=M('text_wonderful');
		$Model->delete($id);
	}
	/**
	 * 精彩语句编辑页面
	 */
	public function editJingcai(){
		$id=I('id/d',0);
		$Model=M('text_wonderful');
		$data=$Model->where('id="%d"',$id)->find();
		$this->assign('data',$data);
		$this->display();
	}
	/**
	 * 精彩内容修改,保存数据库
	 */
	public  function editText_jingcai(){
		$jingcaiId=I('jingcaiId/d',0);
		$textContent=I('textContent/s','');
		$Model=M('text_wonderful');
		$data['section']=$textContent;
		$Model->where('id="%d"',$jingcaiId)->save($data);
		echo $textContent;

	}
	/**
	 * 添加释义页面
	 */
	public function addShiyi(){
		$kecheng_id=I('id/d',0);
		$this->assign('kecheng_id',$kecheng_id);
		$this->display();
	}
	/**
	 * 添加释义到数据库
	 */
	public function addGuwen(){
		$kecheng_id=I('kecheng_id/d',0);
		$sentence=I('sentence/s','');
		$description=I('description/s','');
		$Model=M('text_guwen');
		$data['kecheng_id']=$kecheng_id;
		$data['sentence']=$sentence;
		$data['description']=$description;
		$id=$Model->add($data);
		echo $id;
	}
	/**
	 * 查询当前古文内容
	 */
	public function queryGuwenInfo(){
		$id=I('id/d',0);
		$Model=M('text_guwen');
		$data=$Model->where('id="%d"',$id)->find();
		$this->ajaxReturn($data);
	}
	/**
	 * 删除古文
	 */
	public function delguwen(){
		$id=I('id/d',0);
		$Model=M('cn_text_guwen');
		$Model->delete($id);
	}
	/**
	 * 古文编辑页面
	 */
	public function editGuwen(){
		$id=I('id/d',0);
		$Model=M('cn_text_guwen');
		$data=$Model->where('id="%d"',$id)->find();
		$this->assign('data',$data);
		$this->display();
	}
	/**
	 * 古文编辑保存
	 */
	public function editGuwen_info(){
		$guwenId=I('guwenId/d',0);
		$sentence=I('sentence/s','');
		$description=I('description/s','');
		$Model=M('cn_text_guwen');
		$data['sentence']=$sentence;
		$data['description']=$description;
		$Model->where('id="%d"',$guwenId)->save($data);
		echo $sentence.'|'.$description;
	}














}