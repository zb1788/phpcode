<?php
namespace Home\Controller;
use Think\Controller;

/**
* 中心库数据导入控制器 去掉高中版本 2016.02.14
*
* @author         gm
* @since          1.0  2016-01-18
*/
class RmsController extends Controller {
	public function import(){
		header('Content-Type:text/html;charset=utf-8');
		set_time_limit(0);
		ini_set('memory_limit', '1024M');
		$beg_time  = time();
		echo '开始执行' . date('Y-m-d H:i:s',$beg_time) . '<br\>';

		$rms_dictionary_detail = M('Dictionary_detail','rms_');

		//清空字典数据
		$rms_dictionary = M("rms_dictionary_temp");
		$rms_dictionary->where('1=1')->delete();

		//导入年级学期
		$rs = $rms_dictionary_detail->where('dictionary_code IN ("grade","volume")')->select();
	 	foreach ($rs as $key => $value) {
			$value["update_time"] = strtotime("now");
	 		$rms_dictionary->add($value);
	 	}

	 	//导入美术学科
		$rs = $rms_dictionary_detail->where('dictionary_code = "subject" and  DETAIL_ORDER = "0007"')->select();
	 	foreach ($rs as $key => $value) {
			$value["update_time"] = strtotime("now");
	 		$rms_dictionary->add($value);
	 	}

	 	//导入初小内容
		$share_knowledge_structure = M('knowledge_structure','share_');
		$where = ' c2 = 1 AND R_SUBJECT = "0007" AND R_GRADE<>"0007" AND R_GRADE<>"0008" AND R_GRADE<>"0009" and ks_type=0';
		//$where .= ' AND KS_ID not like "00010d%"';
		$where .= ' AND R_GRADE IN (SELECT detail_code  FROM art_rms_dictionary_temp WHERE dictionary_code = "grade")';
		$where .= ' AND R_VOLUME IN (SELECT detail_code  FROM art_rms_dictionary_temp WHERE dictionary_code = "volume")';
		$where .= ' AND R_VERSION IN (SELECT detail_code FROM rms_dictionary_detail WHERE dictionary_code = "edition")';

		//清空本地目录临时表
		$rms_unit_temp = M("rms_unit_temp");
		$rms_unit_temp->where('1=1')->delete();

		//查询美术用到的所有目录,插入本地目录临时表
		$rs = $share_knowledge_structure->where($where)->select();
	 	foreach ($rs as $key => $value) {
			$value["update_time"] = strtotime("now");
	 		$rms_unit_temp->add($value);
	 	}

 	// 	//导入高中内容
		// $where = ' c2 = 1 AND R_SUBJECT = "0003" AND R_GRADE = "0010"';
		// $where .= ' AND r_version IN (SELECT detail_code FROM rms_dictionary_detail WHERE dictionary_code = "edition")';
		// $rs = $share_knowledge_structure->where($where)->select();
	 // 	foreach ($rs as $key => $value) {
		// 	$value["update_time"] = strtotime("now");
	 // 		$art_rms_unit->add($value);
	 // 	}

	 	//导入版本信息到本地临时目录表
		$rs = $rms_dictionary_detail->where('dictionary_code="edition" AND detail_code IN (SELECT r_version FROM art_rms_unit_temp where flag= 1)')->select();
	 	foreach ($rs as $key => $value) {
			$value["update_time"] = strtotime("now");
	 		$rms_dictionary->add($value);
	 	}

	 // 	//导入必修/选修信息
		// $rs = $rms_dictionary_detail->where('dictionary_code="electiveType" AND detail_code IN (SELECT r_xiu FROM art_rms_unit_temp where flag=1)')->select();
	 // 	foreach ($rs as $key => $value) {
		// 	$value["update_time"] = strtotime("now");
	 // 		$rms_dictionary->add($value);
	 // 	}


	 	//插入中心库新增数据到本地目录正式表
		$sql = 'INSERT INTO art_rms_unit (';
		$sql .= ' ks_id,p_id,business_type,ks_code,ks_name,ks_name_short,ks_level,display_order,flag,c1,c2,c3,c4,c5,c6,display_able,is_unit,ks_type,';
		$sql .= ' r_grade,r_volume,r_subject,r_version,area_id,usestate,section,recommend,ks_url,fei_type,fei_number,r_xiu,k_point,update_time) ';
		$sql .= ' SELECT ks_id,p_id,business_type,ks_code,ks_name,LEFT(ks_name,INSTR(ks_name," ")),ks_level,display_order,flag,c1,c2,c3,c4,c5,c6,display_able,is_unit,ks_type,';
		$sql .= ' r_grade,r_volume,r_subject,r_version,area_id,usestate,section,recommend,ks_url,fei_type,fei_number,r_xiu,k_point,update_time ';
		$sql .= ' FROM  art_rms_unit_temp WHERE ks_code NOT IN (SELECT ks_code FROM art_rms_unit) ';

 		$model = M();
        $model -> execute($sql);

        //单元原有数据修改
		$rms_unit_temp = M("rms_unit_temp");
		$rms_unit = M("rms_unit");
        $rs = $rms_unit_temp->select();
	 	foreach ($rs as $key => $value) {
	 		unset($data);
			$data["ks_id"] = $value["ks_id"];
			$data["p_id"] = $value["p_id"];
			$data["business_type"] = $value["business_type"];
			$data["ks_code"] = $value["ks_code"];
			$data["ks_name"] = trim($value["ks_name"]);
			$data["ks_level"] = $value["ks_level"];
			$data["display_order"] = $value["display_order"];
			$data["flag"] = $value["flag"];
			$data["c1"] = $value["c1"];
			$data["c2"] = $value["c2"];
			$data["c3"] = $value["c3"];
			$data["c4"] = $value["c4"];
			$data["c5"] = $value["c5"];
			$data["c6"] = $value["c6"];
			$data["display_able"] = $value["display_able"];
			$data["is_unit"] = $value["is_unit"];
			$data["ks_type"] = $value["ks_type"];
			$data["r_grade"] = $value["r_grade"];
			$data["r_volume"] = $value["r_volume"];
			$data["r_subject"] = $value["r_subject"];
			$data["r_version"] = $value["r_version"];
			$data["area_id"] = $value["area_id"];
			$data["usestate"] = $value["usestate"];
			$data["section"] = $value["section"];
			$data["recommend"] = $value["recommend"];
			$data["ks_url"] = $value["ks_url"];
			$data["fei_type"] = $value["fei_type"];
			$data["fei_number"] = $value["fei_number"];
			$data["r_xiu"] = $value["r_xiu"];
			$data["k_point"] = $value["k_point"];
			$data["update_time"] = $value["update_time"];
	 		$rms_unit->save($data);
	 	}

	 	//短名字
	 	$sql ='UPDATE art_rms_unit SET ks_name_short = ks_name WHERE ks_name_short = ""';
        $model -> execute($sql);

		$sql ='UPDATE art_rms_unit SET ks_name_short = LEFT(ks_name_short,INSTR(ks_name_short,"："))';
        $model -> execute($sql);
        //短名字end

        //把中心库已经删除的目录flag设置为0
        $sql ='UPDATE art_rms_unit SET flag=0 WHERE ks_code NOT IN (SELECT ks_code FROM art_rms_unit_temp)';
        $model -> execute($sql);

		//字典新增数据插入
		$sql = 'INSERT INTO art_rms_dictionary(id,dictionary_code,detail_code,detail_name,detail_name_short,detail_order,detail_remark,pic_path,update_time)';
		$sql .= ' SELECT id,dictionary_code,detail_code,detail_name,detail_name,detail_order,detail_remark,pic_path,update_time FROM art_rms_dictionary_temp ';
		$sql .= ' WHERE id NOT IN (SELECT id FROM art_rms_dictionary)';
		$model -> execute($sql);

        //字典原有数据修改
		$rms_dictionary_temp = M("rms_dictionary_temp");
		$rms_dictionary = M("rms_dictionary");

        $rs = $rms_dictionary_temp->select();
	 	foreach ($rs as $key => $value) {
	 		unset($data);
			$data["id"] = $value["id"];
			$data["dictionary_code"] = $value["dictionary_code"];
			$data["detail_code"] = $value["detail_code"];
			$data["detail_name"] = $value["detail_name"];
			$data["detail_order"] = trim($value["detail_order"]);
			$data["detail_remark"] = $value["detail_remark"];
			$data["update_time"] = $value["update_time"];
	 		$rms_dictionary->save($data);
	 	}

	 	$sql = 'DELETE FROM art_rms_dictionary WHERE id NOT IN (SELECT id FROM art_rms_dictionary_temp)';
		$model -> execute($sql);

		$end_time  = time();
		echo '执行完成' . date('Y-m-d H:i:s',$end_time ) . '------共计' . ($end_time - $beg_time) .'秒';
	}



	/**
	 * 与中心库目录核对
	 * @return [type] [description]
	 */
	public function checkUnitToRms(){
		$m = M();
		/*
		//生字同步
		$word_kecheng = M('word_kecheng');
		$sql = 'SELECT l.id,l.nianji,l.xueqi,l.kecheng,(SELECT t.banben FROM yw_ziversion t WHERE t.id=l.versionid ) as banben FROM art_word_kecheng l;';
		*/

		//课文同步
		$word_kecheng = M('text_kecheng');
		$sql = 'SELECT l.id,l.nianji,l.xueqi,l.kecheng, l.banben FROM art_text_kecheng l;';

		$rs_word_kecheng = $m->query($sql);
		foreach($rs_word_kecheng as $v){
			$nianji = $v['nianji'];
			$xueqi = $v['xueqi'];
			$banben = trim($v['banben']);
			$kecheng = trim($v['kecheng']);
			$kecheng = str_replace(array(' ','、'),'',$kecheng);
			$pattern = '/^\d+/';
			$kecheng = preg_replace($pattern,'',$kecheng);
			$xueke = '语文';
			$id = $v['id'];
			$localC1 = '班班通.'.$nianji.'.'.$xueqi.'.'.$xueke.'.'.$banben.'.';
			//echo $localC1;exit;
			// $rms_unit = M('rms_unit');
			// $rs_rms_unit = $rms_unit->where('c1="%s"',$localC1)->find();
			$rms_unit = M();
			$sql = 'select ks_code from art_rms_unit where c1 like "'.$localC1.'%" and ks_name like "%'.$kecheng.'%"';
			$rs_rms_unit = $rms_unit->query($sql);
			if(!empty($rs_rms_unit)){
				$ks_code = $rs_rms_unit[0]['ks_code'];
				$word_kecheng->where('id="%d"',$id)->setField('ks_code',$ks_code);
			}

		}
	}














}