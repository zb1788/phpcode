<?php
/**
 * 同步语文动画mp3
 * @author Zhangbo1
 *
 */
namespace Home\Controller;
use Think\Controller;
class SyncController extends Controller {
    public function index(){
    	vendor('fileDirUtil');
    	$dir=new \fileDirUtil();
        $m=M('','','DB_CONFIG2')->table('yw_kecheng_info');
        $data=$m->where('flag=0 and filepath<>""  and banben="人教版"')->field('id,nianji,xueqi,banben,kecheng,filepath,filename')->select();
        foreach ($data as $v){
        	$id=$v['id'];
        	$grade=$v['nianji'];
        	$version=$v['banben'];//版本
        	$term=$v['xueqi'];//学期
        	$kecheng=$v['kecheng'];//章节名称
        	$musicname=$v['filename'];//mp3名字
        	$musicfile=$v['filepath'];//mp3路径
//         	if ($grade=='高中'){
//         		$version=$v['banben'];//版本
//         		$bixu=$arr[3];//必修选修
//         		$sql="SELECT ss.KS_CODE,ss.R_GRADE,ss.R_XIU,ss.R_SUBJECT,ss.R_VERSION FROM SHARE_KNOWLEDGE_STRUCTURE ss WHERE ss.KS_LEVEL=5 AND ss.R_GRADE=(SELECT DETAIL_CODE FROM RMS_DICTIONARY_DETAIL rd WHERE rd.DETAIL_NAME='".$grade."' AND rd.DICTIONARY_CODE='grade') AND ss.R_VERSION=(SELECT DETAIL_CODE FROM RMS_DICTIONARY_DETAIL rd WHERE rd.DETAIL_NAME='".$version."' AND rd.DICTIONARY_CODE='edition') AND ss.R_SUBJECT=(SELECT DETAIL_CODE FROM RMS_DICTIONARY_DETAIL rd WHERE rd.DETAIL_NAME='".$subject."' AND rd.DICTIONARY_CODE='subject') AND ss.R_XIU=(SELECT DETAIL_CODE FROM RMS_DICTIONARY_DETAIL rd WHERE rd.DETAIL_NAME='".$bixu."' AND rd.DICTIONARY_CODE='electiveType');
//     		";
//         	}else {
//         		$term=$v['xueqi'];//学期
//         		$version=$v['banben'];//版本
//         		$sql="SELECT ss.KS_CODE,ss.R_GRADE,ss.R_VOLUME,ss.R_SUBJECT,ss.R_VERSION FROM SHARE_KNOWLEDGE_STRUCTURE ss WHERE ss.KS_LEVEL=5 AND ss.R_GRADE=(SELECT DETAIL_CODE FROM RMS_DICTIONARY_DETAIL rd WHERE rd.DETAIL_NAME='".$grade."' AND rd.DICTIONARY_CODE='grade') AND ss.R_VERSION=(SELECT DETAIL_CODE FROM RMS_DICTIONARY_DETAIL rd WHERE rd.DETAIL_NAME='".$version."' AND rd.DICTIONARY_CODE='edition') AND ss.R_SUBJECT=(SELECT DETAIL_CODE FROM RMS_DICTIONARY_DETAIL rd WHERE rd.DETAIL_NAME='".$subject."' AND rd.DICTIONARY_CODE='subject') AND ss.R_VOLUME=(SELECT DETAIL_CODE FROM RMS_DICTIONARY_DETAIL rd WHERE rd.DETAIL_NAME='".$term."' AND rd.DICTIONARY_CODE='volume');
//     		";
//         	}
        	$sql="SELECT ss.KS_CODE,ss.P_ID FROM SHARE_KNOWLEDGE_STRUCTURE ss WHERE ss.KS_NAME='".$kecheng."' AND ss.R_GRADE=(SELECT DETAIL_CODE FROM RMS_DICTIONARY_DETAIL rd WHERE rd.DETAIL_NAME='".$grade."' AND rd.DICTIONARY_CODE='grade') AND ss.R_VERSION=(SELECT DETAIL_CODE FROM RMS_DICTIONARY_DETAIL rd WHERE rd.DETAIL_NAME='".$version."' AND rd.DICTIONARY_CODE='edition') AND ss.R_SUBJECT=(SELECT DETAIL_CODE FROM RMS_DICTIONARY_DETAIL rd WHERE rd.DETAIL_NAME='语文' AND rd.DICTIONARY_CODE='subject') AND ss.R_VOLUME=(SELECT DETAIL_CODE FROM RMS_DICTIONARY_DETAIL rd WHERE rd.DETAIL_NAME='".$term."' AND rd.DICTIONARY_CODE='volume')";
        	//echo $sql;exit();
        	$Model_ccm=M('','','DB_CONFIG1');
        	$data_ccm=$Model_ccm->query($sql);
        	//var_dump($data_ccm);
        	$m2=M('','','DB_CONFIG2')->table('yw_kecheng_info');
        	if (empty($data_ccm)){
        		$m2->flag='2';
        		$m2->where('id="%d"',$id)->save();
        	}else {
        		$m2->flag='1';
        		$m2->where('id="%d"',$id)->save();
        		$data_add['bookid']=$data_ccm[0]['p_id'];
        		$data_add['chapterid']=$data_ccm[0]['ks_code'];
        		$data_add['musicname']=$musicname;
        		$data_add['musicfile']=$musicfile;
        		$m_chapter=M('','','DB_CONFIG')->table('t_book_chapter_mp3');
        		$data_chapter=$m_chapter->where('bookid="%s" and chapterid="%s"',$data_ccm[0]['p_id'],$data_ccm[0]['ks_code'])->find();
        		$m_chapter2=M('','','DB_CONFIG')->table('t_book_chapter_mp3');
        		if (empty($data_chapter)){
        			$m_chapter2->add($data_add);
        			$dir->copyFile('D:/wwwroot/yuwen/uploads/'.$musicfile, './Uploads/audio/'.$musicfile);
        		}else {
        			continue;
        		}
        		

        		
        	}
        	
        }
        echo 'OK!';
    }
}