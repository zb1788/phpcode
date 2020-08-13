<?php
namespace Manager\Controller;
use Think\Controller;

/** 
* 单词控制器
*  
* @author         gm 
* @since          1.0 
*/
class HanziController extends CheckController {
	public function ziyinedit()
	{
		$zid = I("zid/d");
		$id = I("ziyinid/d");
		$re = array();
		$re["zid"] = $zid;
		$re["2wav"] = '请上传wav文件';

		if (!empty($id)) {
			$Z = M("ziyin"); // 实例化User对象 
			$re = $Z->where('id=%d', $id)->find(); 
			$re["2wav"] = $re["wav"] ;
		}
		$this->assign('data',$re);
		$this->display();
	}

	//查找汉字及展示
	public function ziinfo(){
		// //不是AJAX请求则不返回数据
		//if (!IS_AJAX){$this->ajaxReturn();}
		$szi = I("szi");
		$Z = M("Zi"); // 实例化User对象
		// 查找status值为1name值为think的用户数据 
		$data = $Z->where('zi="%s"', $szi)->find();
		$re = array();
		if (empty($data)) {
			$re["iserr"] = 1;
		}
		else
		{
			$re["iserr"] = 0;
			$re["ziinfo"] = $data;
		}		 
		$this->ajaxReturn($re);
	}	 

	public function ziyininfo(){
		$zid = I("zid/d");
		$Z = M("ziyin"); // 实例化User对象 
		$data = $Z->where('zid=%d', $zid)->select(); 
		$this->ajaxReturn($data);
	}	 

	public function ziyindel(){
		$id = I("id/d");
		$Z = M("ziyin"); // 实例化User对象 
		$data = $Z->where('id=%d', $id)->delete();
		$this->ajaxReturn(1);
	}	 

	public function ziyinsave(){
		// // //不是AJAX请求则不返回数据
		// //if (!IS_AJAX){$this->ajaxReturn();}
		$data = I("data");
		$arr = json_decode(str_replace("&quot;", '"', $data),ture);
		$zid = $arr["zid"] ;
		$id =$arr["id"] ;	
		$wupinyin =$arr["wupinyin"] ;	
		$shengdiao =$arr["shengdiao"];

		$re = array(); 
		if (empty($id)) {
			$id = 0;
		} 
		if (empty($zid)) {
			$re["iserr"] = 1; 
			$this->ajaxReturn($re);
		} 
		$Z = M("Ziyin");
		$data = $Z->where('id <> %d and zid = %d and  wupinyin="%s" and shengdiao=%d',$id,$zid,$wupinyin,$shengdiao)->getField('id'); 
		if (!empty($data)) {
			$re["iserr"] = 1;
			$this->ajaxReturn($re); 
		} 
    	if (empty($id)) { 
    		unset($arr["id"]); 
    		$arr["id"] = $Z->add($arr);
    	}
    	else
    	{ 
    		$Z->save($arr);
    	}
		$re["iserr"] = 0;
		$re["data"] = $arr;
		$this->ajaxReturn($re);
	}	

	public function zisave(){
		// //不是AJAX请求则不返回数据
		//if (!IS_AJAX){$this->ajaxReturn();}
		$data = I("data");
		$arr = json_decode(str_replace("&quot;", '"', $data),ture);
		$zi = $arr["zi"] ;
		$id =$arr["id"] ;

		if (empty($id)) {
			$id = 0;
		}

		$Z = M("Zi");
		$data = $Z->where('zi="%s" and id <> %d',$zi,$id)->find();
		if (!empty($data)) {
			$re["iserr"] = 1;
			$this->ajaxReturn($re); 
		}
      
    	if (empty($id)) { 
    		unset($arr["id"]); 
    		$arr["id"] = $Z->add($arr);
    	}
    	else
    	{ 
    		$Z->save($arr);
    	}
		$re["iserr"] = 0;
		$re["data"] = $arr;
		$this->ajaxReturn($re);
	}

	public function import(){ 
	 	$filename = $_REQUEST["filename"];
	 	Vendor("PHPExcel");
	 	$arr_excel = readExcel($filename);
	 	$arr_rs = $this->importTextExcel($arr_excel);
	 		 	
	 	$iserr = 0;
		$errmsg = "";
		$i = 0;
		foreach ($arr_rs as $row => $v) {
			if ($v["iserr"]) {
				$iserr = 1;
				$errmsg.= $v["errmsg"] . '<br>';
			}
			else
			{
				$i++;
			}			
		}
		$data["iserr"] = $iserr;
		$data["errmsg"] = $errmsg;
		$data["sucnum"] = $i; 
		$this -> ajaxReturn($data);		
	}

	protected function  importTextExcel($arr_excel){  
		//A B C D E G
		foreach (C('CONST_ZIFLASH') as $key => $value) {
			$arr_nianji[$key] = $key;
		} 

		$arr_xueqi = C('CONST_XUEQI');

		$Version = M('ziversion');	 
	    $rs = $Version->field('id,banben') -> select(); 
		foreach($rs as $v) {
			$arr_version[$v["id"]] = $v["banben"];	
		} 

		$arrcolumn = array('A','B','C','D','E','F','G');
		$i = 0;
		$iserr = flase;
		foreach ($arr_excel as $row => $v) {
			if(empty($v["Column"]['A'])){
				unset($arr_excel[$row]);
				continue;
			} 
			/*必填列判断*/
			foreach ($arrcolumn as $cv) {
				$v["iserr"] = false;
				if (empty($v["Column"][$cv])) {
					$v["iserr"] = ture;
					$v["errmsg"] = '第' . $row .'行' . $cv . '列为空';
					$arr_excel[$row] = $v;
					$iserr = ture;
					break;
				}
			}
			if($v["iserr"]){
				continue;
			}

			$i++; 
			$v["Column"]["A"] = is_numeric($v["Column"]["A"])?$v["Column"]["A"]:$i;

				//增加验证及取版本ID数据修改 
			$nianji = trim($v["Column"]["B"]);
			$xueqi = trim($v["Column"]["C"]);
			$banben = trim($v["Column"]["D"]); 
 
			$nianji = array_search($nianji,$arr_nianji); 
			if (empty($nianji)){		
				$v["iserr"] = ture;
				$v["errmsg"] = '第' . $row .'行' . $cv . '年级错误！';
				$arr_excel[$row] = $v;
				$iserr = ture;
				continue;
			} 
			$xueqi = array_search($xueqi,$arr_xueqi);
			if (empty($xueqi)){		
				$v["iserr"] = ture;
				$v["errmsg"] = '第' . $row .'行' . $cv . '学期错误！';
				$arr_excel[$row] = $v;
				$iserr = ture;
				continue;
			} 
 			 
			$versionid = array_search($banben,$arr_version); 
			$v["Column"]["versionid"] = $versionid ;
			if (empty($versionid)){		
				$v["iserr"] = ture;
				$v["errmsg"] = '第' . $row .'行' . $cv . '版本错误！'; 
				$arr_excel[$row] = $v;
				$iserr = ture;
				continue;
			}

			$kecheng = trim($v["Column"]["E"]); 

			unset($arr_cur_kecheng);
			$arr_cur_kecheng["nianji"] = $nianji;
			$arr_cur_kecheng["xueqi"] = $xueqi;
			$arr_cur_kecheng["versionid"] = $versionid ; 
			$arr_cur_kecheng["kecheng"] = $kecheng; 
		 	
			$kechengid = $this->getKechengID($arr_cur_kecheng);
		 	$v["Column"]["kechengid"] =  $kechengid;


  

		 	$arr_excel[$row]["H"] = is_numeric($v["Column"]["H"])?$v["Column"]["H"]:0;


		 	$zi = trim($v["Column"]["F"]); 
		 	$wupinyin = trim($v["Column"]["G"]); 
		 	$shengdiao = trim($v["Column"]["H"]); 

		 	$shengdiao = is_numeric($shengdiao)?$shengdiao:0;

		 	$zid = 0; 
			$ziyinid = 0;
			$Model=M();
		 	$sql='SELECT yw_ziyin.zid,yw_ziyin.id FROM yw_zi,yw_ziyin WHERE yw_zi.id=yw_ziyin.zid AND yw_zi.zi="%s" AND yw_ziyin.wupinyin="%s" AND yw_ziyin.shengdiao=%d';
			$rs = $Model->query($sql,$zi,$wupinyin,$shengdiao);		
			if (empty($rs)) {
				$v["iserr"] = ture;
				$v["errmsg"] = '第' . $row .'行G列，该声调的汉字不存在';
				$arr_excel[$row] = $v;
				$iserr = ture;
				continue;
			}
			else
			{
				$v["Column"]["zid"] = $rs[0]["zid"]; 
				$v["Column"]["ziyinid"] = $rs[0]["id"];
			}

		 	$Kchz = M('kecheng_hanzi');
		 	$rs= $Kchz ->where("kechengid =%d and zid=%d and ziyinid=%d",$kechengid,$v["Column"]["zid"],$v["Column"]["ziyinid"]) -> find(); 
			if (!empty($rs)) {
				$v["iserr"] = ture;
				$v["errmsg"] = '第' . $row .'行G列，生字已经添加';
				$arr_excel[$row] = $v;
				$iserr = ture;
				continue;
			} 
		 	$arr_excel[$row] = $v;    
		 	unset($Kchz);
		 	$Kchz = M('kecheng_hanzi');
			$Kchz -> kechengid = $v["Column"]["kechengid"];  
			$Kchz -> zid =  $v["Column"]["zid"];  
			$Kchz -> ziyinid = $v["Column"]["ziyinid"];  
			$Kchz -> sortid = $v["Column"]["A"];
			$Kchz -> add();
		}
		return $arr_excel;
	}


	function getKechengID($arr_cur_kecheng)
	{
		global $arr_kecheng;
		$kecheng = M('kecheng');
		if (empty($arr_kecheng)) {		    
		    $rs = $kecheng->field('id,nianji,xueqi,versionid,kecheng') -> select(); 
			foreach($rs as $v) {
				$arr_kecheng[$v["id"]]["nianji"] = $v["nianji"];
				$arr_kecheng[$v["id"]]["xueqi"] = $v["xueqi"];
				$arr_kecheng[$v["id"]]["versionid"] = $v["versionid"]; 
				$arr_kecheng[$v["id"]]["kecheng"] = $v["kecheng"]; 	
			}
		}

		if (isset($arr_kecheng)) {
			$kechengid = array_search($arr_cur_kecheng, $arr_kecheng);
		}else
		{
			$kechengid = 0;
		}

		if (empty($kechengid)){		
			$kecheng -> nianji = $arr_cur_kecheng["nianji"];
			$kecheng -> xueqi = $arr_cur_kecheng["xueqi"];
			$kecheng -> versionid = $arr_cur_kecheng["versionid"]; 
			$kecheng -> kecheng = $arr_cur_kecheng["kecheng"]; 
			//新增
			$kechengid= $kecheng -> add(); 
			$Model=M();
			$Model->execute('update yw_kecheng set sortid="'.$kechengid.'" where id="'.$kechengid.'"');
			unset($arr_kecheng);
			global $arr_kecheng;
			$rs=$kecheng->field('id,nianji,xueqi,versionid,kecheng') -> select();
			foreach($rs as $v) {
				$arr_kecheng[$v["id"]]["nianji"] = $v["nianji"];
				$arr_kecheng[$v["id"]]["xueqi"] = $v["xueqi"];
				$arr_kecheng[$v["id"]]["versionid"] =  $v["versionid"];
				$arr_kecheng[$v["id"]]["kecheng"] =  $v["kecheng"];		
			}
		}
		return $kechengid;
	}

	public function kcnianji(){
		// $zid = I("zid/d");
		// $Z = M("kecheng"); // 实例化User对象 
		// $data = $Z ->field('nianji')->group('nianji')->select();
		// $this->ajaxReturn($data);
		$data = C('CONST_ZIFLASH');
		$this->ajaxReturn($data); 
	}	
	
	public function kcxueqi(){
		// $nianji = I("nianji/s");
		// $Z = M("kecheng"); // 实例化User对象 
		// $data = $Z ->where('nianji = "%s"' , $nianji) -> field('xueqi') -> group('xueqi')->select();

		$data = C('CONST_XUEQI');
		$this->ajaxReturn($data); 
	}

	public function kcbanben(){
		$nianji = I("nianji/s");
		$xueqi = I("xueqi/s");
		$Z = M("ziversion"); // 实例化User对象  
		$data = $Z ->where('id in ( SELECT versionid FROM yw_kecheng where nianji= "%s" and xueqi = "%s")' , $nianji, $xueqi) -> field('id,banben') ->order('sortid')->select();
		$this->ajaxReturn($data);
	}

	public function kckecheng(){	
		$nianji = I("nianji/s");
		$xueqi = I("xueqi/s");
		$versionid = I("versionid/d");
		$Z = M("kecheng"); // 实例化User对象  
		$data = $Z ->where('nianji = "%s" and xueqi = "%s" and versionid = %d',$nianji,$xueqi,$versionid)->field('id,kecheng,sortid,url')->order('sortid')->select();
		$this->ajaxReturn($data);
	}

	public function kczi(){	
		$kechengid = I("kechengid/d",0);  
		$Model=M();
		$sql='SELECT yw_kecheng_hanzi.id,yw_kecheng_hanzi.sortid,yw_zi.zi,yw_ziyin.pinyin,yw_ziyin.wupinyin,yw_ziyin.shengdiao ';
		$sql.=' FROM yw_kecheng_hanzi ,yw_zi, yw_ziyin ';
		$sql.=' WHERE yw_kecheng_hanzi.zid = yw_zi.id AND yw_kecheng_hanzi.ziyinid = yw_ziyin.id ';
		$sql.=' AND yw_kecheng_hanzi.kechengid = %d ';
		$sql.=' ORDER BY yw_kecheng_hanzi.sortid,yw_ziyin.id';		 
		$rs = $Model->query($sql,$kechengid);
		$this->ajaxReturn($rs);
	}

	public function getversion(){ 
		$Z = M("ziversion"); // 实例化User对象  
		$data = $Z->order('sortid')->select();
		$this->ajaxReturn($data);
	}
 
	public function upversionsort(){	
		$data = I("data/s","");  
		$arr = json_decode(str_replace("&quot;", '"', $data),ture); 
     	foreach($arr as $v){ 
        	$id = $v["id"]; 
        	$sortid = $v["sortid"]; 
         
        	$id = !is_numeric($id)?0:$id;
        	$sortid = !is_numeric($sortid)?0:$sortid; 

        	$Kchz=M("ziversion");
        	$Kchz->id=$id;  
        	$Kchz->sortid=$sortid;  
            $Kchz->save();  
    	}
    	$arr_return["msg"]=1;
        $arr_return["err"]="修改成功";
        $this -> ajaxReturn($arr_return); 
	}

	public function addversion(){	
		$data = I("data/s","");    
        $Version=M("ziversion");
    	$Version->banben=$data;  
    	$Version->sortid= 100;  
        $id = $Version -> add(); 

    	$arr_return["msg"]=1;
        $arr_return["err"]="添加";
        $this -> ajaxReturn($arr_return); 
	} 
	

	public function editversion(){	
		$data = I("data/s","");  
		$id = I("id/d","");    

        $Version=M("ziversion");
    	$Version->banben=$data;   
        $Version -> where("id=%d",$id) ->  save();

    	$arr_return["msg"]=1;
        $arr_return["err"]="添加";
        $this -> ajaxReturn($arr_return); 
	}  

	public function upsort(){	
		$data = I("data/s","");  
		$arr = json_decode(str_replace("&quot;", '"', $data),ture); 
     	foreach($arr as $v){ 
        	$id = $v["id"]; 
        	$sortid = $v["sortid"]; 
         
        	$id = !is_numeric($id)?0:$id;
        	$sortid = !is_numeric($sortid)?0:$sortid; 

        	$Kchz=M("kecheng_hanzi");
        	$Kchz->id=$id;  
        	$Kchz->sortid=$sortid;  
            $Kchz->save();  
    	}
    	$arr_return["msg"]=1;
        $arr_return["err"]="修改成功";
        $this -> ajaxReturn($arr_return); 
	}


	public function checkdata(){	
		 //不是AJAX请求则不返回数据
		//if (!IS_AJAX){$this->ajaxReturn();}
        $arrrows =  explode("\n", I("zilist/s",""));	
        $data = array();	
		foreach ($arrrows as $row => $v) { 
			$arr =  explode("	", $v); 
			$arr["zi"] = $arr[0];
			$arr["wupinyin"] = $arr[1];
			$arr["shengdiao"]= $arr[2];
			$arr["isyes"] = 0;
			 
			$Model=M();
			$sql='SELECT yw_zi.id FROM yw_zi,yw_ziyin ';
			$sql.='WHERE yw_zi.id = yw_ziyin.zid ';
			$sql.='AND yw_zi.zi="%s" and yw_ziyin.wupinyin="%s" and shengdiao = %d'; 
			$rs = $Model->query($sql,$arr["zi"],$arr["wupinyin"],$arr["shengdiao"]);
			if (!empty($rs)) {			
				$arr["isyes"] = 1;
			}
			$data[]=$arr;			 
		} 
		$this->ajaxReturn($data);
	}

	public function flashdown(){
		$kechengid = I("kechengid/d",0); 

		$Kecheng = M("kecheng"); // 实例化User对象  
		$data = $Kecheng ->where('id =%d',$kechengid)->find();
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

		$arr_template =  C('CONST_ZIFLASH');		
		$template = $arr_template[$nianji];
		if (empty($template)) {
		  exit();
		} 

		Vendor("fileDirUtil");		
		$dir = new \fileDirUtil();

		$todir ='./flash/' . $kechengid;
		$dir->unlinkDir($todir); 		
 		$dir->copyDir('./Template/'.$template,$todir,true);


 		$asfile = $todir . '/Main.as';
 		//写入AS文件
 		$as = $dir->readsFile($asfile);  
 		 
 		$jsflfile = $todir . '/run.jsfl';
 		//写入AS文件
 		$jsfl = $dir->readsFile($jsflfile);   

 		$writestring = '' . "\r\n			" ;
 		$writestring .= 'classText.text = String("'.$nianji.'-'.$banben.'-' . $xueqi . '");' . "\r\n			" ;
 		$writestring .= 'titleText.text = String("' . $kecheng . '");' . "\r\n			" ;



 		$jsfl_importfile = '';
 		$jsfl_importfile_name = ''; 


 		$Zi = M("zi"); // 实例化User对象  
		$arr_zi = $Zi ->where('id =%d',$kechengid)->select();
		$Model=M();
		$sql=' SELECT yw_kecheng_hanzi.zid as zid,yw_kecheng_hanzi.ziyinid,sortid,yw_zi.* ';
		$sql.=' FROM yw_kecheng_hanzi,yw_zi ';
		$sql.=' WHERE yw_zi.id = yw_kecheng_hanzi.zid AND yw_zi.bihuaswf <> "" AND yw_kecheng_hanzi.kechengid = %d ';
		$sql.=' ORDER BY yw_kecheng_hanzi.sortid';		 
		$arr_zi = $Model->query($sql,$kechengid);

		$str='';
		for ($i=0;$i<count($arr_zi);$i++){
			$str.='[],';
		}
		$str=trim($str,',');
		$musicarr='var muiscarr:Array=new Array('.$str.');';
		$pyarr='var py:Array = new Array('.$str.');';
		$zyarr='var zy:Array = new Array('.$str.');';
		$zcarr='var zc:Array = new Array('.$str.');';
		$zjarr='var zj:Array = new Array('.$str.');'; 
		
		$i_zi = 0;
		foreach ($arr_zi as $value) {
			$zi =  $value["zi"];
			$zid =  $value["zid"];
			$ziyinid = $value["ziyinid"];

			$writestring .= 'num['.$i_zi.'] = "'. $value["zi"] .'";' . "\r\n			" ; 
			$writestring .= 'bh['.$i_zi.'] = "'. $value["bihuashu"] .'";' . "\r\n			" ; 
			$writestring .= 'bs['.$i_zi.'] = "'. $value["pianpang"] .'";' . "\r\n			" ; 
			$writestring .= 'zd['.$i_zi.'] = "'. $value["xiezi"] .'";' . "\r\n			" ; 
			$writestring .= 'sz['.$i_zi.'] = "'. $value["shizi"] .'";' . "\r\n			" ; 

 
			$dir->copyFile('./'.$value["bihuaswf"], $todir.'/swf'. $zid.'.swf' ,true);

			$writestring .= '[Embed(source = "swf'. $zid.'.swf")]' . "\r\n			" ; 
			$writestring .= 'var bhembed'.$i_zi.':Class;' . "\r\n			" ; 
			$writestring .= 'bhmovieclip['.$i_zi.'] = bhembed'.$i_zi.';' . "\r\n			" ;  

 			
 			//取拼音本音的基本数据
			$i_ziyin = 0;
			$Ziyin = M("ziyin"); // 实例化User对象  
			$vziyin = $Ziyin->where('id =%d',$ziyinid)->find();


			$writestring .= 'py['.$i_zi.']['.$i_ziyin.'] = "' .$vziyin["pinyin"]. '";' . "\r\n			" ;
 
			$zuci = $vziyin["zuci1"] . '\n' . $vziyin["zuci2"] . '\n' . $vziyin["zuci3"] . '\n';			
			$zuci = rtrim(str_replace('\n\n','\n', $zuci),'\n');
			$writestring .= 'zc['.$i_zi.']['.$i_ziyin.'] = "' .$zuci.'";' . "\r\n			" ;

			$ziyi = $vziyin["ziyi1"] . '\n' . $vziyin["ziyi2"] . '\n' . $vziyin["ziyi3"] . '\n';			
			$ziyi = rtrim(str_replace('\n\n','\n', $ziyi),'\n');
			$writestring .= 'zy['.$i_zi.']['.$i_ziyin.'] = "' .$ziyi.'";' . "\r\n			" ;


			$zaoju = $vziyin["zaoju1"] . '\n' . $vziyin["zaoju2"] . '\n' . $vziyin["zaoju3"] . '\n';			
			$zaoju = rtrim(str_replace('\n\n','\n', $zaoju),'\n');
			$writestring .= 'zj['.$i_zi.']['.$i_ziyin.'] = "' .$zaoju.'";' . "\r\n			" ;

			$writestring .= 'muiscarr['.$i_zi.']['.$i_ziyin.'] = "w' . $ziyinid .'";' . "\r\n			" ;


			$dir->copyFile('./'.$vziyin["wav"], $todir.'/wav/'. basename($vziyin["wav"]),true);
			 
			$jsfl_importfile .= 'doc.importFile(JSFL_PATH + "wav/" + "' . basename($vziyin["wav"]) . '", true);'. "\r\n"; 

 			$jsfl_importfile_name .= 'li = lib.items[lib.findItemIndex("' . basename($vziyin["wav"]) . '")];'. "\r\n";
 			$jsfl_importfile_name .= 'li.linkageExportForAS = true;'. "\r\n";  
 			$jsfl_importfile_name .= 'li.linkageExportForRS = false;'. "\r\n";
 			$jsfl_importfile_name .= 'li.linkageExportInFirstFrame = true;'. "\r\n"; 
 			$jsfl_importfile_name .= 'li.linkageClassName = "w' . $ziyinid .'"; '. "\r\n"; 
			 
			$i_ziyin++;
			
			//多音字的处理
			$Ziyin = M("ziyin");
			$duoyin = $Ziyin->where('id <> %d and zid = %d',$ziyinid, $zid)->fetchSql(false)->select();
		 
			foreach ($duoyin as $vziyin) {
				$writestring .= 'py['.$i_zi.']['.$i_ziyin.'] = "' .$vziyin["pinyin"]. '";' . "\r\n			" ;

				$zuci = $vziyin["zuci1"] . '\n' . $vziyin["zuci2"] . '\n' . $vziyin["zuci3"] . '\n';			
				$zuci = rtrim(str_replace('\n\n','\n', $zuci),'\n');
				$writestring .= 'zc['.$i_zi.']['.$i_ziyin.'] = "' .$zuci.'";' . "\r\n			" ;

				$ziyi = $vziyin["ziyi1"] . '\n' . $vziyin["ziyi2"] . '\n' . $vziyin["ziyi3"] . '\n';			
				$ziyi = rtrim(str_replace('\n\n','\n', $ziyi),'\n');
				$writestring .= 'zy['.$i_zi.']['.$i_ziyin.'] = "' .$ziyi.'";' . "\r\n			" ;


				$zaoju = $vziyin["zaoju1"] . '\n' . $vziyin["zaoju2"] . '\n' . $vziyin["zaoju3"] . '\n';			
				$zaoju = rtrim(str_replace('\n\n','\n', $zaoju),'\n');
				$writestring .= 'zj['.$i_zi.']['.$i_ziyin.'] = "' .$zaoju.'";' . "\r\n			" ;

				$writestring .= 'muiscarr['.$i_zi.']['.$i_ziyin.'] = "w' . $vziyin["id"] .'";' . "\r\n			" ;
				 
				$dir->copyFile('./'.$vziyin["wav"], $todir.'/wav/'. basename($vziyin["wav"]),true);
				$jsfl_importfile .= 'doc.importFile(JSFL_PATH + "wav/" + "' . basename($vziyin["wav"]) . '", true);'. "\r\n"; 

	 			$jsfl_importfile_name .= 'li = lib.items[lib.findItemIndex("' . basename($vziyin["wav"]) . '")];'. "\r\n";
	 			$jsfl_importfile_name .= 'li.linkageExportForAS = true;'. "\r\n";  
	 			$jsfl_importfile_name .= 'li.linkageExportForRS = false;'. "\r\n";
	 			$jsfl_importfile_name .= 'li.linkageExportInFirstFrame = true;'. "\r\n"; 
	 			$jsfl_importfile_name .= 'li.linkageClassName = "w' . $vziyin["id"] .'"; '. "\r\n"; 

				$i_ziyin++;
			}

			$i_zi++;
		}
 

 		$as = str_replace('$$musicarr$$', $musicarr, $as);
 		$as = str_replace('$$pyarr$$', $pyarr, $as);
 		$as = str_replace('$$zyarr$$', $zyarr, $as);
 		$as = str_replace('$$zcarr$$', $zcarr, $as);
 		$as = str_replace('$$zjarr$$', $zjarr, $as);
 		$as = str_replace('$$writestring$$', $writestring, $as);
 		$dir->writeFile($asfile, $as);

 		$jsfl = str_replace('$$jsfl_importfile$$', $jsfl_importfile, $jsfl);
 		$jsfl = str_replace('$$jsfl_importfile_name$$', $jsfl_importfile_name, $jsfl);
 		$jsfl = str_replace('$$flash_name$$',$kecheng,$jsfl);
 		$dir->writeFile($jsflfile, $jsfl);

		Vendor("PHPZip");		
		$Zip = new \PHPZip();
		$zifile = './flash/' . $kechengid . '.zip';
		$dir->unlinkDir($zifile);
	    $Zip->Zip($todir, $zifile); 


		$this->download ($zifile, ' ' . $nianji.'-'.$banben.'-'.$xueqi.'-'.$kecheng . '.zip');
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
    
    //提交排序
    public function updateSort(){
    	$sortsInfo=I('sortsInfo/s',0);
    	$sortsInfo=str_replace('&quot;', '"', $sortsInfo);
    	foreach (json_decode($sortsInfo,true) as $v){
    		$Model=M("kecheng");
    		$data['sortid']=$v['sortid'];
    		$Model->where('id="%d"',$v['id'])->save($data);
    	}
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
     * 导入flash写入数据库
     */
    public function  importFlashData(){
    	$id=I('id/d');
    	$url=I('url/s');
    	$m=M("kecheng");
    	$da=$m->where('id="%d"',$id)->field('url')->find();
    	$data['id']=$id;
    	$data['url']=$url;
    	if (is_array($da)){
    		$m->where('id="%d"',$id)->save($data);
    	}else {
    		$m->add($data);
    	}
    }
    /**
     * 删除课程
     */
    public function delKecheng(){
    	$id=I('id/d');
    	$m=M('kecheng');
    	$m->delete($id);
    }
    
    
}