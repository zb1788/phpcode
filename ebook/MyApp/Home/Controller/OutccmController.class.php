<?php
/**
 * 用户登录首页
 * @author Zhangbo1
 *
 */
namespace Home\Controller;
use Think\Controller;
class OutccmController extends Controller {
    public function index(){
        $this->display();
    }
    public function getTreeNodes(){
        $id=I('id/s','');
        $m=M('','','DB_CONFIG1');
        if ($id==''){
            $max = 6;//默认6级
            $sql = "SELECT KS_ID,P_ID,KS_NAME,IF(ks_level<>".$max.",'true','false') as isParent,KS_LEVEL,C1,DISPLAY_ORDER FROM SHARE_KNOWLEDGE_STRUCTURE WHERE IS_UNIT='0' and flag<>'0' AND KS_LEVEL=2 ORDER BY DISPLAY_ORDER";
            //$sql="SELECT KS_ID,P_ID,KS_NAME,IF(ks_level<>6,'true','false') as isParent,C1,DISPLAY_ORDER  FROM t_share_knowledge_structure WHERE is_unit=0 and flag>0 and ks_type=0 AND KS_LEVEL=2 AND SUBSTRING(KS_ID,1,6) in('000102','000103','000104','000105','000106','000107','000108','000109','00010a','00010b')";
        }else {
            $sql_l = "SELECT max(KS_LEVEL) as total FROM SHARE_KNOWLEDGE_STRUCTURE WHERE FLAG=1 AND KS_CODE like '".$id."%';";
            $re = $m->query($sql_l);

            //var_dump($re);exit;
            $max = $re[0]['total'];//最大level
            //$sql = "SELECT KS_ID,P_ID,KS_NAME,IF(ks_level<>".$max.",'true','false') as isParent,KS_LEVEL,C1,DISPLAY_ORDER FROM SHARE_KNOWLEDGE_STRUCTURE WHERE p_id='".$id."' AND is_unit=0 and flag<>0 ORDER BY DISPLAY_ORDER";
            $sql = "SELECT KS_ID,P_ID,KS_NAME,IF(c2<>1,'true','false') as isParent,KS_LEVEL,C1,DISPLAY_ORDER FROM SHARE_KNOWLEDGE_STRUCTURE WHERE p_id='".$id."' AND is_unit=0 and flag<>0 ORDER BY DISPLAY_ORDER";

        }
        $data_mulu = $m->query($sql);

        $json="[";
        foreach ($data_mulu as $v){
            $json.='{id:"'.$v['ks_id'].'", pId:"'.$v['p_id'].'",isParent:"'.$v['isparent'].'", name:"'.$v['ks_name'].'",file:"'.$v['ks_level'].'", maxLevel:"'.$max.'"},';
        }
        $json = rtrim($json,',');
        $json = $json.']';
        echo $json;
    }

    //获取当前版本下的所有章节信息
    public function getLists(){
        $id=I('chapterid/s','');

        // $m=M('','','DB_CONFIG1');
        // $sql = "SELECT KS_ID,P_ID,KS_NAME,KS_LEVEL FROM SHARE_KNOWLEDGE_STRUCTURE WHERE is_unit = 0 AND flag > 0 AND P_ID = '".$id."' ORDER BY DISPLAY_ORDER;";
        // //echo $sql;
        // $data = $m->query($sql);
        // //var_dump($data);exit;
        // $this->ajaxReturn($data);
        //

        $m=M('','','DB_CONFIG1');
        $sql = "SELECT KS_ID,P_ID,KS_NAME,KS_LEVEL FROM SHARE_KNOWLEDGE_STRUCTURE WHERE is_unit = 0 AND flag > 0 AND P_ID = '".$id."' ORDER BY DISPLAY_ORDER;";
        //echo $sql;
        $data = $m->query($sql);
        $sql_2 = "SELECT KS_LEVEL as total FROM SHARE_KNOWLEDGE_STRUCTURE WHERE FLAG=1 AND KS_CODE like '".$id."%' and ks_level=".($data[0]['ks_level']+1).";";
        $re2 = $m->query($sql_2);
        if(empty($re2)){
            //说明到底层了
            $this->ajaxReturn($data);
        }else{
            $arr = array();
            $this->ajaxReturn($arr);
        }

    }

    //导出选择章节的策划单
    public function downloadRes(){
        vendor('fileDirUtil');
        $dir = new \fileDirUtil();

        $str=I('str/s');
        $chapterArr=explode('|', $str);//章节id数组

        $filename = $this->ExcelForCCM($chapterArr);

        echo $filename;
    }

    public function xiazai(){
        $filename = I('filename/s','');
        $this->download ($filename);
        //$dir->unlinkFile($filename);
    }



	/**
     * 生成excel（入库单）
     */
    public function ExcelForCCM($chapterArr){
        vendor('PHPExcel');
        $todayDir='download/ccmlist/';
		if(!is_dir($todayDir)){
			mkdir($todayDir,0777);
		}
        $Model=M('','','DB_CONFIG1');
        $sql_where='';
        if (count($chapterArr)==1){
            //导出本章节
            $chapterid=$chapterArr[0];
            $sql_where.=" t.KS_ID = '".$chapterid."' ";
        }else {
            //导出多个章节
            for ($i=0;$i<count($chapterArr);$i++){
                $sql_where.=" t.KS_ID = '".$chapterArr[$i]."' OR";
            }
            $sql_where = " ( ".trim($sql_where,'OR')." ) ";
        }


        $sql ="SELECT IF(t.C1 IS NULL,'',IF (t.KS_CODE LIKE '00010c%',REPLACE (SUBSTRING_INDEX(t.C1, '.', 6),'.','/'),REPLACE (SUBSTRING_INDEX(t.C1, '.', 5),'.','/'))) AS '上层目录',IF(t.KS_NAME IS NULL,'',t.KS_NAME) as '课程（节）',IF(l.WEEK_SUBJECT IS NULL,'',l.WEEK_SUBJECT) as '主题ID',IF(l.R_EXT5 IS NULL,'',l.R_EXT5) as '序号',IF(l.R_TITLE IS NULL,'',l.R_TITLE) as '标题', '' as'制作要求','' as '处理类型',(select max(p2.rt_name) from RMS_RES_PARAM p2 where l.R_TYPECODE = p2.RT_CODE) as '资源类型',IF(m.RF_NAME IS NULL,'',m.RF_NAME) as '资源格式',IF(s.DETAIL_NAME IS NULL,'',s.DETAIL_NAME) as '课堂应用',IF(l.c3='1','是','否') as '导学案',IF (l.R_SFLAG = '1','是','否') as '拓展提升',(SELECT c.NAME FROM COMMON_TYPE c WHERE  l.COURSE_TYPE = c.ID) AS '课件',IF (l.R_AUTHOR = '','',l.R_AUTHOR) AS '名师',(SELECT c.NAME FROM COMMON_TYPE c WHERE  l.LESSION_TYPE = c.ID) AS '教学设计',IF (l.LINK_TYPE = '0','',IF(l.LINK_TYPE = '1','静态链接',IF(l.LINK_TYPE = '2','仿真实验',IF(l.LINK_TYPE = '3','英语同步练',IF(l.LINK_TYPE = '4','中心库资源',''))))) AS '链接类型',IF (l.LINK_VALUE IS NULL,'',IF(l.LINK_VALUE = 'experiment/diy/1','初中物理实验DIY',IF ( l.LINK_VALUE = 'experiment/diy/2','高中物理实验DIY',IF (l.LINK_VALUE LIKE '%run%',(SELECT NAME FROM win_fzsy WHERE id = SUBSTRING_INDEX(l.LINK_VALUE, '/' ,- 1)),l.LINK_VALUE)))) AS '链接地址',IF(l.R_DESC IS NULL,'',trim(REPLACE(l.R_DESC,'\r\n','<br>'))) as '描述',IF(l.R_PUBLISHER IS NULL,'',l.R_PUBLISHER) as '资源供应商',IF(l.R_EXT3 IS NULL,'',l.R_EXT3) as '上传者','' as '海报',l.R_CODE as '资源编码',IF(l.R_USED_OBJ='OBJ001','教师备课','教师备课授课') as '使用对象',IF(l.R_EXT6='ZYNEW     ','NEW',IF(l.R_EXT6='ZYHOT','HOT',IF(l.R_EXT6='ZYTJ','推荐',''))) as '资源推介',IF(l.R_KEYWORDS IS NULL,'',trim(REPLACE(l.R_KEYWORDS,'\r\n','<br>'))) as '关键字','' as '备注'  FROM ((RMS_RESOURCEINFO l INNER JOIN SHARE_KNOWLEDGE_STRUCTURE t ON l.R_KS_ID=t.KS_CODE AND  ".$sql_where." AND l.R_STATE='1' AND t.FLAG='1' AND (l.R_HASHCODE='' OR l.R_HASHCODE is NULL) ) INNER JOIN RMS_RESOURCEFORMAT m ON l.R_FORMAT=m.RF_CODE)  LEFT JOIN RMS_DICTIONARY_DETAIL s ON l.c1=s.DETAIL_CODE order by l.R_KS_ID,l.R_EXT5;";

        $data=$Model->query($sql);
        $keyArray = array_keys($data[0]);
        //var_dump($data);exit;
        // echo $keyArray[0].'/'.$keyArray[1];

        $arr=array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
                'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');

        $objExcel = new \PHPExcel();
        $objWriter = new \PHPExcel_Writer_Excel2007($objExcel);

        $objExcel->setActiveSheetIndex(0);
        $objActSheet = $objExcel->getActiveSheet();
        $objActSheet->setTitle('资源策划单');

        //循环输出第一列的列名称
        for($i=0; $i<count($keyArray); $i++){
            $objActSheet->setCellValue($arr[$i].'1',$keyArray[$i]);
        }
        $objActSheet->setCellValue('C1','主题ID');

        //手动设置前4列宽度
        $objActSheet->getColumnDimension('A')->setWidth(35);
        $objActSheet->getColumnDimension('B')->setWidth(25);


        foreach ($data as $key=>$v){
            for($i=0; $i<count($keyArray); $i++){
               /// echo $arr[$i].($key+2).'||'.$v[$keyArray[$i]].'<br>';continue;
                if($arr[$i]=='V'||$arr[$i]=='C'||$arr[$i]=='Q'){
                    //资源编码列，不让为科学计数法
                    //echo $arr[$i].($key+2).'||'.$v[$keyArray[$i]];exit;
                    $objActSheet->setCellValueExplicit($arr[$i].($key+2), $v[$keyArray[$i]],\PHPExcel_Cell_DataType::TYPE_STRING);
                }else{
                    $objActSheet->setCellValue($arr[$i].($key+2),$v[$keyArray[$i]]);
                }
            }

        }

        $filename = $todayDir.date('YmdHis').rand(1000,9999).".xlsx";


        $objWriter->save($filename);  //保存到服务器

        return $filename;
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
        //exit();
    }




   public function testjson(){
         header('Content-Type:application/json;charset=utf-8');
        $json = '{"typeList":[{"typeCode":"RT001","typeName":"教案设计","items":[{"jsdata":"{\"groupType\":\"gxbk\",\"rcode\":\"20150710134007325767767227758\",\"rsType\":\"1\"}","icoTitle":"单元检测","rtitle":"Unit5 SectionA（Grammar Focus-4c）精品课件","rcode":"20150710134007325767767227758","iconType":"F200.png"},{"jsdata":"{\"groupType\":\"gxbk\",\"rcode\":\"20150710134007325767767227759\",\"rsType\":\"1\"}","icoTitle":"词汇","rtitle":"【聚焦中考】：Unit5 必考知识点汇编","rcode":"20150710134007325767767227759","iconType":"F100.png"},{"jsdata":"{\"groupType\":\"gxbk\",\"rcode\":\"20150710134007325767767227760\",\"rsType\":\"1\"}","rtitle":"Unit5 SectionB（3a-Self Check）精品课件","rcode":"20150710134007325767767227760","iconType":"F300.png"},{"jsdata":"{\"groupType\":\"gxbk\",\"rcode\":\"20150710134007325767767227761\",\"rsType\":\"1\"}","rtitle":"Unit5 SectionA 核心词汇及短语精讲","rcode":"20150710134007325767767227761","iconType":"F400.png"},{"jsdata":"{\"groupType\":\"gxbk\",\"rcode\":\"20150710134007325767767227762\",\"rsType\":\"1\"}","icoTitle":"真题演练","rtitle":"Unit5 SectionB 核心词汇及短语精讲","rcode":"20150710134007325767767227762","iconType":"F500.png"},{"jsdata":"{\"groupType\":\"gxbk\",\"rcode\":\"20150710134007325767767227763\",\"rsType\":\"1\"}","rtitle":"【易混易错全解】：与“made”有关的词组辨析","rcode":"20150710134007325767767227763","iconType":"F600.png"},{"jsdata":"{\"groupType\":\"gxbk\",\"rcode\":\"20150710134007325767767227764\",\"rsType\":\"1\"}","rtitle":"【聚焦中考】：Unit5 必考知识点汇编1","rcode":"20150710134007325767767227764","iconType":"F700.png"},{"jsdata":"{\"groupType\":\"gxbk\",\"rcode\":\"20150710134007325767767227765\",\"rsType\":\"1\"}","rtitle":"【聚焦中考】：Unit5 必考知识点汇编2","rcode":"20150710134007325767767227765","iconType":"F800.png"},{"jsdata":"{\"groupType\":\"gxbk\",\"rcode\":\"20150710134007325767767227766\",\"rsType\":\"1\"}","icoTitle":"中考链接","rtitle":"【聚焦中考】：Unit5 必考知识点汇编3","rcode":"20150710134007325767767227766","iconType":"ppt.png"},{"jsdata":"{\"groupType\":\"gxbk\",\"rcode\":\"20150710134007325767767227767\",\"rsType\":\"1\"}","rtitle":"【聚焦中考】：Unit5 必考知识点汇编4","rcode":"20150710134007325767767227767","iconType":"pdf.png"},{"jsdata":"{\"groupType\":\"gxbk\",\"rcode\":\"20150710134007325767767227768\",\"rsType\":\"1\"}","rtitle":"【聚焦中考】：Unit5 必考知识点汇编5","rcode":"20150710134007325767767227768","iconType":"xls.png"},{"jsdata":"{\"groupType\":\"gxbk\",\"rcode\":\"20150710134007325767767227769\",\"rsType\":\"1\"}","rtitle":"【聚焦中考】：Unit5 必考知识点汇编6","rcode":"20150710134007325767767227769","iconType":"jpg.png"}]},{"typeCode":"RT002","typeName":"教学课件"},{"typeCode":"RT003","typeName":"教案类型","items":[{"jsdata":"{\"groupType\":\"gxbk\",\"rcode\":\"20150710134007325767767227770\",\"rsType\":\"1\"}","icoTitle":"单元检测","rtitle":"Unit5 SectionA（Grammar Focus-4c）精品课件测试1","rcode":"20150710134007325767767227770","iconType":"F200.png"},{"jsdata":"{\"groupType\":\"gxbk\",\"rcode\":\"20150710134007325767767227771\",\"rsType\":\"1\"}","icoTitle":"词汇","rtitle":"【聚焦中考】：Unit5 必考知识点汇编测试2","rcode":"20150710134007325767767227771","iconType":"F100.png"},{"jsdata":"{\"groupType\":\"gxbk\",\"rcode\":\"20150710134007325767767227772\",\"rsType\":\"1\"}","rtitle":"Unit5 SectionB（3a-Self Check）精品课件测试3","rcode":"20150710134007325767767227772","iconType":"ppt.png"}]},{"typeCode":"RT004","typeName":"测试分类","items":[{"jsdata":"{\"groupType\":\"gxbk\",\"rcode\":\"20150710134007325767767227773\",\"rsType\":\"1\"}","icoTitle":"单元检测","rtitle":"Unit5 SectionA（Grammar Focus-4c）精品课件测试1","rcode":"20150710134007325767767227773","iconType":"F200.png"},{"jsdata":"{\"groupType\":\"gxbk\",\"rcode\":\"20150710134007325767767227774\",\"rsType\":\"1\"}","icoTitle":"词汇","rtitle":"【聚焦中考】：Unit5 必考知识点汇编测试2","rcode":"20150710134007325767767227774","iconType":"F100.png"},{"jsdata":"{\"groupType\":\"gxbk\",\"rcode\":\"20150710134007325767767227775\",\"rsType\":\"1\"}","rtitle":"Unit5 SectionB（3a-Self Check）精品课件测试3","rcode":"20150710134007325767767227775","iconType":"ppt.png"}]}]}';

    exit($json);
    }



 public function testjson2(){
         header('Content-Type:application/json;charset=utf-8');
        $json = '{"typeList":[{"jsdata":"{\"groupType\":\"gxbk\",\"rcode\":\"20150710134007325767767227758\",\"rsType\":\"1\"}","icoTitle":"单元检测","rtitle":"Unit5 SectionA（Grammar Focus-4c）精品课件","rcode":"20150710134007325767767227758","iconType":"F200.png","ebookCode":"20190710134007325767767227758","page":"1","iconX":"213.1500000000001","iconY":"808.8"},{"jsdata":"{\"groupType\":\"gxbk\",\"rcode\":\"20150710134007325767767227759\",\"rsType\":\"1\"}","icoTitle":"词汇","rtitle":"【聚焦中考】：Unit5 必考知识点汇编","rcode":"20150710134007325767767227759","iconType":"F100.png","ebookCode":"20190710134007325767767227758","page":"1","iconX":"225.0999999999999","iconY":"899.5"},{"jsdata":"{\"groupType\":\"gxbk\",\"rcode\":\"20150710134007325767767227760\",\"rsType\":\"1\"}","rtitle":"Unit5 SectionB（3a-Self Check）精品课件","rcode":"20150710134007325767767227760","iconType":"F300.png","ebookCode":"20190710134007325767767227758","page":"2","iconX":"179.54999999999995","iconY":"1154.9"},{"jsdata":"{\"groupType\":\"gxbk\",\"rcode\":\"20150710134007325767767227761\",\"rsType\":\"1\"}","rtitle":"Unit5 SectionA 核心词汇及短语精讲","rcode":"20150710134007325767767227761","iconType":"F400.png","ebookCode":"20190710134007325767767227758","page":"2","iconX":"335.35","iconY":"579.15"},{"jsdata":"{\"groupType\":\"gxbk\",\"rcode\":\"20150710134007325767767227762\",\"rsType\":\"1\"}","icoTitle":"真题演练","rtitle":"Unit5 SectionB 核心词汇及短语精讲","rcode":"20150710134007325767767227762","iconType":"F500.png","ebookCode":"20190710134007325767767227758","page":"3","iconX":"535.8","iconY":"823.35"},{"jsdata":"{\"groupType\":\"gxbk\",\"rcode\":\"20150710134007325767767227763\",\"rsType\":\"1\"}","rtitle":"【易混易错全解】：与“made”有关的词组辨析","rcode":"20150710134007325767767227763","iconType":"F600.png","ebookCode":"20190710134007325767767227758","page":"3","iconX":"119.5","iconY":"796.25"},{"jsdata":"{\"groupType\":\"gxbk\",\"rcode\":\"20150710134007325767767227764\",\"rsType\":\"1\"}","rtitle":"【聚焦中考】：Unit5 必考知识点汇编1","rcode":"20150710134007325767767227764","iconType":"F700.png","ebookCode":"20190710134007325767767227758","page":"1","iconX":"525.45","iconY":"1010.5"}]}';

    exit($json);
    }

 public function testjson3(){
         header('Content-Type:application/json;charset=utf-8');
	$arr[0]['rcode'] = 'aaaaaa';
	$arr[0]['title'] = 'xxxxx';
	$arr[1]['rcode'] = 'aaaaaa';
	$arr[1]['title'] = 'xxxxx';
	$arr[2]['rcode'] = 'aaaaaa';
	$arr[2]['title'] = 'xxxxx';
	$info['info'] = $arr;
	$info['list'] = 'list';
	exit(json_encode($info));
 }

}