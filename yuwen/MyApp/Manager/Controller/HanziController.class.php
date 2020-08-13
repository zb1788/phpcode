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
		$re["2wav"] = '请上传mp3文件';

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
    		$sql = 'UPDATE yw_zi SET pianpangurl ="'.C('CONST_BUSHOU')[$arr['pianpang']].'" where pianpang="'.$arr['pianpang'].'";';
			$Z->execute($sql);
    	}
    	else
    	{
    		$Z->save($arr);
    		$sql = 'UPDATE yw_zi SET pianpangurl ="'.C('CONST_BUSHOU')[$arr['pianpang']].'" where pianpang="'.$arr['pianpang'].'";';

			$Z->execute($sql);
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

		$template = 1;//生字只用一套模版

		Vendor("fileDirUtil");
		$dir = new \fileDirUtil();

		$todir ='./flash/' . $kechengid;
		$dir->unlinkDir($todir);
 		$dir->copyDir('./Template/'.$template,$todir,true);


 		$asfile = $todir . '/com/DataClass.as';
 		//写入AS文件
 		$as = $dir->readsFile($asfile);

 		$jsflfile = $todir . '/run.jsfl';
 		//写入AS文件
 		$jsfl = $dir->readsFile($jsflfile);

 		$writestring = '' . "\r\n			" ;
 		//$writestring .= 'classText.text = String("'.$nianji.'-'.$banben.'-' . $xueqi . '");' . "\r\n			" ;
 		//$writestring .= 'titleText.text = String("' . $kecheng . '");' . "\r\n			" ;
 		$biaoti = 'public var biaoTi:String="' . $kecheng . '";'."\r\n			";
 		$biaoti .='public var TZG_I:Array=new Array();'."\r\n			";


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
		$musicarr='public var muiscarr:Array=new Array('.$str.');';
		$pyarr='public var py:Array = new Array('.$str.');';
		$zyarr='public var zy:Array = new Array('.$str.');';
		$zcarr='public var zc:Array = new Array('.$str.');';
		$zjarr='public var zj:Array = new Array('.$str.');';

		$i_zi = 0;
		foreach ($arr_zi as $value) {
			$zi =  $value["zi"];
			$zid =  $value["zid"];
			$ziyinid = $value["ziyinid"];

			$writestring .= 'num['.$i_zi.'] = "'. $value["zi"] .'";' . "\r\n			" ;
			$writestring .= 'TZG_I['.$i_zi.']=0;' . "\r\n			" ;
			$writestring .= 'bh['.$i_zi.'] = "'. $value["bihuashu"] .'";' . "\r\n			" ;

			$writestring .= 'zd['.$i_zi.'] = "'. $value["xiezi"] .'";' . "\r\n			" ;
			$writestring .= 'sz['.$i_zi.'] = "'. $value["shizi"] .'";' . "\r\n			" ;


			$dir->copyFile('./'.$value["bihuaswf"], $todir.'/swf'. $zid.'.swf' ,true);

			$writestring .= '[Embed(source = "../swf'. $zid.'.swf")]' . "\r\n			" ;
			$writestring .= 'var bhembed'.$i_zi.':Class;' . "\r\n			" ;
			$writestring .= 'bhmovieclip['.$i_zi.'] = bhembed'.$i_zi.';' . "\r\n			" ;

			// if($zid==2995){
			// echo './uploads/zibushou/'. $value["pianpangurl"].'||'.$todir.'/pic/'. $zid.'.png';exit;
			// }
			$dir->copyFile('./uploads/zibushou/'. $value["pianpangurl"] , $todir.'/pic/'. $zid.'.png' ,true);
			$writestring .= '[Embed(source = "../pic/'. $zid.'.png")]' . "\r\n			" ;
			$writestring .= 'var bushou'.$i_zi.':Class;' . "\r\n			" ;
			$writestring .= 'bs['.$i_zi.'] = bushou'.$i_zi.';' . "\r\n			" ;


 			//取拼音本音的基本数据
			$i_ziyin = 0;
			$Ziyin = M("ziyin"); // 实例化User对象
			$vziyin = $Ziyin->where('id =%d',$ziyinid)->find();


			$writestring .= 'py['.$i_zi.']['.$i_ziyin.'] = "' .$vziyin["pinyin"]. '";' . "\r\n			" ;

 			$zuci = 'new Array( "'.$vziyin["zuci1"] . '","' . $vziyin["zuci2"] . '","' . $vziyin["zuci3"] . '")';
			$zuci = str_replace(array(',"",""',',""'),'',$zuci);
			$writestring .= 'zc['.$i_zi.']['.$i_ziyin.'] = ' .$zuci.';' . "\r\n			" ;


			$ziyi = 'new Array( "'.$vziyin["ziyi1"] . '","' . $vziyin["ziyi2"] . '","' . $vziyin["ziyi3"] . '")';
			$ziyi = str_replace(array(',"",""',',""'),'',$ziyi);
			$writestring .= 'zy['.$i_zi.']['.$i_ziyin.'] = ' .$ziyi.';' . "\r\n			" ;


			$zaoju = 'new Array( "'.$vziyin["zaoju1"] . '","' . $vziyin["zaoju2"] . '","' . $vziyin["zaoju3"] . '")';
			$zaoju = str_replace(array(',"",""',',""'),'',$zaoju);
			$writestring .= 'zj['.$i_zi.']['.$i_ziyin.'] = ' .$zaoju.';' . "\r\n			" ;

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


				$zuci = 'new Array( "'.$vziyin["zuci1"] . '","' . $vziyin["zuci2"] . '","' . $vziyin["zuci3"] . '")';
				$zuci = str_replace(array(',"",""',',""'),'',$zuci);
				$writestring .= 'zc['.$i_zi.']['.$i_ziyin.'] = ' .$zuci.';' . "\r\n			" ;


				$ziyi = 'new Array( "'.$vziyin["ziyi1"] . '","' . $vziyin["ziyi2"] . '","' . $vziyin["ziyi3"] . '")';
				$ziyi = str_replace(array(',"",""',',""'),'',$ziyi);
				$writestring .= 'zy['.$i_zi.']['.$i_ziyin.'] = ' .$ziyi.';' . "\r\n			" ;

				$zaoju = 'new Array( "'.$vziyin["zaoju1"] . '","' . $vziyin["zaoju2"] . '","' . $vziyin["zaoju3"] . '");';
				$zaoju = str_replace(array(',"",""',',""'),'',$zaoju);
				$writestring .= 'zj['.$i_zi.']['.$i_ziyin.'] = ' .$zaoju.';' . "\r\n			" ;

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

		$as = str_replace('$$biaoti$$',$biaoti,$as);
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


	public function addPoints(){
		header('Access-Control-Allow-Origin: *');
		$zi = I('zi/d',0);
		$points = I('points/s','');

		$code = 200;
		$msg = '';
		if(empty($zi)){
			$msg = '参数zi不能为空';
			$code = 500;
		}

		if(empty($points)){
			$msg = '参数points不能为空';
			$code = 500;
		}

		if($code == 200){
			$json = str_replace("&quot;", '"', $points);
		
			$ziInfo = M('zi_info');
			$data = $ziInfo->where('zid="%d"',$zi)->find();

			$re = array();
			$re['zid'] = $zi;
			$re['points'] = $json;

			if(empty($data)){
				$ziInfo->add($re);
				$msg = '添加成功';
			}else{
				$ziInfo->where('id="%d"',$data['id'])->save($re);
				$msg = "修改成功";
			}
			$this->getZiInfoById($zi);
		}



	    $arr_return["code"]=$code;
        $arr_return["msg"]=$msg;
        $this -> ajaxReturn($arr_return);
	}


	public function getZiPoint(){
		header('Access-Control-Allow-Origin: *');
		$id=I('id/d');
		$ziInfo = M('zi_info');
		$data = $ziInfo->field('points')->where('zid="%d"',$id)->find();
		$this->ajaxReturn($data);
	}



	public function getZiInfoByKsId($ksId,$todir){

	 	$Zi = M("zi"); // 实例化User对象
		$arr_zi = $Zi ->where('id =%d',$ksId)->select();
		$Model=M();
		$sql=' SELECT yw_kecheng_hanzi.zid as zid,yw_kecheng_hanzi.ziyinid,sortid,yw_zi.* ';
		$sql.=' FROM yw_kecheng_hanzi,yw_zi ';
		$sql.=' WHERE yw_zi.id = yw_kecheng_hanzi.zid AND yw_kecheng_hanzi.kechengid = %d ';
		$sql.=' ORDER BY yw_kecheng_hanzi.sortid';
		$arr_zi = $Model->query($sql,$ksId);

		//var_dump($arr_zi);exit;

		$result = array();
		foreach ($arr_zi as $value) {
			$item = array();
			$item['zid'] = $value['zid'];
			$item['sortid'] = $value['sortid'];
			$item['zi'] = $value['zi'];
			$item['pianpang'] = $value['pianpang'];
			$item['bihuashu'] = $value['bihuashu'];
			$item['jiegou'] = $value['jiegou'];
			$item['bishun'] = $value['bishun'];
			$item['shizi'] = $value['shizi'];
			$item['xiezi'] = $value['xiezi'];

			//$item['zi'] = iconv('utf-8', 'gbk', $value['zi']);
			//$item['pianpang'] = iconv('utf-8', 'gbk', $value['pianpang']);
			//$item['bihuashu'] = iconv('utf-8', 'gbk', $value['bihuashu']);
			//$item['shizi'] = iconv('utf-8', 'gbk', $value['shizi']);
			//$item['xiezi'] = iconv('utf-8', 'gbk', $value['xiezi']);
			$item['pianpangurl'] = $value['pianpangurl'];

			//查询发音信息
			$sql='SELECT n.id,n.pinyin,n.wav,n.zaoju1,n.zaoju2,n.zaoju3,n.zuci1,n.zuci2,n.zuci3,n.ziyi1,n.ziyi2,n.ziyi3,if(n.id="%d",1,0) as used FROM yw_ziyin n WHERE n.zid="%d" order by used desc';
			$data_ziyin = $Model->query($sql,$value['ziyinid'],$value['zid']);

			//var_dump($data_ziyin);exit;
			/**
			$ziyinArr = array();
			$wav = array();
			$pinyin = array();
			$ziyi = array();
			$zaoju = array();
			$zuci = array();
			foreach($data_ziyin as $val){
				array_push($wav,$val['wav']);
				array_push($pinyin,$val['pinyin']);
				array_push($ziyi,$val['ziyi']);
				array_push($zaoju,$val['zaoju']);
				array_push($zuci,$val['zuci']);
			}
			
			$ziyinArr['wav'] = $wav;
			$ziyinArr['pinyin'] = $pinyin;
			$ziyinArr['ziyi'] = $ziyi;
			$ziyinArr['zaoju'] = $zaoju;
			$ziyinArr['zuci'] = $zuci;

			$item['ziyin'] = $ziyinArr;
			*/

			foreach($data_ziyin as $key=>$val){
				$data_ziyin[$key]['wav'] = str_replace('.wav','.mp3',basename($val['wav']));
				$mp3 = str_replace('.wav','.mp3',basename($val['wav']));

				if(pathinfo(basename($val['wav']),PATHINFO_EXTENSION) == 'wav'){
					if(file_exists('./szT/audio/'.$mp3)){
						copy('./szT/audio/'.$mp3,$todir.'/'.$mp3);
					}else{
						file_put_contents('szh.txt', 'no audio|'.$mp3);
					}
				}else{
					if(file_exists($val['wav'])){
						copy($val['wav'],$todir.'/'.$mp3);
					}else{
						file_put_contents('szh.txt', 'no audio|'.$mp3);
					}
				}

			}
    	


			//var_dump($data_ziyin);exit;


			$item['ziyin'] = $data_ziyin;

			$sql = 'SELECT points FROM yw_zi_info WHERE zid="%d"';
			$data_points = $Model->query($sql,$value['zid']);
			
			$item['points'] = $data_points;

			//var_dump($item);exit;

			array_push($result,$item);
		}
		
//		var_dump(json_encode($result));exit;
//		var_dump($result);exit;
		return json_encode($result,JSON_UNESCAPED_UNICODE);
	}


	public function downloadZiJson(){
		set_time_limit(0);

		$ksId = I('ksId/d',0);

		Vendor("fileDirUtil");
		$dir = new \fileDirUtil();

		$todir = 'szh/'.$ksId;
		if(!is_dir($todir)){
			mkdir($todir);
		}else{
			$dir->unlinkDir($todir);
			mkdir($todir);
		}

		$json = $this->getZiInfoByKsId($ksId,$todir);
		//"\xEF\xBB\xBF".
		file_put_contents($todir.'/'.$ksId.'json',$json);

        Vendor("PHPZip");
        $Zip = new \PHPZip();
        $zifile = './szh/zip/' . $ksId . '.zip';

		//echo $zifile;exit;
        if(file_exists($zifile)){
            $dir->unlinkDir($zifile);
        }


        $Zip->Zip('szh/'.$ksId, $zifile);

        $this->download ($zifile, ' ' . $ksId . '.zip');
	}


	public function getZiInfoById($id){
		Vendor("fileDirUtil");
		$dir = new \fileDirUtil();

		$todir = 'zijson/'.$id;
		$viewdir = 'D:/wwwroot/shengzi/resource/assets/HanZiData/'.$id;
		if(!is_dir($todir) && !is_dir($viewdir)){
			mkdir($todir);
			mkdir($viewdir);
		}else{
			$dir->unlinkDir($todir);
			mkdir($todir);
			$dir->unlinkDir($viewdir);
			mkdir($viewdir);
		}
	
		$Model=M();

	 	$Zi = M("zi"); // 实例化User对象
		$value = $Zi ->where('id =%d',$id)->find();

		$item = array();
		$item['zid'] = $value['id'];
		$item['zi'] = $value['zi'];
		$item['pianpang'] = $value['pianpang'];
		$item['bihuashu'] = $value['bihuashu'];
		$item['jiegou'] = $value['jiegou'];
		$item['bishun'] = $value['bishun'];
		$item['shizi'] = $value['shizi'];
		$item['xiezi'] = $value['xiezi'];



		//查询发音信息
		$sql='SELECT n.id,n.pinyin,n.wav,n.zaoju1,n.zaoju2,n.zaoju3,n.zuci1,n.zuci2,n.zuci3,n.ziyi1,n.ziyi2,n.ziyi3 FROM yw_ziyin n WHERE n.zid="%d"';
		$data_ziyin = $Model->query($sql,$value['id']);



		foreach($data_ziyin as $key=>$val){
			$data_ziyin[$key]['wav'] = str_replace('.wav','.mp3',basename($val['wav']));
			$mp3 = str_replace('.wav','.mp3',basename($val['wav']));

			if(pathinfo(basename($val['wav']),PATHINFO_EXTENSION) == 'wav'){
				if(file_exists('./szT/audio/'.$mp3)){
					copy('./szT/audio/'.$mp3,$todir.'/'.$mp3);
					copy('./szT/audio/'.$mp3,$viewdir.'/'.$mp3);
				}else{
					file_put_contents('szh.txt', 'no audio|'.$mp3);
				}
			}else{
				if(file_exists($val['wav'])){
					copy($val['wav'],$todir.'/'.$mp3);
					copy($val['wav'],$viewdir.'/'.$mp3);
				}else{
					file_put_contents('szh.txt', 'no audio|'.$mp3);
				}
			}

		}
    	
		$item['ziyin'] = $data_ziyin;

		$sql = 'SELECT points FROM yw_zi_info WHERE zid="%d"';
		$data_points = $Model->query($sql,$value['id']);
		
		$item['points'] = $data_points;
		
		$json = json_encode($item,JSON_UNESCAPED_UNICODE);
		
		file_put_contents($todir.'/'.$id.'.json',$json);
		file_put_contents($viewdir.'/'.$id.'.json',$json);
	}


	public function getZiJson(){
		$id = I('id/d',0);
		$this->getZiInfoById($id);
	}
	public function downloadZiListJson(){
		set_time_limit(0);
		echo 'making......please sleep';
		ob_flush();
		flush();
		$zi = M('zi');
		$data = $zi->field('id')->select();
		foreach($data as $k=>$v){
			$this->getZiInfoById($v['id']);
			if($k%250==0){
				echo '.<br/>';
			}else{
				echo '.';
			}
			
			ob_flush();
			flush();
		}

		echo '<br/>OK';
	}


	public function getKsIdAndZi(){
		set_time_limit(0);
		echo 'making......please sleep';
		ob_flush();
		flush();
		$Kchz = M('kecheng_hanzi');
		$kc = M('kecheng');
		$data = $kc->field('id')->where('versionid=2')->select();

		$result = array();
		foreach($data as $v){
			$re = $Kchz->field('zid,ziyinid')->where('kechengid="%d"',$v['id'])->select();
			$result['k'.$v['id']] = $re;
			echo '.';
			ob_flush();
			flush();
		}


		$json = json_encode($result,JSON_UNESCAPED_UNICODE);
		//echo $json;
		file_put_contents('zijson/kc.json',$json);
		file_put_contents('D:/wwwroot/shengzi/resource/assets/kc.json',$json);
		echo '<br/>OK';
	}




	//遍历ftp，导入图片
    public function importPics(){
		header("Content-Type:text/html;charset=utf-8");
		set_time_limit(0);

		echo '开始批量导入...请稍等...';
		ob_flush();
		flush();

    	vendor('fileDirUtil');
    	$dir = new \fileDirUtil();
    	$dirs=$dir->dirNodeTree('TmpUploads');//目录数组

    	$errorinfo=array();//错误信息数组
    	$success=array();//成功信息数组

		for ($i=0;$i<count($dirs);$i++){
			$wenjianjia = $dirs[$i];

		    //$mp3s=$dir->dirList('TmpUploads/'.$dirs[$i],'mp3');//本目录下的所有图片

			$basepath = 'TmpUploads/'.$wenjianjia; 
    		$excelpath='TmpUploads/'.$wenjianjia.'/1.xlsx';//excel路径
			
			
			if(file_exists($excelpath)){
    			$this->importExcelData($excelpath,$basepath);
    		}else{
				array_push($errorinfo, 'excel不存在<br/>');
    			continue;
			}
			$dir->unlinkDir($basepath);
		}
		echo '导入完成!';
		
	}



    /**
     * 批量excel
     */
    public function importExcelData($path,$basepath){
    	vendor('PHPExcel');
		$dir = new \fileDirUtil();

		$jiegouArr['独体字'] = '1.mp3';
		$jiegouArr['上下'] = '2.mp3';
		$jiegouArr['上中下'] = '3.mp3';
		$jiegouArr['左右'] = '4.mp3';
		$jiegouArr['左中右'] = '5.mp3';
		$jiegouArr['半包围'] = '6.mp3';
		$jiegouArr['嵌套'] = '7.mp3';
		$jiegouArr['品字体'] = '8.mp3';


    	$arr_excel = readExcel($path);


    	$insert_flag = true; //是否写数据库标志
    	$errorinfo = array(); //错误信息数组
    	$success_num = 0; //导入成功数

        $mzi = M('zi');
		$mziyin = M('ziyin');

    	foreach ($arr_excel as $row=>$v){
    		$zi=$v['Column']['A']; //字
    		$pinyin=$v['Column']['B'];//拼音
			$wupinyin=$v['Column']['C'];//无拼音
			$shengdiao=$v['Column']['D'];//声调
    		$bihuashu=$v['Column']['E'];//笔画数
    		$bushou=$v['Column']['F'];//部首
			$jiegou=$v['Column']['G'];//结构
			$bishun=$v['Column']['H'];//笔顺
			$shizi=$v['Column']['I'];//识字方法
			$xiezizhidao=$v['Column']['J'];//写字指导
			$ziyi1=$v['Column']['K'];//字义1
			$zuci1=$v['Column']['L'];//组词1
			$zaoju1=$v['Column']['M'];//造句1
			$ziyi2=$v['Column']['N'];//字义2
			$zuci2=$v['Column']['O'];//组词2
			$zaoju2=$v['Column']['P'];//造句2
			$ziyi3=$v['Column']['Q'];//字义3
			$zuci3=$v['Column']['R'];//组词3
			$zaoju3=$v['Column']['S'];//造句3
			$shiziyin=$v['Column']['T'];//识字方法音
			$shizitu=$v['Column']['U'];//识字方法图
			$shizishipin=$v['Column']['V'];//识字方法视频
			$xiezizhidaoyin=$v['Column']['W'];//写字指导音
			$ziyiyin1=$v['Column']['X'];//字义1音
			$zuciyin1=$v['Column']['Y'];//组词1音
			$zaojuyin1=$v['Column']['Z'];//造句1音
			$ziyiyin2=$v['Column']['AA'];//字义2音
			$zuciyin2=$v['Column']['AB'];//组词2音
			$zaojuyin2=$v['Column']['AC'];//造句2音
			$ziyiyin3=$v['Column']['AD'];//字义3音
			$zuciyin3=$v['Column']['AE'];//组词3音
			$zaojuyin3=$v['Column']['AF'];//造句3音



    		/**
    		 * 判断excel里是否有空值
    		 */
    		if ($zi == ""){
    			array_push($errorinfo, '字不能为空' . '|' . $row); //记录错误信息
				$this->flushText('《<span style="color:red;">'.$zi.'</span>》字不能为空<br/>');
    			continue;//如果当前算式为空，跳出本次循环
    		}
    		if ($pinyin == ""){
    			array_push($errorinfo, '拼音不能为空' . '|' . $row); //记录错误信息
				$this->flushText('《<span style="color:red;">'.$zi.'</span>》拼音不能为空<br/>');
    			continue;//如果当前结果为空，跳出本次循环
    		}
    		if ($wupinyin == ""){
    			array_push($errorinfo, '无拼音不能为空' . '|' . $row); //记录错误信息
				$this->flushText('《<span style="color:red;">'.$zi.'</span>》无拼音不能为空<br/>');
    			continue;//如果当前结果为空，跳出本次循环
    		}
    		if ($shengdiao == ""){
    			array_push($errorinfo, '声调不能为空' . '|' . $row); //记录错误信息
				$this->flushText('《<span style="color:red;">'.$zi.'</span>》声调不能为空<br/>');
    			continue;//如果当前结果为空，跳出本次循环
    		}
    		if ($bihuashu === ""){
    			array_push($errorinfo, '笔画数不能为空' . '|' . $row); //记录错误信息
				$this->flushText('《<span style="color:red;">'.$zi.'</span>》笔画数不能为空<br/>');
    			continue;//如果当前结果为空，跳出本次循环
    		}
    		if ($bushou == ""){
    			array_push($errorinfo, '部首不能为空' . '|' . $row); //记录错误信息
				$this->flushText('《<span style="color:red;">'.$zi.'</span>》部首不能为空<br/>');
    			continue;//如果当前结果为空，跳出本次循环
    		}
    		if ($jiegou == ""){
    			array_push($errorinfo, '结构不能为空' . '|' . $row); //记录错误信息
				$this->flushText('《<span style="color:red;">'.$zi.'</span>》结构不能为空<br/>');
    			continue;//如果当前结果为空，跳出本次循环
    		}
    		if ($bishun == ""){
    			array_push($errorinfo, '笔顺不能为空' . '|' . $row); //记录错误信息
				$this->flushText('《<span style="color:red;">'.$zi.'</span>》笔顺不能为空<br/>');
    			continue;//如果当前结果为空，跳出本次循环
    		}
    		if ($ziyi1 == ""){
    			array_push($errorinfo, '字义1不能为空' . '|' . $row); //记录错误信息
				$this->flushText('《<span style="color:red;">'.$zi.'</span>》字义1不能为空<br/>');
    			continue;//如果当前结果为空，跳出本次循环
    		}
    		if ($zuci1 == ""){
    			array_push($errorinfo, '组词1不能为空' . '|' . $row); //记录错误信息
				$this->flushText('《<span style="color:red;">'.$zi.'</span>》组词1不能为空<br/>');
    			continue;//如果当前结果为空，跳出本次循环
    		}
    		if ($zaoju1 == ""){
    			array_push($errorinfo, '造句1不能为空' . '|' . $row); //记录错误信息
				$this->flushText('《<span style="color:red;">'.$zi.'</span>》造句1不能为空<br/>');
    			continue;//如果当前结果为空，跳出本次循环
    		}

    		/**
    		 * 如果以上没有空值的话继续执行
    		 */
			$this->flushText('<br/>《'.$zi.'》开始处理<br/>');



			$data['zi']=$zi;
			$data['pianpang']=$bushou;
			$data['bihuashu']=$bihuashu;
			$data['jiegou']=$jiegou;
			$data['bishun']=$bishun;
			$data['shizi']=$shizi;
			$data['xiezi']=$xiezizhidao;
						
			$data['bushouyin']='bushou.mp3';
			$data['bihuashuyin']='uploads/bihuashuyin/'.$bihuashu.'.mp3';
			$data['jiegouyin']= 'uploads/jiegou/'.$jiegouArr[$jiegou];

			
			$ziyinArr['wupinyin'] = $wupinyin;
			$ziyinArr['shengdiao'] = $shengdiao;
			$ziyinArr['pinyin'] = $pinyin;
			$ziyinArr['zuci1'] = $zuci1;
			$ziyinArr['zuci2'] = $zuci2;
			$ziyinArr['zuci3'] = $zuci3;
			$ziyinArr['ziyi1'] = $ziyi1;
			$ziyinArr['ziyi2'] = $ziyi2;
			$ziyinArr['ziyi3'] = $ziyi3;
			$ziyinArr['zaoju1'] = $zaoju1;
			$ziyinArr['zaoju2'] = $zaoju2;
			$ziyinArr['zaoju3'] = $zaoju3;
			$ziyinArr['wav'] = 'uploads/zipinyin/'.$wupinyin.$shengdiao.'.mp3';//字的发音



			$resultZi = $mzi->where('zi="%s"',$zi)->find();

			$zid = 0;

			if(!empty($resultZi)){
				//已存在,更新
				$mzi->where('zi="%s"',$zi)->save($data);
				$zid = $resultZi['id'];
			}else{
				//新增
				$zid = $mzi->add($data);
			}


			//以下音要判断文件是否存在
			$zigbk=iconv('utf-8', 'gbk', $zi);


			if(!empty($xiezizhidaoyin)&&file_exists($basepath.'/'.$zigbk.'/'.$xiezizhidaoyin)){
				$xiezizhidaoyinmp3 = md5(uniqid(microtime(true),true)).'.mp3';
				$dir->copyFile($basepath.'/'.$zigbk.'/'.$xiezizhidaoyin, 'uploads/ziaudio/'.$zid.'/'.$xiezizhidaoyinmp3,true);				
				$ziArr['xieziyin'] = 'uploads/ziaudio/'.$zid.'/'.$xiezizhidaoyinmp3;
			}

			if(!empty($shiziyin)&&file_exists($basepath.'/'.$zigbk.'/'.$shiziyin)){
				$shiziyinmp3 = md5(uniqid(microtime(true),true)).'.mp3';
				$dir->copyFile($basepath.'/'.$zigbk.'/'.$shiziyin, 'uploads/ziaudio/'.$zid.'/'.$shiziyinmp3,true);		
				$ziArr['shiziyin'] = 'uploads/ziaudio/'.$zid.'/'.$shiziyinmp3;
			}

			if(!empty($shizitu)&&file_exists($basepath.'/'.$zigbk.'/'.$shizitu)){
				$ext = pathinfo($shizitu);
				$shizitupic = md5(uniqid(microtime(true),true)).'.'.$ext['extension'];
				$dir->copyFile($basepath.'/'.$zigbk.'/'.$shizitupic, 'uploads/ziaudio/'.$zid.'/'.$shizitupic,true);		
				$ziArr['shizitu'] = 'uploads/ziaudio/'.$zid.'/'.$shizitupic;
			}
			if(!empty($shizishipin)&&file_exists($basepath.'/'.$zigbk.'/'.$shizishipin)){
				$ext = pathinfo($shizishipin);
				$shizishipinvideo = md5(uniqid(microtime(true),true)).'.'.$ext['extension'];
				$dir->copyFile($basepath.'/'.$zigbk.'/'.$shizishipin, 'uploads/ziaudio/'.$zid.'/'.$shizishipinvideo,true);
				$ziArr['shizivideo'] = 'uploads/ziaudio/'.$zid.'/'.$shizishipinvideo;
			}

			$mzi->where('zi="%s"',$zi)->save($ziArr);



			if(!empty($ziyiyin1)&&file_exists($basepath.'/'.$zigbk.'/'.$ziyiyin1)){
				$ziyiyin1mp3 = md5(uniqid(microtime(true),true)).'.mp3';
				$dir->copyFile($basepath.'/'.$zigbk.'/'.$ziyiyin1, 'uploads/ziaudio/'.$zid.'/'.$ziyiyin1mp3,true);				
				$ziyinArr['ziyiyin1'] = $ziyiyin1mp3;
			}

			if(!empty($ziyiyin2)&&file_exists($basepath.'/'.$zigbk.'/'.$ziyiyin2)){
				$ziyiyin2mp3 = md5(uniqid(microtime(true),true)).'.mp3';
				$dir->copyFile($basepath.'/'.$zigbk.'/'.$ziyiyin2, 'uploads/ziaudio/'.$zid.'/'.$ziyiyin2mp3,true);				
				$ziyinArr['ziyiyin2'] = $ziyiyin2mp3;
			}
			if(!empty($ziyiyin3)&&file_exists($basepath.'/'.$zigbk.'/'.$ziyiyin3)){
				$ziyiyin3mp3 = md5(uniqid(microtime(true),true)).'.mp3';
				$dir->copyFile($basepath.'/'.$zigbk.'/'.$ziyiyin3, 'uploads/ziaudio/'.$zid.'/'.$ziyiyin3mp3,true);				
				$ziyinArr['ziyiyin3'] = $ziyiyin3mp3;
			}
			if(!empty($zuciyin1)&&file_exists($basepath.'/'.$zigbk.'/'.$zuciyin1)){
				$zuciyin1mp3 = md5(uniqid(microtime(true),true)).'.mp3';
				$dir->copyFile($basepath.'/'.$zigbk.'/'.$zuciyin1, 'uploads/ziaudio/'.$zid.'/'.$zuciyin1mp3,true);				
				$ziyinArr['zuciyin1'] = $ziyiyin1mp3;
			}
			if(!empty($zuciyin2)&&file_exists($basepath.'/'.$zigbk.'/'.$zuciyin2)){
				$zuciyin2mp3 = md5(uniqid(microtime(true),true)).'.mp3';
				$dir->copyFile($basepath.'/'.$zigbk.'/'.$zuciyin2, 'uploads/ziaudio/'.$zid.'/'.$zuciyin2mp3,true);				
				$ziyinArr['zuciyin2'] = $zuciyin2mp3;
			}
			if(!empty($zuciyin3)&&file_exists($basepath.'/'.$zigbk.'/'.$zuciyin3)){
				$zuciyin3mp3 = md5(uniqid(microtime(true),true)).'.mp3';
				$dir->copyFile($basepath.'/'.$zigbk.'/'.$zuciyin3, 'uploads/ziaudio/'.$zid.'/'.$zuciyin3mp3,true);				
				$ziyinArr['zuciyin3'] = $zuciyin3mp3;
			}
			if(!empty($zaojuyin1)&&file_exists($basepath.'/'.$zigbk.'/'.$zaojuyin1)){
				$zaojuyin1mp3 = md5(uniqid(microtime(true),true)).'.mp3';
				$dir->copyFile($basepath.'/'.$zigbk.'/'.$zaojuyin1, 'uploads/ziaudio/'.$zid.'/'.$zaojuyin1mp3,true);				
				$ziyinArr['zaojuyin1'] = $zaojuyin1mp3;
			}
			if(!empty($zaojuyin2)&&file_exists($basepath.'/'.$zigbk.'/'.$zaojuyin2)){
				$zaojuyin2mp3 = md5(uniqid(microtime(true),true)).'.mp3';
				$dir->copyFile($basepath.'/'.$zigbk.'/'.$zaojuyin2, 'uploads/ziaudio/'.$zid.'/'.$zaojuyin2mp3,true);				
				$ziyinArr['zaojuyin2'] = $zaojuyin2mp3;
			}
			if(!empty($zaojuyin3)&&file_exists($basepath.'/'.$zigbk.'/'.$zaojuyin3)){
				$zaojuyin3mp3 = md5(uniqid(microtime(true),true)).'.mp3';
				$dir->copyFile($basepath.'/'.$zigbk.'/'.$zaojuyin3, 'uploads/ziaudio/'.$zid.'/'.$zaojuyin3mp3,true);				
				$ziyinArr['zaojuyin3'] = $zaojuyin3mp3;
			}


			$resultziyin = $mziyin->where("zid='%d' and pinyin='%s'",$zid,$pinyin)->find();
			if(!empty($resultziyin)){
				//已存在,更新
				$mziyin->where("zid='%d' and pinyin='%s'",$zid,$pinyin)->save($ziyinArr);
			}else{
				//新增
				$mziyin->where("zid='%d' and pinyin='%s'",$zid,$pinyin)->add($ziyinArr);
			}
    		$success_num = $success_num + 1;
    	}

		echo '导入成功数量:'.$success_num.'<br/>';
    }

	public function flushText($msg){
		echo $msg;
		ob_flush();
		flush();	
	}


	public function downloadtemplate(){
		$this->download ('Template/1.xlsx', ' 1.xlsx');
	}

	public function updatejiegou(){
		$zi = M('zi');

		$yzi = M('lib_zi');
		$data = $zi->field('id,zi')->select();
		//var_dump($data);
		foreach($data as $v){
			$re = $yzi->where('zi="%s"',$v['zi'])->find();
			//var_dump(empty($re));exit;
			if(!empty($re)){
				$up['jiegou'] = $re['jiegou'];
				$zi->where('id="%d"',$v['id'])->save($up);
			}
		}
		
	}



}