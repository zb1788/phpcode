<?php
namespace Home\Controller;
use Think\Controller;
class JsdemoController extends Controller {
    public function jslist(){
        $this->display();
    }
    public function playMp3(){
    	$filepath=I('filepath/s');
    	$this->assign('filepath',$filepath);
    	$this->display();
    }
    public function playPic(){
    	$filepath=I('filepath/s');
    	$this->assign('filepath',$filepath);
    	$this->display();
    }
    public function piclist(){
    	$this->display();
    }
    public function pagelist(){
    	$this->display();
    }

    public function picShow(){
        $this->display();
    }



    public function getTreeNodes(){
    	//header('Content-type:text/json');
    	$id=I('id/s');
    	$m=M('');
    	if ($id==''){
    	    $sql="SELECT KS_ID,P_ID,KS_NAME,IF(ks_level<>6,'true','false') as isParent,C1,DISPLAY_ORDER  FROM t_share_knowledge_structure WHERE is_unit=0 and flag>0 and ks_type=0 AND KS_LEVEL=2 AND SUBSTRING(KS_ID,1,6) in('000102','000103','000104','000105','000106','000107','000108','000109','00010a','00010b')";
    	}else {
    		$sql = "SELECT KS_ID,P_ID,KS_NAME,IF(ks_level<>6,'true','false') as isParent,C1,DISPLAY_ORDER FROM t_share_knowledge_structure WHERE p_id='".$id."' AND is_unit=0 and flag>0 and ks_type=0 AND SUBSTRING(KS_ID,1,6) in('000102','000103','000104','000105','000106','000107','000108','000109','00010a','00010b')";
    	}
    	$data_mulu = $m->query($sql);
    	$json="[";
    	foreach ($data_mulu as $v){
    		$json.='{id:"'.$v['ks_id'].'", pId:"'.$v['p_id'].'",isParent:"'.$v['isparent'].'", name:"'.$v['ks_name'].'",file:"'.$v['ks_id'].'"},';
    	}
    	$json = rtrim($json,',');
    	$json = $json.']';
	   	echo $json;

    }
}