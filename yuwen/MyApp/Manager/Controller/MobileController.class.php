<?php
namespace Manager\Controller;
use Think\Controller;

class MobileController extends Controller {
    public function index(){
    	//$userid='01788';
    	//$userarea='1.1';
    	$userid=cookie('username');
    	$userarea=cookie('areacode');
    	$kid=$this->checkUser($userid, $userarea);
    	$kid=I('kid/d',0);
    	//$kid=56;
	    $m=M();
    	if ($kid==0){
	    	$sql='SELECT DISTINCT n.banben FROM yw_kecheng m RIGHT JOIN yw_ziversion n ON m.versionid=n.id WHERE m.nianji="一年级" AND m.xueqi="上学期" ORDER BY n.sortid';
	    	$data_version=$m->query($sql);
	    	$sql_first='select id from yw_ziversion where banben="%s"';
	    	$data_first=$m->query($sql_first,$data_version[0]['banben']);
			$data=M('kecheng')->where('nianji="一年级" and xueqi="上学期" and versionid="%d" and url is not null',$data_first[0]['id'])->field('id,kecheng,url')->order('sortid')->select();
			$this->assign('banben_now',$data_version[0]['banben']);
    	}else {
    		$sql_user='SELECT m.nianji,m.xueqi,n.banben,m.versionid FROM yw_kecheng m,yw_ziversion n WHERE m.versionid=n.id AND m.id="%d"';
    		$data_user=$m->query($sql_user,$kid);
    		$data_user=$data_user[0];
    		
    		$sql='SELECT DISTINCT n.banben FROM yw_kecheng m RIGHT JOIN yw_ziversion n ON m.versionid=n.id WHERE m.nianji="%s" AND m.xueqi="%s" ORDER BY n.sortid';
    		$data_version=M('kecheng')->query($sql,$data_user['nianji'],$data_user['xueqi']);   
    		 		
    		$data=M('kecheng')->where('nianji="%s" and xueqi="%s" and versionid="%d" and url is not null',$data_user['nianji'],$data_user['xueqi'],$data_user['versionid'])->field('id,kecheng,url')->order('sortid')->select();
    		if ($data_user['nianji']=='一年级'){
    			$nianji='yinianji';
    		}elseif ($data_user['nianji']=='二年级'){
    			$nianji='ernianji';
    		}else {
    			$nianji='sannianji';
    		}
    		if ($data_user['xueqi']=='上学期'){
    			$xueqi='sxq';
    		}else {
    			$xueqi='xxq';
    		}
    		$this->assign('nianji',$nianji);
    		$this->assign('xueqi',$xueqi);
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
    		return 0;
    	}else {
    		return $data['kid'];
    	}
    }
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
	    	$data_kecheng=M('kecheng')->where('nianji="%s" and xueqi="%s" and versionid="%d" and url is not null',$nianji,$xueqi,$data_first[0]['id'])->field('id,kecheng,url')->order('sortid')->select();
	    	$info['banben']=$data_version;
	    	$info['kecheng']=$data_kecheng;
	    	//var_dump($info);
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
    	$sql_first='select id from yw_ziversion where banben="%s"';
    	$data_first=M()->query($sql_first,$banben);
    	$m=M('kecheng');
    	$data=$m->where('nianji="%s" and xueqi="%s" and versionid="%d" and url is not null',$nianji,$xueqi,$data_first[0]['id'])->field('id,kecheng,url')->order('sortid')->select();
    	$this->ajaxReturn($data);
    }

    /**
     * 课程汉字内容页
     */
    public function info(){
    	$kid=I('kid/d');
    	$m=M('kecheng');
    	$data=$m->where('id="%d"',$kid)->field('kecheng')->find();
    	
    	$sql='SELECT m.zid,m.ziyinid,n.zi,n.pianpang,n.bihuashu,n.shizi,n.xiezi FROM yw_kecheng_hanzi m,yw_zi n WHERE m.zid=n.id AND m.kechengid="%d" ORDER BY sortid';
    	$Model=M();
    	$data_hanzi=$Model->query($sql,$kid);
    	
    	//查询上一页和下一页的课程id
    	
    	$sql_info='SELECT id FROM yw_kecheng WHERE nianji=(SELECT nianji from yw_kecheng WHERE id="%d") AND xueqi=(SELECT xueqi from yw_kecheng WHERE id="%d") and versionid=(SELECT versionid from yw_kecheng WHERE id="%d") ORDER BY sortid';
    	$data_info=$Model->query($sql_info,$kid,$kid,$kid);
    	$data_info=array_column($data_info,'id'); //二维数组转一维数组
    	//var_dump($data_info);
    	$keyarray=array_keys($data_info,$kid);
    	$key=$keyarray[0];
    	
    	if ($data_info[$key-1]==''){
    		$kidup='first';
    	}else {
    		$kidup=$data_info[$key-1];
    	}
    	
    	if ($data_info[$key+1]==''){
    		$kiddown='last';
    	}else {
    		$kiddown=$data_info[$key+1];
    	}
    	
//     	$data_info=$m->where('id="%d"',$kid)->field('nianji,xueqi,versionid,sortid')->find();
//     	$sort=$data_info['sortid'];
    	
//     	$sql1='SELECT id,case WHEN sortid="%d" then "下一页" else "上一页" end as flag FROM yw_kecheng WHERE nianji="%s" and xueqi="%s" AND versionid="%d" and sortid in ("%d","%d")';
//     	$data_sx=$Model->query($sql1,$sort+1,$data_info['nianji'],$data_info['xueqi'],$data_info['versionid'],$sort-1,$sort+1);
//     	if (count($data_sx)==2){
//     		$kidup=$data_sx[0]['id'];
//     		$kiddown=$data_sx[1]['id'];
//     	}else {
//     		if ($data_sx[0]['flag']=='上一页'){
//     			$kidup=$data_sx[0]['id'];
//     			$kiddown='last';
//     		}else {
//     			$kidup='first';
//     			$kiddown=$data_sx[0]['id'];
//     		}
//     	}
    	
    	$this->assign('kidup',$kidup);
    	$this->assign('kiddown',$kiddown);
    	$this->assign('kid',$kid);
    	$this->assign('hanzi',$data_hanzi);
    	$this->assign('kecheng',$data['kecheng']);
    	$this->display();
    }
    
    public function queryHanziInfo(){
    	$zid=I('zid/d');
    	$ziyinid=I('ziyinid/d');
    	$m=M();
    	$sql='SELECT n.id,m.pianpang,m.bihuashu,m.shizi,m.xiezi,n.pinyin,n.wav,n.zaoju1,n.zaoju2,n.zaoju3,n.zuci1,n.zuci2,n.zuci3,n.ziyi1,n.ziyi2,n.ziyi3 FROM yw_zi m,yw_ziyin n WHERE m.id=n.zid AND n.id="%d"  AND m.id="%d"';
    	$data=$m->query($sql,$ziyinid,$zid);
    	
    	$tmp=$this->checkDuoyin($zid, $ziyinid);
    	
    	$duoyin['duoyin']=$tmp;
    	$duoyin['normal']=$data;
    	//var_dump($duoyin);exit();
    	$this->ajaxReturn($duoyin);
    }
    /**
     * 测试是否多音字
     */
    private function checkDuoyin($zid,$ziyinid){
    	$sql='SELECT id,pinyin,wav FROM yw_ziyin WHERE id<>"%d" AND zid="%d"';
    	$m=M();
    	$data=$m->query($sql,$ziyinid,$zid);
    	return  $data;
    }
    /**
     * 查询多音字信息
     */
    public function queryDuoyin(){
    	$ziyinid=I('ziyinid/d');
    	$m=M('ziyin');
    	$data=$m->where('id="%d"',$ziyinid)->field('pinyin,zaoju1,zaoju2,zaoju3,zuci1,zuci2,zuci3,ziyi1,ziyi2,ziyi3')->find();
    	$this->ajaxReturn($data);
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

 
