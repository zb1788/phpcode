<?php
namespace Manager\Controller;
use Think\Controller;

class KlxszController extends Controller {
    public function index(){
    	//$userid='01788';
    	//$userarea='1.1';
    	$userid=cookie('username');
    	$userarea=cookie('areacode');
    	$kid=$this->checkUser($userid, $userarea);
	    //$m=M('kecheng');
	    $m=M();
    	if ($kid=='kong'){
	    	$sql='SELECT DISTINCT n.banben FROM yw_kecheng m RIGHT JOIN yw_ziversion n ON m.versionid=n.id WHERE m.nianji="一年级" AND m.xueqi="上学期" ORDER BY n.sortid';
	    	$data_version=$m->query($sql);
	    	$sql_first='select id from yw_ziversion where banben="%s"';
	    	$data_first=$m->query($sql_first,$data_version[0]['banben']);
			$data=M('kecheng')->where('nianji="一年级" and xueqi="上学期" and versionid="%d" and url is not null',$data_first[0]['id'])->field('id,kecheng,url,praise')->order('sortid')->select();
    	}else {
    		$sql_user='SELECT m.nianji,m.xueqi,n.banben,m.versionid FROM yw_kecheng m,yw_ziversion n WHERE m.versionid=n.id AND m.id="%d"';
    		$data_user=$m->query($sql_user,$kid);
    		$data_user=$data_user[0];    		
    		$sql='SELECT DISTINCT n.banben FROM yw_kecheng m RIGHT JOIN yw_ziversion n ON m.versionid=n.id WHERE m.nianji="%s" AND m.xueqi="%s" ORDER BY n.sortid';
    		$data_version=$m->query($sql,$data_user['nianji'],$data_user['xueqi']);
    		//$data_version=$m->distinct(true)->where('nianji="%s" and xueqi="%s" and url is not null',$data_user['nianji'],$data_user['xueqi'])->field('banben')->select();
    		$data=M('kecheng')->where('nianji="%s" and xueqi="%s" and versionid="%d" and url is not null',$data_user['nianji'],$data_user['xueqi'],$data_user['versionid'])->field('id,kecheng,url,praise')->order('sortid')->select();
    		if ($data_user['nianji']=='一年级'){
    			$nianji='yinianji';
    		}elseif ($data_user['nianji']=='二年级'){
    			$nianji='ernianji';
    		}else {
    			$nianji='sannianji';
    		}
    		$this->assign('nianji',$nianji);
    		$this->assign('xueqi',$data_user['xueqi']);
    		$this->assign('banben_now',$data_user['banben']);
    	}
    	$this->assign('kid',$kid);
		$this->assign('banben',$data_version);
    	$this->assign('kecheng',$data);
    	$this->display();
    }
    /**
     *检查用户记录是否存在 
     */
    private function checkUser($userid,$userarea){
    	$m=M('record');
    	$data=$m->where('userid="%s" and userarea = "%s"',$userid,$userarea)->field('kid')->find();
    	if (empty($data)){
    		return 'kong';
    	}else {
    		return $data['kid'];
    	}
    }
    /**
     * 更新用户浏览记录
     */
    public function updateRecord(){
    	$kid=I('kid/d');
    	$opt=I('opt/s');
    	$userid=cookie('username');
    	$userarea=cookie('areacode');
    	
    	if ($userid!=''){
	    	$m=M('record');
	    	$data['userid']=$userid;
	    	$data['userarea']=$userarea;
	    	$data['kid']=$kid;
	    	$data_user=$m->where('userid="%s" and userarea="%s"',$userid,$userarea)->find();
	    	if (empty($data_user)){
	    		$m->add($data);
	    	}else {
	    		$m->where('userid="%s" and userarea="%s"',$userid,$userarea)->save($data);
	    	}
    	}
    }
    /**
     * 判断是否有人教版
     */
    private function checkVersion($data_version){
    	$data_version=array_column($data_version,'banben');
    	$default_v="人教版";
    	if (in_array($default_v, $data_version)){
    		//如果有人教版就查询人教版下的资源
    		$banben=$default_v;
    	}else{
    		$banben=$data_version[0];
    	}
    	return $banben;
    }
    /**
     * 改变年级或者学期
     */
    public function changeGrade(){
    	$nianji=I('nianji/s');
    	$xueqi=I('xueqi/s');
    	$m=M();
    	$sql='SELECT DISTINCT n.banben FROM yw_kecheng m RIGHT JOIN yw_ziversion n ON m.versionid=n.id WHERE m.nianji="%s" AND m.xueqi="%s" ORDER BY n.sortid';
    	$data_version=$m->query($sql,$nianji,$xueqi);
    	//$data_version=$m->distinct(true)->where('nianji="%s" and xueqi="%s"  and url is not null',$nianji,$xueqi)->field('banben')->select();
    	if (empty($data_version)){
    		echo 'error';
    	}else {
    		$sql_first='select id from yw_ziversion where banben="%s"';
    		$data_first=$m->query($sql_first,$data_version[0]['banben']);    		
	    	$data_kecheng=M('kecheng')->where('nianji="%s" and xueqi="%s" and versionid="%d" and url is not null',$nianji,$xueqi,$data_first[0]['id'])->field('id,kecheng,url,praise')->order('sortid')->select();
	    	$info['banben']=$data_version;
	    	$info['kecheng']=$data_kecheng;
	    	//var_dump($info);exit();
	    	$this->ajaxReturn($info);
    	}
    }
    /**
     * 改变版本
     */
    public function changeVersion(){
    	$nianji=I('nianji/s');
    	$banben=I('banben/s');
    	$xueqi=I('xueqi/s');
    	$m=M('kecheng');
    	$sql_first='select id from yw_ziversion where banben="%s"';
    	$data_first=M()->query($sql_first,$banben);
    	$data=$m->where('nianji="%s" and xueqi="%s" and versionid="%d" and url is not null',$nianji,$xueqi,$data_first[0]['id'])->field('id,kecheng,url,praise')->order('sortid')->select();
    	$this->ajaxReturn($data);
    }
    /**
     * 加载flash页面
     */
    public function playSwf(){
    	$type=I('type/d');
    	$url=I('url/s');
    	$width=I('width/d',0);
    	$height=I('height/d',0);
    	$this->assign('type',$type);
    	$this->assign('width',$width);
    	$this->assign('height',$height);
    	$this->assign('url',$url);
    	$this->display();
    }
    /**
     * 点赞
     */
    public function updatePraise(){
    	$kid=I('kid/d');
    	$m=M();
    	$sql='UPDATE yw_kecheng SET praise=(praise+1) WHERE id="%d"';
    	$m->execute($sql,$kid);
    	
    }
    

    /**
     * 测试
     */
    public function  test(){
    	$m=M('kecheng');
    	$data=$m->field('nianji,xueqi,banben,url,kecheng,sortid')->select();
    	$json= json_encode($data);
    	var_dump(json_decode($json));
    	var_dump(json_decode($json,true));
    }
}

 
