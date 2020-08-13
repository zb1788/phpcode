<?php
/**
 * 用户登录首页
 * @author Zhangbo1
 *
 */
namespace Home\Controller;
use Think\Controller;
class BookController extends Controller {
	//图片导入页面
    public function import(){
        $this->display();
    }
    //遍历ftp，导入图片
    public function importPics(){
    	vendor('fileDirUtil');
    	$dir = new \fileDirUtil();
    	$dirs=$dir->dirNodeTree('TmpUploads');//目录数组

    	$errorinfo=array();//错误信息数组
    	$success=array();//成功信息数组
    	for ($i=0;$i<count($dirs);$i++){
    		$jiaocai= $dirs[$i];
    		$jiaocaiToUtf8= iconv('GBK', 'utf-8', $jiaocai);//文件夹名称：一年级-上学期-语文-人教版
    		$arr=explode('-', $jiaocai);
    		$str=iconv('gbk', 'utf-8', $arr[0]);
    		if ($str=='高中'){
    			$grade=$arr[0];//年级
    			$subject=$arr[1];//科目
    			$version=$arr[2];//版本
    			if (count($arr)==4){
	    			$bixu=$arr[3];//必修选修
    			}else{
    				$bixu=$arr[3].'-'.$arr[4];
    			}
    			$sql="SELECT ss.KS_CODE,ss.R_GRADE,ss.R_XIU,ss.R_SUBJECT,ss.R_VERSION FROM SHARE_KNOWLEDGE_STRUCTURE ss WHERE ss.KS_LEVEL=5 AND ss.R_GRADE=(SELECT DETAIL_CODE FROM RMS_DICTIONARY_DETAIL rd WHERE rd.DETAIL_NAME='".$grade."' AND rd.DICTIONARY_CODE='grade') AND ss.R_VERSION=(SELECT DETAIL_CODE FROM RMS_DICTIONARY_DETAIL rd WHERE rd.DETAIL_NAME='".$version."' AND rd.DICTIONARY_CODE='edition') AND ss.R_SUBJECT=(SELECT DETAIL_CODE FROM RMS_DICTIONARY_DETAIL rd WHERE rd.DETAIL_NAME='".$subject."' AND rd.DICTIONARY_CODE='subject') AND ss.R_XIU=(SELECT DETAIL_CODE FROM RMS_DICTIONARY_DETAIL rd WHERE rd.DETAIL_NAME='".$bixu."' AND rd.DICTIONARY_CODE='electiveType');
    		";
    		}else {
    			$grade=$arr[0];//年级
    			$term=$arr[1];//学期
    			$subject=$arr[2];//科目
    			$version=$arr[3];//版本
    			$sql="SELECT ss.KS_CODE,ss.R_GRADE,ss.R_VOLUME,ss.R_SUBJECT,ss.R_VERSION FROM SHARE_KNOWLEDGE_STRUCTURE ss WHERE ss.KS_LEVEL=5 AND ss.R_GRADE=(SELECT DETAIL_CODE FROM RMS_DICTIONARY_DETAIL rd WHERE rd.DETAIL_NAME='".$grade."' AND rd.DICTIONARY_CODE='grade') AND ss.R_VERSION=(SELECT DETAIL_CODE FROM RMS_DICTIONARY_DETAIL rd WHERE rd.DETAIL_NAME='".$version."' AND rd.DICTIONARY_CODE='edition') AND ss.R_SUBJECT=(SELECT DETAIL_CODE FROM RMS_DICTIONARY_DETAIL rd WHERE rd.DETAIL_NAME='".$subject."' AND rd.DICTIONARY_CODE='subject') AND ss.R_VOLUME=(SELECT DETAIL_CODE FROM RMS_DICTIONARY_DETAIL rd WHERE rd.DETAIL_NAME='".$term."' AND rd.DICTIONARY_CODE='volume');
    		";
    		}
    		//echo $version.'|'.$grade.'|'.$term;
    		$Model_ccm=M('','','DB_CONFIG1');
    		$Model_ccm->execute('set names gbk');
    		//echo $sql;
    		$data=$Model_ccm->query($sql);

    		if (empty($data)){
    			array_push($errorinfo, $jiaocaiToUtf8.'中心库不存在<br/>');
    			continue;
    		}else {
    			if ($str=='高中'){
    				$ksid=$data[0]['ks_code'];//获取目录编码ks_code
    				$gradeCode=$data[0]['r_grade'];
    				$subjectCode=$data[0]['r_subject'];
    				$versionCode=$data[0]['r_version'];
    				$bixiuCode=$data[0]['r_xiu'];
    				$folder='Uploads/'.$gradeCode.'/'.$subjectCode.'/'.$versionCode.'/'.$bixiuCode;
    			}else {
    				$ksid=$data[0]['ks_code'];//获取目录编码ks_code
    				$gradeCode=$data[0]['r_grade'];
    				$termCode=$data[0]['r_volume'];
    				$subjectCode=$data[0]['r_subject'];
    				$versionCode=$data[0]['r_version'];
    				$folder='Uploads/'.$gradeCode.'/'.$termCode.'/'.$subjectCode.'/'.$versionCode;
    			}

    			$pics=$dir->dirList('TmpUploads/'.$dirs[$i],'jpg');//本目录下的所有图片

    			$excelpath='TmpUploads/'.$dirs[$i].'/page.xlsx';//excel路径
    			$bookid=$ksid;//书本id
    			if(file_exists($excelpath)){
    				$this->importExcelData($excelpath, $bookid);
    			}


    			if (empty($pics)){
    				array_push($errorinfo, $jiaocaiToUtf8.'目录下文件不存在<br/>');
    				//echo 'TmpUploads/'.$dirs[$i];
    				//$dir->unlinkDir('TmpUploads/'.$dirs[$i]);
    				continue;
    			}
    			$totalpage=count($pics);//总页数
    			$Model_book=M('','','DB_CONFIG')->table('t_book');
    			$data_book=$Model_book->where('bookid="%s"',$ksid)->field('id')->find();
    			$data['bookid']=$ksid;
    			$data['bookname']=$jiaocaiToUtf8;
    			$data['totalpage']=$totalpage;
    			//$data['pageflag']=0;
    			$Model_book_new=M('','','DB_CONFIG')->table('t_book');
    			//查询是否存在此本书的信息
    			if (empty($data_book)){
    				$id=$Model_book_new->add($data);//插入book表成功，返回id
    				array_push($success, $id);
    			}else {
    				//array_push($errorinfo, $jiaocaiToUtf8.'数据库已存在<br/>');
    				//continue;
    				$Model_book_new->where('id="%d"',$data_book['id'])->save($data);
    				$id=$data_book['id'];
                    array_push($success, $id);
    			}
    			//array_push($success, $id);//把插入成功的book表id写入成功数组


/*                //删除原来书本图片
                //删除图片
                $Model_bp=M('','','DB_CONFIG')->table('t_book_page');
                //删除数据库前先删物理文件
                $data_bp=$Model_bp->where('bookid="%s"',$ksid)->field('pagefile')->select();
                foreach ($data_bp as $v){
                    unlink($v['pagefile']);//删除图片
                }
                $Model_bp1=M('','','DB_CONFIG')->table('t_book_page');
                $Model_bp1->where('bookid="%s"',$ksid)->delete();*/


    			$dir->createDir($folder);

    			//$excelpath=dirname($pics[0]).'/page.xlsx';//excel路径

    			for ($j=0;$j<count($pics);$j++){

    				$dir->moveFile($pics[$j], $folder.'/'.basename($pics[$j]),true);//循环移动文件到新目录
    				$Model_book_page=M('','','DB_CONFIG')->table('t_book_page');
    				$pageurl=iconv('gbk', 'utf-8', $folder.'/'.basename($pics[$j]));//当前页数
    				$pagenum=(int)basename($pageurl,".jpg");//当前图片id

    				$arr_book=$Model_book_page->where('bookid="%s" and pagefile="%s" and pagenum="%d"',$ksid,$pageurl,$pagenum)->field('id')->find();
    				$new=M('','','DB_CONFIG')->table('t_book_page');

    				$data1['bookid']=$ksid;
    				$data1['pagefile']=$pageurl;
    				$data1['pagenum']=$pagenum;
    				if (!empty($arr_book)){
    					$new->where('id="%d"',$arr_book['id'])->save($data1);
    				}else {
    					$new->add($data1);
    				}

    				//$excelpath=iconv('gbk', 'utf-8', dirname($pics[$i]));
    				//$excelpath=$excelpath.'/page.xlsx';//excel路径


    			}

    			$Mo_book_page=M('','','DB_CONFIG')->table('t_book_page');
    			$data_type=$Mo_book_page->where('bookid="%s"',$bookid)->min('pagenum');

    			$MO_book=M('','','DB_CONFIG')->table('t_book');
    			if (($data_type['pagenum'])%2==0){
    				//偶数双页左，那么pageflag=0;
    				$data_mo_book['pageflag']=0;
    			}else {
    				//奇数单页左，那么pageflag=1;
    				$data_mo_book['pageflag']=1;
    			}
    			$MO_book->where('bookid="%s"',$bookid)->save($data_mo_book);


    		}

    		$dir->unlinkDir('TmpUploads/'.$jiaocai);
    	}

    	$arr_return['suc']=$success;
    	$arr_return['err']=$errorinfo;
    	$this->ajaxReturn($arr_return);


    }
    //获取插入的图片信息
    public function getBookInfo(){
    	$id=I('id/s');
    	$Model_book=M('','','DB_CONFIG')->table('t_book');
    	$data=$Model_book->where('id="%d"',$id)->field('bookid,bookname,totalpage,pageflag')->find();
    	//var_dump($data);
    	$this->ajaxReturn($data);

    }

    public function getBooks(){
    	$ks_id=I('ks_id/s');
    	$Model_bk_book=M('','','DB_CONFIG')->table('t_book');
    	$data=$Model_bk_book->where('bookid="%s"',$ks_id)->field('bookname,bookid,totalpage,pageflag')->find();
    	//var_dump($data);
    	$this->ajaxReturn($data);

    }
    /**
     * 设置课文页面
     */
    public function changeStart(){
    	$Model_SHARE_KNOWLEDGE_STRUCTURE=M('','','DB_CONFIG1')->table('SHARE_KNOWLEDGE_STRUCTURE');
    	$data_mulu=$Model_SHARE_KNOWLEDGE_STRUCTURE->where("is_unit=0 and flag>0 and ks_type=0 AND KS_LEVEL<=5 AND SUBSTRING(KS_ID,1,6) in('000102','000103','000104','000105','000106','000107','000108','000109','00010a','00010b','00010d') ")->field('KS_ID,P_ID,KS_NAME,C1,DISPLAY_ORDER')->order('display_order')->select();
    	$json="";
    	foreach ($data_mulu as $v){
    		$json.='{id:"'.$v['ks_id'].'", pId:"'.$v['p_id'].'", name:"'.$v['ks_name'].'",file:"'.$v['ks_id'].'"},';
    	}

    	//echo $json;exit();
    	$this->assign('json',$json);//目录树json
    	$this->display();
    }
    /**
     * 课文页面页码设置页面
     */
    public function pageSet(){
    	$Model_SHARE_KNOWLEDGE_STRUCTURE=M('','','DB_CONFIG1')->table('SHARE_KNOWLEDGE_STRUCTURE');
    	$data_mulu=$Model_SHARE_KNOWLEDGE_STRUCTURE->where("is_unit=0 and flag>0 and ks_type=0 AND SUBSTRING(KS_ID,1,6) in('000102','000103','000104','000105','000106','000107','000108','000109','00010a','00010b','00010d') ")->field('KS_ID,P_ID,KS_NAME,C1,DISPLAY_ORDER')->order('display_order')->select();
    	$json="";
    	foreach ($data_mulu as $v){
    		$json.='{id:"'.$v['ks_id'].'", pId:"'.$v['p_id'].'", name:"'.$v['ks_name'].'",file:"'.$v['p_id'].'"},';
    	}

    	//echo $json;exit();
    	$this->assign('json',$json);//目录树json
    	$this->display();
    }
    /**
     * 获取章节信息
     */
    public function getzhangjie(){
    	$bookid=I('bookid/s');
    	$chaperid=I('chaperid/s');
    	$Model_book=M('','','DB_CONFIG')->table('t_book');
    	$data=$Model_book->where('bookid="%s"',$bookid)->field('bookname')->find();
    	if (empty($data)){
    		echo 'error';
    	}else{
    		$Model_book_chapter=M('','','DB_CONFIG')->table('t_book_chapter');
    		$data_chapter=$Model_book_chapter->where('chapterid="%s"',$chaperid)->field('pagebeg,pageend')->find();
    		echo $data['bookname'].'|'.$data_chapter['pagebeg'].'|'.$data_chapter['pageend'];
    	}
    }

    /**
     * excel下载
     */
    public function excelDown(){
    	$Model_SHARE_KNOWLEDGE_STRUCTURE=M('','','DB_CONFIG1')->table('SHARE_KNOWLEDGE_STRUCTURE');
    	$data_mulu=$Model_SHARE_KNOWLEDGE_STRUCTURE->where("is_unit=0 and flag>0 and ks_type=0 AND KS_LEVEL<=5 AND SUBSTRING(KS_ID,1,6) in('000102','000103','000104','000105','000106','000107','000108','000109','00010a','00010b','00010d') ")->field('KS_ID,P_ID,KS_NAME,C1,DISPLAY_ORDER')->order('display_order')->select();
    	$json="";
    	foreach ($data_mulu as $v){
    		$json.='{id:"'.$v['ks_id'].'", pId:"'.$v['p_id'].'", name:"'.$v['ks_name'].'",file:"'.$v['p_id'].'"},';
    	}

    	//echo $json;exit();
    	$this->assign('json',$json);//目录树json
    	$this->display();
    }

    /**
     * 下载excel
     */
    public function makeExcel(){
    	$id=I('id/s');
    	vendor('PHPExcel');
    	$Model=M('','','DB_CONFIG1');
		$sql="SELECT KS_CODE,KS_NAME FROM SHARE_KNOWLEDGE_STRUCTURE WHERE is_unit=0 and flag>0 and ks_type=0 AND KS_CODE LIKE '".$id."%' AND P_ID='".$id."' ORDER BY DISPLAY_ORDER";
    	$data=$Model->query($sql);
    	//var_dump($data);
    	$objExcel = new \PHPExcel();
    	$objWriter = new \PHPExcel_Writer_Excel2007($objExcel);

    	$objExcel->setActiveSheetIndex(0);
    	$objActSheet = $objExcel->getActiveSheet();
    	$objActSheet->setTitle('目录表');

    	$objActSheet->setCellValue('A1','ID');
    	$objActSheet->setCellValue('B1','章节名称');
    	$objActSheet->setCellValue('C1','起始页码');
    	$objActSheet->setCellValue('D1','结束页码');

    	//手动设置前4列宽度
    	$objActSheet->getColumnDimension('A')->setWidth(15);
    	$objActSheet->getColumnDimension('B')->setWidth(35);

    	foreach ($data as $key=>$v){
    		$objActSheet->setCellValueExplicit('A'.($key+2), $v['ks_code'], \PHPExcel_Cell_DataType::TYPE_STRING);
			//$objActSheet->setCellValue('A'.($key+2),$v['ks_code']);
			$objActSheet->setCellValue('B'.($key+2),$v['ks_name']);
		}

		$filename = "page.xlsx";
		$objWriter->save($filename);  //保存到服务器

		ob_end_clean();//清除缓冲区,避免乱码
		$filename=iconv('utf-8', 'gbk', $filename);
		$filesize = filesize($filename);
		header( "Content-Type: application/force-download;charset=utf-8");
		header( "Content-Disposition: attachment; filename= ".$filename);
		header( "Content-Length: ".$filesize);
		ob_clean();
		readfile($filename);

		unlink($filename); //下载完成后删除服务器文件

    }

    /**
     * 批量excel
     */
    public function importExcelData($path,$bookid){
    	vendor('PHPExcel');

//     	$path='TmpUploads/一年级-上学期-语文-人教版/page.xlsx';
//     	$path=iconv('utf-8', 'gbk', $path);
//     	$bookid='1111111';
    	$arr_excel = readExcel($path);

    	$insert_flag = true; //是否写数据库标志
    	$errorinfo = array(); //错误信息数组
    	$success_num = 0; //导入成功数

        $Model_book_c=M('','','DB_CONFIG')->table('t_book_chapter');
        $Model_book_c->where('bookid="%s"',$bookid)->delete();

    	foreach ($arr_excel as $row=>$v){
    		$chapterid=$v['Column']['A']; //章节id
    		$chaptername=$v['Column']['B'];//章节名称
    		$pagebeg=$v['Column']['C'];//开始页码
    		$pageend=$v['Column']['D'];//结束页码

    		/**
    		 * 判断excel里是否有空值
    		 */
    		if ($chapterid == ""){
    			array_push($errorinfo, 'ID不能为空' . '|' . $row); //记录错误信息
    			continue;//如果当前算式为空，跳出本次循环
    		}
    		if ($chaptername == ""){
    			array_push($errorinfo, '章节名称不能为空' . '|' . $row); //记录错误信息
    			continue;//如果当前结果为空，跳出本次循环
    		}
    		if ($pagebeg === ""){
    			array_push($errorinfo, '起始页码不能为空' . '|' . $row); //记录错误信息
    			continue;//如果当前结果为空，跳出本次循环
    		}
    		if ($pageend == ""){
    			array_push($errorinfo, '结束页码不能为空' . '|' . $row); //记录错误信息
    			continue;//如果当前结果为空，跳出本次循环
    		}

    		/**
    		 * 如果以上没有空值的话继续执行
    		 */


    		$data['bookid']=$bookid;
    		$data['chapterid']=$chapterid;
    		$data['chaptername']=$chaptername;
    		$data['pagebeg']=(int)$pagebeg;
    		$data['pageend']=(int)$pageend;
            //var_dump($data);
    		//$data_c=$Model_book_c->where('bookid="%s" and chapterid="%s"',$bookid,$chapterid)->find();

            $Model_book_chapter=M('','','DB_CONFIG')->table('t_book_chapter');
            $Model_book_chapter->add($data);
    		// if (empty($data_c)){
      //           $Model_book_chapter=M('','','DB_CONFIG')->table('t_book_chapter');
    		// 	$Model_book_chapter->add($data);
    		// }else {
      //           $Model_book_chapter=M('','','DB_CONFIG')->table('t_book_chapter');
    		// 	$Model_book_chapter->where('bookid="%s" and chapterid="%s"',$bookid,$chapterid)->save($data);
    		// }

    		$success_num = $success_num + 1;
    	}

//     	if (count($errorinfo) > 0) {
//     		echo "一共导入了" . $success_num . "条记录<br>";
//     		echo "以下数据没有导入<br>";
//     		for ($i = 0; $i < count($errorinfo); $i++) {
//     			$temp_info = explode("|", $errorinfo[$i]);
//     			echo "第" . $temp_info[1] . "行错误信息：" . $temp_info[0] . "<br>";
//     		}
//     	} else {
//     		echo "数据导入成功<br>";
//     		echo "一共导入了" . $success_num . "条记录<br>";
//     	}

    }
    /**
     * 左右设置
     */
    public function updateSelect(){
    	$id=I('id/d');
    	$bookid=I('bookid/s');
    	$m=M('','','DB_CONFIG')->table('t_book');
    	$data['pageflag']=$id;
    	$m->where('bookid="%s"',$bookid)->save($data);
    }
    /**
     * 设置章节对应页码
     */
    public function resetPage(){
    	$bookid=I('bookid/s');
    	$chapterid=I('chapterid/s');
    	$chaptername=I('chaptername/s');
    	$pagebeg=I('pagebeg/d');
    	$pageend=I('pageend/d');
    	$Model_book_page=M('','','DB_CONFIG')->table('t_book_page');
    	$sum=$Model_book_page->where('pagenum>="%d" and pagenum<="%d"',$pagebeg,$pageend)->count();
    	$total=$pageend-$pagebeg+1;
    	if ($sum<$total){
    		echo 'error';
    	}else {
    		$Model_book_chapter=M('','','DB_CONFIG')->table('t_book_chapter');
    		$data_ch=$Model_book_chapter->where('chapterid="%s"',$chapterid)->field('id')->find();//查询此章节是否设置过
    		$data['bookid']=$bookid;
    		$data['chapterid']=$chapterid;
    		$data['chaptername']=rtrim($chaptername);
    		$data['pagebeg']=$pagebeg;
    		$data['pageend']=$pageend;
    		$Model_book_chapter_new=M('','','DB_CONFIG')->table('t_book_chapter');
    		//var_dump($data_ch);
    		//echo $bookid.'|'.$chapterid;
    		if (empty($data_ch)){
    			$Model_book_chapter_new->add($data);
    		}else {
    			//echo 'a';
    			$Model_book_chapter_new->where('id="%d"',$data_ch['id'])->save($data);
    		}
    	}
    }

    /**
     * 预览页面
     */
    public function preview(){
    	$Model_SHARE_KNOWLEDGE_STRUCTURE=M('','','DB_CONFIG1')->table('SHARE_KNOWLEDGE_STRUCTURE');
    	$data_mulu=$Model_SHARE_KNOWLEDGE_STRUCTURE->where("is_unit=0 and flag>0 and ks_type=0 AND SUBSTRING(KS_ID,1,6) in('000102','000103','000104','000105','000106','000107','000108','000109','00010a','00010b','00010d') ")->field('KS_ID,P_ID,KS_NAME,KS_LEVEL')->order('display_order')->select();
    	$json="";
    	foreach ($data_mulu as $v){
    		$json.='{id:"'.$v['ks_id'].'", pId:"'.$v['p_id'].'", name:"'.$v['ks_name'].'",file:"'.$v['ks_level'].'"},';
    	}

    	//echo $json;exit();
    	$this->assign('json',$json);//目录树json
    	$this->display();
    }
    //获取当前章节的图片数组
    public function getPics_bak(){
    	$chapterid=I('chapterid/s');
    	$bookid=I('bookid/s');
    	$Model=M('','','DB_CONFIG')->table('t_book_chapter');
    	//$Model->where('chapterid="%s"',$chaperid)->field($field);
    	$sql="SELECT te.pagefile,te.pagenum FROM t_book_chapter tr,t_book_page te WHERE tr.bookid=te.bookid AND tr.chapterid='".$chapterid."' AND te.pagenum>=tr.pagebeg AND te.pagenum<=tr.pageend  ORDER BY te.pagenum";
    	$data=$Model->query($sql);
    	$sql1="SELECT pageflag FROM t_book WHERE bookid = '".$bookid."'";
    	$data_flag=$Model->query($sql1);
    	$arr=array();
    	$arr['data']=$data;
    	$arr['flag']=$data_flag;
    	//var_dump($arr);
    	$this->ajaxReturn($arr);
    }
    public function getPics(){
    	$chapterid=I('chapterid/s');
    	$bookid=I('bookid/s');
    	$Model=M('','','DB_CONFIG');
    	//$Model->where('chapterid="%s"',$chaperid)->field($field);
//     	$sql="SELECT te.pagefile,te.pagenum FROM t_book_chapter tr,t_book_page te WHERE tr.bookid=te.bookid AND tr.chapterid='".$chapterid."' AND te.pagenum>=tr.pagebeg AND te.pagenum<=tr.pageend  ORDER BY te.pagenum";
//     	$data=$Model->query($sql);
		$sql="SELECT pagebeg,pageend FROM t_book_chapter WHERE chapterid='".$chapterid."'";
    	$sql1="SELECT pageflag FROM t_book WHERE bookid = '".$bookid."'";
    	$data_page=$Model->query($sql);
    	$data_flag=$Model->query($sql1);
    	$pageflag=$data_flag[0]['pageflag'];
    	if (empty($data_page)){
    		$data='';
    	}else {
		    	$pagebeg=$data_page[0]['pagebeg'];
		    	$pageend=$data_page[0]['pageend'];
		    	if ($pageflag==1){
		    		//奇数在左
		    		if ($pagebeg%2==0){
		    			//如果是偶数，从上一页开始
		    			$pagebeg=$pagebeg-1;
		    		}else{
		    			//如果是奇数，从当前页开始
		    			$pagebeg==$pagebeg;
		    		}
		    		if ($pageend%2==0){
		    			//如果是偶数
		    			$pageend==$pageend;
		    		}else{
		    			//如果是奇数
		    			$pageend=$pageend+1;
		    		}
		    	}else {
		    		//偶数在左
		    		if ($pagebeg%2==0){
		    			//偶数
		    			$pagebeg==$pagebeg;
		    		}else {
		    			//奇数
		    			$pagebeg=$pagebeg-1;
		    		}
		    		if ($pageend%2==0){
		    			//偶数
		    			$pageend=$pageend+1;
		    		}else {
		    			//奇数
		    			$pageend==$pageend;
		    		}
		    	}

		  		$sql3="SELECT pagefile,pagenum FROM t_book_page WHERE bookid='".$bookid."' AND pagenum>=".$pagebeg." AND pagenum<=".$pageend." ORDER BY pagenum";
		  		$data=$Model->query($sql3);
    	    	}
    	$arr=array();
    	$arr['data']=$data;
    	$arr['flag']=$data_flag;
    	//var_dump($arr);
    	$this->ajaxReturn($arr);
    }
    //获取整本书的图片数组
    public function getPicsForBook(){
    	$bookid=I('bookid/s');
    	$Model=M('','','DB_CONFIG');
    	//$Model->where('chapterid="%s"',$chaperid)->field($field);
    	$sql="SELECT pagefile,pagenum FROM t_book_page WHERE bookid='".$bookid."' ORDER BY pagenum";
    	$data=$Model->query($sql);
    	$sql1="SELECT pageflag FROM t_book WHERE bookid = '".$bookid."'";
    	$data_flag=$Model->query($sql1);
    	$arr=array();
    	$arr['data']=$data;
    	$arr['flag']=$data_flag;
    	//var_dump($arr);
    	$this->ajaxReturn($arr);
    }

    //上下翻页时，返回当前页面的值
    public function changepage(){
    	$pageleft=I('pageleft/d');
    	$chapterid=I('chapterid/s');
    	$bookid=I('bookid/s');
    	$flag=I('flag/d');
    	$arr=array();
    	if ($flag==1){
    		//向左滑
    		array_push($arr, $this->query($pageleft+2,$chapterid,$bookid));
    		array_push($arr, $this->query($pageleft+3,$chapterid,$bookid));
    	}else {
    		//向右滑
    		array_push($arr, $this->query($pageleft-2,$chapterid,$bookid));
    		array_push($arr, $this->query($pageleft-1,$chapterid,$bookid));
    	}
    	echo $arr[0][0]['pagefile'].'|'.$arr[1][0]['pagefile'];
    }

    protected function query($page,$chapterid,$bookid){
    	$Model=M('','','DB_CONFIG');
    	if (empty($chapterid)){
    		$sql="SELECT pagefile FROM t_book_page WHERE pagenum='".$page."' and bookid='".$bookid."'";
    	}else {
	    	//$sql="SELECT t.pagefile FROM t_book_chapter l,t_book_page t WHERE l.bookid=t.bookid AND l.chapterid='".$chapterid."' AND ".$page.">=l.pagebeg AND ".$page."<=l.pageend AND t.pagenum=".$page;
	    	$sql="SELECT t.pagefile FROM t_book_chapter l,t_book_page t WHERE l.bookid=t.bookid AND l.chapterid='".$chapterid."' AND t.pagenum=".$page;
    	}
    	$data=$Model->query($sql);
    	return $data;
    }

    /**
     * MP3管理页面
     */
    public function audioInfo(){
    	$Model_SHARE_KNOWLEDGE_STRUCTURE=M('','','DB_CONFIG1')->table('SHARE_KNOWLEDGE_STRUCTURE');
    	$data_mulu=$Model_SHARE_KNOWLEDGE_STRUCTURE->where("is_unit=0 and flag>0 and ks_type=0 AND SUBSTRING(KS_ID,1,6) in('000102','000103','000104','000105','000106','000107','000108','000109','00010a','00010b','00010d') ")->field('KS_ID,P_ID,KS_NAME,C1,DISPLAY_ORDER')->order('display_order')->select();
    	$json="";
    	foreach ($data_mulu as $v){
    		$json.='{id:"'.$v['ks_id'].'", pId:"'.$v['p_id'].'", name:"'.$v['ks_name'].'",file:"'.$v['p_id'].'"},';
    	}

    	//echo $json;exit();
    	$this->assign('json',$json);//目录树json
    	$this->display();
    }
    /**
     * 获取章节下的MP3
     */
    public function getmp3Info(){
    	$bookid=I('bookid/s');
    	$chaperid=I('chaperid/s');
    	$Model_book=M('','','DB_CONFIG')->table('t_book');
    	$data=$Model_book->where('bookid="%s"',$bookid)->field('bookname')->find();
    	$Model_mp3=M('','','DB_CONFIG')->table('t_book_chapter_mp3');
    	$sql="SELECT GROUP_CONCAT(musicname) as name FROM t_book_chapter_mp3 WHERE chapterid='".$chaperid."'";
    	$info=$Model_mp3->query($sql);
    	//var_dump($info);
    	if (empty($data)){
    		echo 'error';
    	}else{
    		echo $data['bookname'].'|'.$info[0]['name'];
    	}
    }
    /**
     * 音频管理页面
     */
    public function audioManage(){
    	$chapterid=I('chapterid/s');
    	$bookid=I('bookid/s');
    	$Model=M('','','DB_CONFIG')->table('t_book_chapter_mp3');
    	$data=$Model->where('chapterid="%s"',$chapterid)->field('id,musicname,musicfile')->select();
    	$this->assign('data',$data);
    	$this->assign('chapterid',$chapterid);
    	$this->assign('bookid',$bookid);
    	$this->display();
    }
    /**
     * 添加mp3到数据库
     */
    public function addMP3(){
    	$chapterid=I('chapterid/s');
    	$bookid=I('bookid/s');
    	$filename=I('filename/s');
    	$filepath=I('filepath/s');
    	$Model=M('','','DB_CONFIG')->table('t_book_chapter_mp3');
    	$data['bookid']=$bookid;
    	$data['chapterid']=$chapterid;
    	$data['musicname']=$filename;
    	$data['musicfile']=$filepath;
    	$Model->add($data);
    }
    /**
     * 删除mp3
     */
    public function delMP3(){
    	$id=I('id/d');
    	$Model=M('','','DB_CONFIG')->table('t_book_chapter_mp3');
    	$data=$Model->delete($id);
    	echo $data;
    }
    /**
     * 教材信息导出页面
     */
    public function outFileInfo(){
    	$Model_SHARE_KNOWLEDGE_STRUCTURE=M('','','DB_CONFIG1')->table('SHARE_KNOWLEDGE_STRUCTURE');
    	$data_mulu=$Model_SHARE_KNOWLEDGE_STRUCTURE->where("is_unit=0 and flag>0 and ks_type=0 AND SUBSTRING(KS_ID,1,6) in('000102','000103','000104','000105','000106','000107','000108','000109','00010a','00010b','00010d') ")->field('KS_ID,P_ID,KS_NAME,KS_LEVEL')->order('display_order')->select();
    	$json="";
    	foreach ($data_mulu as $v){
    		$json.='{id:"'.$v['ks_id'].'", pId:"'.$v['p_id'].'", name:"'.$v['ks_name'].'",file:"'.$v['ks_level'].'"},';
    	}

    	//echo $json;exit();
    	$this->assign('json',$json);//目录树json
    	$this->display();
    }
    /**
     * 图书管理页面
     */
    public function books(){
    	$this->display();
    }
    /**
     * 分页展示图书信息
     */
    public function fenye(){
    	$pageCurrent=I('pageCurrent/d',0);
    	$page_size=I('page_size/d',0);
    	$title=I('title/s');
    	$Model=M('','','DB_CONFIG');
    	$sql_where='where id<>0';
    	if($title==''){
    		$sql_where.='';
    	}else{
    		$sql_where.=' and bookname like "%'.$title.'%"';
    	}
    	$sql='select count(*) as num from t_book ';
    	$sql1="select bookid,bookname,if(pageflag=1,'单页左','双页左') as pageflag,totalpage from t_book ";

    	$sql_limit=' limit '.($pageCurrent-1)*$page_size.','.$page_size;
    	$sql_order=' order by id';
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
    	//echo $sql.$sql_where.$sql_order.$sql_limit;
    	$word[$page]=$data;
    	$this->ajaxReturn($word);
    }
    /**
     * 删除图书
     */
    public function delBook(){
    	$id=I('id/s');
    	//删除课本
    	$Model_book=M('','','DB_CONFIG')->table('t_book');
   		$Model_book->where('bookid="%s"',$id)->delete();
    	//删除章节对应页码
    	$Model_bc=M('','','DB_CONFIG')->table('t_book_chapter');
    	$Model_bc->where('bookid="%s"',$id)->delete();
    	//删除音频
    	$Model_bcm=M('','','DB_CONFIG')->table('t_book_chapter_mp3');
    	//删除数据库前先删物理文件
    	$data_bcm=$Model_bcm->where('bookid="%s"',$id)->field('musicfile')->select();
    	foreach ($data_bcm as $v){
    		unlink('Uploads/audio/'.$v['musicfile']);//删除音频
    	}
    	$Model_bcm1=M('','','DB_CONFIG')->table('t_book_chapter_mp3');
    	$Model_bcm1->where('bookid="%s"',$id)->delete();

    	//删除图片
    	$Model_bp=M('','','DB_CONFIG')->table('t_book_page');
    	//删除数据库前先删物理文件
    	$data_bp=$Model_bp->where('bookid="%s"',$id)->field('pagefile')->select();
    	foreach ($data_bp as $v){
    		unlink($v['pagefile']);//删除图片
    	}
    	$Model_bp1=M('','','DB_CONFIG')->table('t_book_page');
    	$Model_bp1->where('bookid="%s"',$id)->delete();

    	$Model_bph=M('','','DB_CONFIG')->table('t_book_page_hot');
    	//逻辑删除
    	$data['isdel']=1;
    	//$Model_bph->where('bookid="%s"',$id)->save($data);

    }
    /**
     * 查看文件是否存在
     */
    public function filestatus(){
    	$bookid=I('bookid/s','');
    	$chapterid=I('chapterid/s','');
    	$Model_book=M('','','DB_CONFIG')->table('t_book');
    	$data_book=$Model_book->where('bookid="%s"',$bookid)->field('bookname')->find();
    	$Model_chapter=M('','','DB_CONFIG')->table('t_book_chapter');
    	if (empty($data_book)){
    		echo 'error';
    	}else {
    		if ($chapterid==''){
    			//整本书
    			$data_chapter=$Model_chapter->where('bookid="%s"',$bookid)->field('chapterid,chaptername,pagebeg')->order('pagebeg')->select();
    		}else {
    			//本章节
    			$data_chapter=$Model_chapter->where('chapterid="%s"',$chapterid)->field('id')->find();
    		}
    		if (empty($data_chapter)){
    			echo 'error';
    		}else {
    			$bookarr['bookname']=$data_book['bookname'];
    			$bookarr['chapters']=$data_chapter;
    			//var_dump($bookarr);
    			$this->ajaxReturn($bookarr);
    		}
    	}

    }
    /**
     * 下载文件
     */
    public function downloadRes(){
    	vendor('fileDirUtil');
    	$dir = new \fileDirUtil();
    	$bookid=I('bookid/s','');
    	//$chapterid=I('chapterid/s','');
    	$str=I('str/s');
    	$chapterArr=explode('|', $str);//章节id数组

    	$Model_book=M('','','DB_CONFIG')->table('t_book');
    	$data_book=$Model_book->where('bookid="%s"',$bookid)->field('bookname,pageflag')->find();
    	$bookname=$data_book['bookname'];
    	$count= explode('英语',$bookname);
    	$isEn=false;
    	if (count($count)>1){
    		$isEn=true;
    	}
    	$pageflag=$data_book['pageflag'];
    	$bookname=iconv('utf-8', 'gbk', $bookname);
    	if ($isEn==true){
    		$title='电子教材';
    	}else {
    		$title='》电子教材';
    	}

    	$title=iconv('utf-8', 'gbk', $title);
    	//$dir->createDir('download/'.$bookname);
//     	echo '开始生成文件夹'.date('Y-m-d H:i:s',time()).'<br>';
    	for ($i=0;$i<count($chapterArr);$i++){
    		$chapterid=$chapterArr[$i];
    		$this->copyRes($bookid, $bookname, $pageflag, $chapterid, $title,$isEn);
    	}

    	$this->ExcelForCCM($bookid,$chapterArr,$bookname,$isEn);

    	$zip=new \ZipArchive();
    	$zifile = 'download/' . $bookid . '.zip';
    	if($zip->open($zifile, \ZipArchive::OVERWRITE)=== TRUE){
    		$this->addFileToZip('download/'.$bookname, $zip); //调用方法，对要打包的根目录进行操作，并将ZipArchive的对象传递给方法
    		$zip->close(); //关闭处理的zip文件
    	}



    	$bookname=iconv('gbk', 'utf-8', $bookname);
//     	echo '打包结束'.date('Y-m-d H:i:s',time()).'<br>';
    	$this->download ($zifile,' '.$bookname . '.zip');
    	$dir->unlinkFile($zifile);
    	$bookname=iconv('utf-8', 'gbk', $bookname);
    	$dir->unlinkDir('./download/'.$bookname);
    }



    //下载测试
    public function test(){
    	echo date('Y-m-d H:i:s',time()).'<br>';
    	$zip=new \ZipArchive();
    	$zifile = 'download/1.zip';
    	$bookname='五年级-上学期-语文-人教版';
    	//$bookname=iconv('gbk', 'utf-8', $bookname);
    	$bookname=iconv('utf-8', 'gbk', $bookname);
    	//chdir('download/');
    	//echo getcwd();
    	if($zip->open($zifile, \ZipArchive::OVERWRITE)=== TRUE){
    		$this->addFileToZip('download/'.$bookname, $zip); //调用方法，对要打包的根目录进行操作，并将ZipArchive的对象传递给方法
    		$zip->close(); //关闭处理的zip文件
    	}
    	echo date('Y-m-d H:i:s',time()).'<br>';
    	//$this->display();
    }
    function addFileToZip($path,$zip){
    	//echo $path;
    	$handler=opendir($path); //打开当前文件夹由$path指定。
    	while(($filename=readdir($handler))!==false){
    		//var_dump($filename);continue;
    		if($filename != "." && $filename != ".."){//文件夹文件名字为'.'和‘..’，不要对他们进行操作
    			if(is_dir($path."/".$filename)){// 如果读取的某个对象是文件夹，则递归
    				$this->addFileToZip($path."/".$filename, $zip);
    			}else{ //将文件加入zip对象
    				//echo $path."/".$filename.'<br>';
    				$a=substr($path."/".$filename,strlen('download/'));
    				//$zip->addFile($path."/".$filename);
    				$zip->addFile($path."/".$filename,$a);
    			}
    		}
    	}
    	@closedir($path);
    }
    /**
     * 复制本章节文件
     */
    public function copyRes($bookid,$bookname,$pageflag,$chapterid,$title,$isEn){
        vendor('fileDirUtil');
        $dir = new \fileDirUtil();
        $Model_chapter=M('','','DB_CONFIG')->table('t_book_chapter');
        $data_chapter=$Model_chapter->where('chapterid="%s"',$chapterid)->field('chaptername,pagebeg,pageend')->find();
        $chaptername=$data_chapter['chaptername'];
        $chaptername=iconv('utf-8', 'gbk', rtrim($chaptername));
        $pagebeg=$data_chapter['pagebeg'];
        $pageend=$data_chapter['pageend'];
        //$dir->createDir('download/'.$bookname.'/'.$chaptername);
        if ($isEn==true){
            $str="校本资源：";
        }else {
            $str="校本资源：《";
        }

        $str=iconv('utf-8', 'gbk', $str);
        $todayDir='download/'.$bookname.'/'.$chaptername.'/'.$str.$chaptername.$title.'/';
        //复制data.xml
        $dir->copyFile('Uploads/template/data.xml', $todayDir.'data.xml',true);
        $xmlFile=$todayDir.'data.xml';
        $xml = $dir->readsFile($xmlFile);

        $Model=M('','','DB_CONFIG');
        if ($pageflag==1){
            //奇数在左
            if ($pagebeg%2==0){
                //如果是偶数，从上一页开始
                $pagebeg=$pagebeg-1;
            }else{
                //如果是奇数，从当前页开始
                $pagebeg==$pagebeg;
            }
            if ($pageend%2==0){
                //如果是偶数
                $pageend==$pageend;
            }else{
                //如果是奇数
                $pageend=$pageend+1;
            }
        }else {
            //偶数在左
            if ($pagebeg%2==0){
                //偶数
                $pagebeg==$pagebeg;
            }else {
                //奇数
                $pagebeg=$pagebeg-1;
            }
            if ($pageend%2==0){
                //偶数
                $pageend=$pageend+1;
            }else {
                //奇数
                $pageend==$pageend;
            }
        }
        $Model_page=M('','','DB_CONFIG')->table('t_book_page');
//      $data_beg=$Model_page->where('pagenum="%d"',$pagebeg)->find();
//      $data_end=$Model_page->where('pagenum="%d"',$pageend)->find();
        //查询图片
        $sql_page="SELECT pagefile FROM t_book_page WHERE bookid='".$bookid."' AND pagenum>=".$pagebeg." AND pagenum<=".$pageend." order by pagenum";
        //exit($sql_page);
        $data_page=$Model->query($sql_page);

        //查询音频
        // $sql_audio="SELECT musicname,musicfile FROM t_book_chapter_mp3 WHERE bookid='".$bookid."' AND chapterid='".$chapterid."' order by id";
        // //exit($sql_audio);
        // $data_audio=$Model->query($sql_audio);

        $hotnum=ceil(count($data_page)/2);
        //热点数据
        $hot_xml='';
        //$arr_hot=array();
        for ($i=$pagebeg;$i<=($pageend-1);$i=$i+2){
            $sql_hot="SELECT bookid,pagenum,plist,vbeg,vend,type,urlname,isdel,iscut,hotmp3 FROM t_book_page_hot WHERE bookid='".$bookid."' AND isdel=0 AND isbig=0 AND iscut=1 AND pagenum='".$i."' order by id";
            $sql_hot1="SELECT bookid,pagenum,plist,vbeg,vend,type,urlname,isdel,iscut,hotmp3 FROM t_book_page_hot WHERE bookid='".$bookid."' AND isdel=0 AND isbig=0 AND iscut=1 AND pagenum='".($i+1)."' order by id";

            //echo $sql_hot.'<br>'.$sql_hot1;exit;
            //$sql_hot="SELECT * FROM t_book_page_hot WHERE bookid='".$bookid."' AND isdel=0 AND pagenum='".$i."'";
            $data_hot=$Model->query($sql_hot);
            $data_hot1=$Model->query($sql_hot1);

            $hot_xml.=" <hotpage name=\"第一整页热点声音\" id=\"".$i."\">\r\n";
            if (empty($data_hot)&&empty($data_hot1)){

                $hot_xml.="    <leftpage>\r\n";
                //$hot_xml.="   <hotmusic id=\"1\">\r\n         <name>热区</name>\r\n         <link>null</link>\r\n           <pX>1</pX>\r\n          <pY>1</pY>\r\n          <width>1</width>\r\n            <height>1</height>\r\n          <play>00:13.890</play>\r\n          <stop>00:17.890</stop>\r\n      </hotmusic>\r\n";
                $hot_xml.="    </leftpage>\r\n";

                $hot_xml.="    <rightpage>\r\n";
                //$hot_xml.=" <hotmusic id=\"1\">\r\n         <name>热区</name>\r\n         <link>null</link>\r\n           <pX>1</pX>\r\n          <pY>1</pY>\r\n          <width>1</width>\r\n            <height>1</height>\r\n          <play>00:13.890</play>\r\n          <stop>00:17.890</stop>\r\n      </hotmusic>\r\n";
                $hot_xml.="    </rightpage>\r\n";
            }else {
                //左半页
                $hot_xml.="    <leftpage>\r\n";
                foreach ($data_hot as $key=>$v){
                    //$hot_xml.="       <hotmusic id=\"".$key."\">\r\n          <name>热区</name>\r\n         <link>".basename($v['urlname'])."</link>\r\n            <pX>".$v['x']."</pX>\r\n            <pY>".$v['y']."</pY>\r\n            <width>".$v['w']."</width>\r\n          <height>".$v['h']."</height>\r\n            <play>".$v['vbeg']."</play>\r\n         <stop>".$v['vend']."</stop>\r\n     </hotmusic>\r\n";

                    $hot_xml.="     <hotmusic id=\"".$key."\">\r\n          <name>热区</name>\r\n          <link>".basename($v['hotmp3'])."</link>\r\n          <plist>".$v['plist']."</plist>\r\n          <play>00:00:000</play>\r\n          <stop>00:00:000</stop>\r\n     </hotmusic>\r\n";
                    //array_push($arr_hot, $v['urlname']);
                    $dir->copyFile('Uploads/audio/'.$v['hotmp3'], $todayDir.basename($v['hotmp3']),true);
                }
                $hot_xml.="    </leftpage>\r\n";

                //右半页
                $hot_xml.="    <rightpage>\r\n";
                foreach ($data_hot1 as $key=>$v){
                    //$hot_xml.="       <hotmusic id=\"".$key."\">\r\n          <name>热区</name>\r\n         <link>".basename($v['urlname'])."</link>\r\n            <pX>".$v['x']."</pX>\r\n            <pY>".$v['y']."</pY>\r\n            <width>".$v['w']."</width>\r\n          <height>".$v['h']."</height>\r\n            <play>".$v['vbeg']."</play>\r\n         <stop>".$v['vend']."</stop>\r\n     </hotmusic>\r\n";

                    $hot_xml.="     <hotmusic id=\"".$key."\">\r\n          <name>热区</name>\r\n          <link>".basename($v['hotmp3'])."</link>\r\n          <plist>".$v['plist']."</plist>\r\n          <play>00:00:000</play>\r\n          <stop>00:00:000</stop>\r\n     </hotmusic>\r\n";
                    //array_push($arr_hot, $v['urlname']);
                    $dir->copyFile('Uploads/audio/'.$v['hotmp3'], $todayDir.basename($v['hotmp3']),true);
                }
                $hot_xml.="    </rightpage>\r\n";


            }
            $hot_xml.=" </hotpage>\r\n";
        }

//      $arr_uni=array_unique($arr_hot);//去重
//      $arr_uni=array_values($arr_uni);//重新排序
//      for ($i=0;$i<count($arr_uni);$i++){
//          $dir->copyFile('Uploads/audio'.$arr_uni[$i], $todayDir.md5(uniqid(microtime(true),true)).'.mp3',true);
//      }

        //复制图片
        $pics_xml='';
        $pics_xml .= "<content name=\"书页内容\" id=\"1\" width=\"1110\" height=\"1570\">\r\n";

        $picWidthHeight = '';
        $picWidthHeight .= "<pic>\r\n";
        foreach ($data_page as $key=>$v){
            //$file=iconv('utf-8', 'gbk', $v['pagefile']);
            //echo $v['pagefile'];

            $dir->copyFile($v['pagefile'], $todayDir.basename($v['pagefile']),true);
            if($key==0||$key==1){
                $image = new \Think\Image();
                $image->open($todayDir.basename($v['pagefile']));
                $image->thumb(300, 800)->save($todayDir.'thumb'.$key.'.jpg');
            }

            $pics_xml.="            <page>".basename($v['pagefile'])."</page>\r\n";

            $picInfo = getimagesize($v['pagefile']);
            $w_d = $picInfo[0];
            $h_d = $picInfo[1];
            $picWidthHeight.="            <width>".$w_d."</width>\r\n";
            $picWidthHeight.="            <height>".$h_d."</height>\r\n";
        }
        $pics_xml .= "</content>\r\n";
        $picWidthHeight .= "</pic>\r\n";
        // //复制音频
        // $audio_xml='';
        // foreach ($data_audio as $key=>$v){
        //  $musicname=iconv('utf-8', 'gbk', $v['musicname']);
        //  $dir->copyFile('Uploads/audio/'.$v['musicfile'], $todayDir.$musicname,true);
        //  $audio_xml.="   <bookmusic name=\"课文声音\" id=\"".($key+1)."\">\r\n       <name>".substr($v['musicname'],0,strrpos($v['musicname'], '.'))."</name>\r\n        <link>".$v['musicname']."</link>\r\n        <icon>mp3</icon>\r\n    </bookmusic>\r\n";
        // }
        // $xml = str_replace('$$audio$$', $audio_xml, $xml);
        $xml = str_replace('$$pics$$', $pics_xml, $xml);
        $xml = str_replace('$$hotmusic$$', $hot_xml, $xml);
        $xml = str_replace('$$picWidthHeight$$', $picWidthHeight, $xml);
        $dir->writeFile($xmlFile, $xml);

				// $xmlContent = simplexml_load_file($xmlFile);
				// file_put_contents($todayDir.'data.js','var res = '.json_encode($xmlContent).';');
    }
    /**
     * 复制本章节文件
     */
	public function copyRes1($bookid,$bookname,$pageflag,$chapterid,$title,$isEn){
		vendor('fileDirUtil');
		$dir = new \fileDirUtil();
		$Model_chapter=M('','','DB_CONFIG')->table('t_book_chapter');
		$data_chapter=$Model_chapter->where('chapterid="%s"',$chapterid)->field('chaptername,pagebeg,pageend')->find();
		$chaptername=$data_chapter['chaptername'];
		$chaptername=iconv('utf-8', 'gbk', rtrim($chaptername));
		$pagebeg=$data_chapter['pagebeg'];
		$pageend=$data_chapter['pageend'];
		//$dir->createDir('download/'.$bookname.'/'.$chaptername);
		if ($isEn==true){
			$str="校本资源：";
		}else {
			$str="校本资源：《";
		}

		$str=iconv('utf-8', 'gbk', $str);
		$todayDir='download/'.$bookname.'/'.$chaptername.'/'.$str.$chaptername.$title.'/';
		//复制data.xml
		$dir->copyFile('Uploads/template/data.xml', $todayDir.'data.xml',true);
		$xmlFile=$todayDir.'data.xml';
		$xml = $dir->readsFile($xmlFile);

		$Model=M('','','DB_CONFIG');
		if ($pageflag==1){
			//奇数在左
			if ($pagebeg%2==0){
				//如果是偶数，从上一页开始
				$pagebeg=$pagebeg-1;
			}else{
				//如果是奇数，从当前页开始
				$pagebeg==$pagebeg;
			}
			if ($pageend%2==0){
				//如果是偶数
				$pageend==$pageend;
			}else{
				//如果是奇数
				$pageend=$pageend+1;
			}
		}else {
			//偶数在左
			if ($pagebeg%2==0){
				//偶数
				$pagebeg==$pagebeg;
			}else {
				//奇数
				$pagebeg=$pagebeg-1;
			}
			if ($pageend%2==0){
				//偶数
				$pageend=$pageend+1;
			}else {
				//奇数
				$pageend==$pageend;
			}
		}
		$Model_page=M('','','DB_CONFIG')->table('t_book_page');
// 		$data_beg=$Model_page->where('pagenum="%d"',$pagebeg)->find();
// 		$data_end=$Model_page->where('pagenum="%d"',$pageend)->find();
		//查询图片
		$sql_page="SELECT pagefile FROM t_book_page WHERE bookid='".$bookid."' AND pagenum>=".$pagebeg." AND pagenum<=".$pageend." order by pagenum";
		//exit($sql_page);
		$data_page=$Model->query($sql_page);

		//查询音频
		// $sql_audio="SELECT musicname,musicfile FROM t_book_chapter_mp3 WHERE bookid='".$bookid."' AND chapterid='".$chapterid."' order by id";
		// //exit($sql_audio);
		// $data_audio=$Model->query($sql_audio);

		$hotnum=ceil(count($data_page)/2);
		//热点数据
		$hot_xml='';
		//$arr_hot=array();
		for ($i=$pagebeg;$i<=($pageend-1);$i=$i+2){
            $sql_hot="SELECT bookid,pagenum,plist,vbeg,vend,type,urlname,isdel,iscut,hotmp3 FROM t_book_page_hot WHERE bookid='".$bookid."' AND isdel=0 AND isbig=0 AND iscut=1 AND pagenum='".$i."' order by id";
			$sql_hot1="SELECT bookid,pagenum,plist,vbeg,vend,type,urlname,isdel,iscut,hotmp3 FROM t_book_page_hot WHERE bookid='".$bookid."' AND isdel=0 AND isbig=0 AND iscut=1 AND pagenum='".($i+1)."' order by id";

            //echo $sql_hot.'<br>'.$sql_hot1;exit;
            //$sql_hot="SELECT * FROM t_book_page_hot WHERE bookid='".$bookid."' AND isdel=0 AND pagenum='".$i."'";
            $data_hot=$Model->query($sql_hot);
			$data_hot1=$Model->query($sql_hot1);

			$hot_xml.="	<hotpage name=\"第一整页热点声音\" id=\"".$i."\">\r\n";
			if (empty($data_hot)&&empty($data_hot1)){

                $hot_xml.="    <leftpage>\r\n";
				//$hot_xml.="	<hotmusic id=\"1\">\r\n			<name>热区</name>\r\n			<link>null</link>\r\n			<pX>1</pX>\r\n			<pY>1</pY>\r\n			<width>1</width>\r\n			<height>1</height>\r\n			<play>00:13.890</play>\r\n			<stop>00:17.890</stop>\r\n		</hotmusic>\r\n";
                $hot_xml.="    </leftpage>\r\n";

                $hot_xml.="    <rightpage>\r\n";
                //$hot_xml.=" <hotmusic id=\"1\">\r\n         <name>热区</name>\r\n         <link>null</link>\r\n           <pX>1</pX>\r\n          <pY>1</pY>\r\n          <width>1</width>\r\n            <height>1</height>\r\n          <play>00:13.890</play>\r\n          <stop>00:17.890</stop>\r\n      </hotmusic>\r\n";
                $hot_xml.="    </rightpage>\r\n";
			}else {
                //左半页
                $hot_xml.="    <leftpage>\r\n";
				foreach ($data_hot as $key=>$v){
					//$hot_xml.="		<hotmusic id=\"".$key."\">\r\n			<name>热区</name>\r\n			<link>".basename($v['urlname'])."</link>\r\n			<pX>".$v['x']."</pX>\r\n			<pY>".$v['y']."</pY>\r\n			<width>".$v['w']."</width>\r\n			<height>".$v['h']."</height>\r\n			<play>".$v['vbeg']."</play>\r\n			<stop>".$v['vend']."</stop>\r\n		</hotmusic>\r\n";

                    $hot_xml.="     <hotmusic id=\"".$key."\">\r\n          <name>热区</name>\r\n          <link>".basename($v['hotmp3'])."</link>\r\n          <plist>".$v['plist']."</plist>\r\n          <play>00:00:000</play>\r\n          <stop>00:00:000</stop>\r\n     </hotmusic>\r\n";
					//array_push($arr_hot, $v['urlname']);
					$dir->copyFile('Uploads/audio/'.$v['hotmp3'], $todayDir.basename($v['hotmp3']),true);
				}
                $hot_xml.="    </leftpage>\r\n";

                //右半页
                $hot_xml.="    <rightpage>\r\n";
                foreach ($data_hot1 as $key=>$v){
                    //$hot_xml.="       <hotmusic id=\"".$key."\">\r\n          <name>热区</name>\r\n         <link>".basename($v['urlname'])."</link>\r\n            <pX>".$v['x']."</pX>\r\n            <pY>".$v['y']."</pY>\r\n            <width>".$v['w']."</width>\r\n          <height>".$v['h']."</height>\r\n            <play>".$v['vbeg']."</play>\r\n         <stop>".$v['vend']."</stop>\r\n     </hotmusic>\r\n";

                    $hot_xml.="     <hotmusic id=\"".$key."\">\r\n          <name>热区</name>\r\n          <link>".basename($v['hotmp3'])."</link>\r\n          <plist>".$v['plist']."</plist>\r\n          <play>00:00:000</play>\r\n          <stop>00:00:000</stop>\r\n     </hotmusic>\r\n";
                    //array_push($arr_hot, $v['urlname']);
                    $dir->copyFile('Uploads/audio/'.$v['hotmp3'], $todayDir.basename($v['hotmp3']),true);
                }
                $hot_xml.="    </rightpage>\r\n";


			}
			$hot_xml.="	</hotpage>\r\n";
		}

// 		$arr_uni=array_unique($arr_hot);//去重
// 		$arr_uni=array_values($arr_uni);//重新排序
// 		for ($i=0;$i<count($arr_uni);$i++){
// 			$dir->copyFile('Uploads/audio'.$arr_uni[$i], $todayDir.md5(uniqid(microtime(true),true)).'.mp3',true);
// 		}

		//复制图片
		$pics_xml='';
        $pics_xml .= "<content name=\"书页内容\" id=\"1\" width=\"1110\" height=\"1570\">\r\n";
        $picWidthHeight = '';
        $picWidthHeight .= "<pic>\r\n";
        foreach ($data_page as $key=>$v){
            //$file=iconv('utf-8', 'gbk', $v['pagefile']);
            //echo $v['pagefile'];

            $dir->copyFile($v['pagefile'], $todayDir.basename($v['pagefile']),true);
            if($key==0||$key==1){
                $image = new \Think\Image();
                $image->open($todayDir.basename($v['pagefile']));
                $image->thumb(300, 800)->save($todayDir.'thumb'.$key.'.jpg');
            }

            $pics_xml.="            <page>".basename($v['pagefile'])."</page>\r\n";

            $picInfo = getimagesize($v['pagefile']);
            $w_d = $picInfo[0];
            $h_d = $picInfo[1];
            $picWidthHeight.="            <width>".$w_d."</width>\r\n";
            $picWidthHeight.="            <height>".$h_d."</height>\r\n";
        }
        $pics_xml .= "</content>\r\n";
        $picWidthHeight .= "</pic>\r\n";
		// //复制音频
		// $audio_xml='';
		// foreach ($data_audio as $key=>$v){
		// 	$musicname=iconv('utf-8', 'gbk', $v['musicname']);
		// 	$dir->copyFile('Uploads/audio/'.$v['musicfile'], $todayDir.$musicname,true);
		// 	$audio_xml.="	<bookmusic name=\"课文声音\" id=\"".($key+1)."\">\r\n		<name>".substr($v['musicname'],0,strrpos($v['musicname'], '.'))."</name>\r\n		<link>".$v['musicname']."</link>\r\n		<icon>mp3</icon>\r\n	</bookmusic>\r\n";
		// }
		// $xml = str_replace('$$audio$$', $audio_xml, $xml);
		$xml = str_replace('$$pics$$', $pics_xml, $xml);
		$xml = str_replace('$$hotmusic$$', $hot_xml, $xml);
		$dir->writeFile($xmlFile, $xml);


	}

    /**
     * 在线生成xml文件
     */
    public function makexml($chapterid){


        $Model_book=M('','','DB_CONFIG');
        $sql = "select l.bookid,l.pageflag,t.pagebeg,t.pageend from t_book l,t_book_chapter t where l.bookid=t.bookid and t.chapterid='".$chapterid."'";
        $data = $Model_book->query($sql);


        vendor('fileDirUtil');
        $dir = new \fileDirUtil();

        $bookid  = $data[0]['bookid'];
        $pagebeg = $data[0]['pagebeg'];
        $pageend = $data[0]['pageend'];
		$pageflag= $data[0]['pageflag'];

        $todayDir='download/'.$bookid.'/'.$chapterid.'/';


        $xmlFile=$todayDir.'data.xml';

        if(file_exists($xmlFile)){
            unlink($xmlFile);
        }

        //复制data.xml
        $dir->copyFile('Uploads/template/data.xml', $xmlFile,true);

        $xml = $dir->readsFile($xmlFile);

        $Model=M('','','DB_CONFIG');
        if ($pageflag==1){
            //奇数在左
            if ($pagebeg%2==0){
                //如果是偶数，从上一页开始
                $pagebeg=$pagebeg-1;
            }else{
                //如果是奇数，从当前页开始
                $pagebeg==$pagebeg;
            }
            if ($pageend%2==0){
                //如果是偶数
                $pageend==$pageend;
            }else{
                //如果是奇数
                $pageend=$pageend+1;
            }
        }else {
            //偶数在左
            if ($pagebeg%2==0){
                //偶数
                $pagebeg==$pagebeg;
            }else {
                //奇数
                $pagebeg=$pagebeg-1;
            }
            if ($pageend%2==0){
                //偶数
                $pageend=$pageend+1;
            }else {
                //奇数
                $pageend==$pageend;
            }
        }
        $Model_page=M('','','DB_CONFIG')->table('t_book_page');


        //查询图片
        $sql_page="SELECT pagefile FROM t_book_page WHERE bookid='".$bookid."' AND pagenum>=".$pagebeg." AND pagenum<=".$pageend." order by pagenum";
        $data_page=$Model->query($sql_page);

        //查询音频
        // $sql_audio="SELECT musicname,musicfile FROM t_book_chapter_mp3 WHERE bookid='".$bookid."' AND chapterid='".$chapterid."' order by id";
        // $data_audio=$Model->query($sql_audio);

        $hotnum=ceil(count($data_page)/2);
        //热点数据
        $hot_xml='';
        //$arr_hot=array();
        for ($i=$pagebeg;$i<=($pageend-1);$i=$i+2){
            $sql_hot="SELECT * FROM t_book_page_hot WHERE bookid='".$bookid."' AND isdel=0 AND isbig=0 AND iscut=1 AND pagenum='".$i."' order by id";
            $sql_hot1="SELECT * FROM t_book_page_hot WHERE bookid='".$bookid."' AND isdel=0 AND isbig=0 AND iscut=1 AND pagenum='".($i+1)."' order by id";


            $data_hot=$Model->query($sql_hot);
            $data_hot1=$Model->query($sql_hot1);

            $hot_xml.=" <hotpage name=\"第一整页热点声音\" id=\"".$i."\">\r\n";
            if (empty($data_hot)&&empty($data_hot1)){

                $hot_xml.="    <leftpage>\r\n";

                $hot_xml.="    </leftpage>\r\n";

                $hot_xml.="    <rightpage>\r\n";

                $hot_xml.="    </rightpage>\r\n";
            }else {
                //左半页
                $hot_xml.="    <leftpage>\r\n";
                foreach ($data_hot as $key=>$v){

                    //老版
                    //$hot_xml.="     <hotmusic id=\"".$key."\">\r\n          <name>热区</name>\r\n          <link>../../../Uploads/audio".$v['urlname']."</link>\r\n          <plist>".$v['plist']."</plist>\r\n          <play>".$v['vbeg']."</play>\r\n          <stop>".$v['vend']."</stop>\r\n     </hotmusic>\r\n";

                    // 新版
                    $hot_xml.="     <hotmusic id=\"".$key."\">\r\n          <name>热区</name>\r\n          <bid>".$v['id']."</bid>\r\n          <link>../../../Uploads/audio/".$v['hotmp3']."</link>\r\n          <plist>".$v['plist']."</plist>\r\n          <play>00:00:000</play>\r\n          <stop>00:00:000</stop>\r\n     </hotmusic>\r\n";

                    //$dir->copyFile('Uploads/audio'.$v['urlname'], $todayDir.basename($v['urlname']),true);
                }
                $hot_xml.="    </leftpage>\r\n";

                //右半页
                $hot_xml.="    <rightpage>\r\n";
                foreach ($data_hot1 as $key=>$v){

                    //老版
                    //$hot_xml.="     <hotmusic id=\"".$key."\">\r\n          <name>热区</name>\r\n          <link>../../../Uploads/audio/".$v['urlname']."</link>\r\n          <plist>".$v['plist']."</plist>\r\n          <play>".$v['vbeg']."</play>\r\n          <stop>".$v['vend']."</stop>\r\n     </hotmusic>\r\n";
                    //新版
                    $hot_xml.="     <hotmusic id=\"".$key."\">\r\n          <name>热区</name>\r\n          <bid>".$v['id']."</bid>\r\n          <link>../../../Uploads/audio/".$v['hotmp3']."</link>\r\n          <plist>".$v['plist']."</plist>\r\n          <play>00:00:000</play>\r\n          <stop>00:00:000</stop>\r\n     </hotmusic>\r\n";

                    //$dir->copyFile('Uploads/audio'.$v['urlname'], $todayDir.basename($v['urlname']),true);
                }
                $hot_xml.="    </rightpage>\r\n";


            }
            $hot_xml.=" </hotpage>\r\n";
        }



        //复制图片
        $pics_xml='';
        $pics_xml .= "<content name=\"书页内容\" id=\"1\" width=\"1110\" height=\"1570\">\r\n";
        foreach ($data_page as $v){

            //$dir->copyFile($v['pagefile'], $todayDir.basename($v['pagefile']),true);
            $pics_xml.="            <page>../../../".$v['pagefile']."</page>\r\n";
        }
        $pics_xml .= "</content>\r\n";
        //复制音频
        // $audio_xml='';
        // foreach ($data_audio as $key=>$v){
        //     $musicname=iconv('utf-8', 'gbk', $v['musicname']);
        //     //$dir->copyFile('Uploads/audio/'.$v['musicfile'], $todayDir.$musicname,true);
        //     $audio_xml.="   <bookmusic name=\"课文声音\" id=\"".($key+1)."\">\r\n       <name>".substr($v['musicname'],0,strrpos($v['musicname'], '.'))."</name>\r\n        <link>../../../Uploads/audio/".$v['musicfile']."</link>\r\n        <icon>mp3</icon>\r\n    </bookmusic>\r\n";
        // }
        // $xml = str_replace('$$audio$$', $audio_xml, $xml);
        $xml = str_replace('$$pics$$', $pics_xml, $xml);
        $xml = str_replace('$$hotmusic$$', $hot_xml, $xml);
        $dir->writeFile($xmlFile, $xml);

        return $xmlFile;

    }

    public function online(){
        $chapterid = I('chapterid/s');
        $xmltxt = $this->makexml($chapterid);

        // echo $xmltxt."111<br/>";
        // echo $xmltxt."111<br/>";

        $xmltxt = 'http://192.168.151.126:8051/'.$xmltxt;

        // echo $xmltxt;exit;
        $url = "mainXmlUrl=".urlencode($xmltxt)."&show=1&preferenceUrl=";
        $this->assign('url',$url);
        $this->display();
    }




    /**
     * 生成excel（入库单）
     */
    public function ExcelForCCM($bookid,$chapterArr,$bookname,$isEn){
    	vendor('PHPExcel');
    	$todayDir='download/'.$bookname.'/';
    	$Model=M('','','DB_CONFIG1');
    	$sql_where='';
    	if (count($chapterArr)==1){
    		//导出本章节
    		$chapterid=$chapterArr[0];
    		$sql="SELECT REPLACE(SUBSTRING_INDEX(C1,'.',5),'.','/') AS 'mulu',ks_name FROM SHARE_KNOWLEDGE_STRUCTURE WHERE KS_CODE='".$chapterid."' order by DISPLAY_ORDER";
    	}else {
    		//导出多个章节
    		for ($i=0;$i<count($chapterArr);$i++){
    			$sql_where.=" KS_CODE='".$chapterArr[$i]."' OR";
    		}
    		$sql="SELECT REPLACE(SUBSTRING_INDEX(C1,'.',5),'.','/') AS 'mulu',ks_name FROM SHARE_KNOWLEDGE_STRUCTURE WHERE ".trim($sql_where,'OR')." order by DISPLAY_ORDER";
    	}
//     	if ($chapterid==''){
//     		//导出整本书
//     		$sql="SELECT REPLACE(SUBSTRING_INDEX(C1,'.',5),'.','/') AS 'mulu',ks_name FROM SHARE_KNOWLEDGE_STRUCTURE WHERE C2=1 AND KS_CODE like '".$bookid."%'";
//     	}else {
//     		//导出本章节
//     		$sql="SELECT REPLACE(SUBSTRING_INDEX(C1,'.',5),'.','/') AS 'mulu',ks_name FROM SHARE_KNOWLEDGE_STRUCTURE WHERE KS_CODE='".$chapterid."'";
//     	}
    	$data=$Model->query($sql);
    	//var_dump($data);
    	$objExcel = new \PHPExcel();
    	//$objWriter = new \PHPExcel_Writer_Excel2007($objExcel);
        $objWriter = new \PHPExcel_Writer_Excel5($objExcel);

    	$objExcel->setActiveSheetIndex(0);
    	$objActSheet = $objExcel->getActiveSheet();
    	$objActSheet->setTitle('资源策划单');
        /*
    	$objActSheet->setCellValue('A1','上层目录');
    	$objActSheet->setCellValue('B1','课程（节）');
    	$objActSheet->setCellValue('C1','主题ID');
    	$objActSheet->setCellValue('D1','序号');
    	$objActSheet->setCellValue('E1','标题');
    	$objActSheet->setCellValue('F1','制作要求');
    	$objActSheet->setCellValue('G1','处理类型');
    	$objActSheet->setCellValue('H1','资源类型');
    	$objActSheet->setCellValue('I1','资源格式');
    	$objActSheet->setCellValue('J1','课堂应用');
        $objActSheet->setCellValue('K1','链接类型');
        $objActSheet->setCellValue('L1','链接地址');
    	$objActSheet->setCellValue('M1','导学类型');
    	$objActSheet->setCellValue('N1','学习目标');
    	$objActSheet->setCellValue('O1','导学应用');
    	$objActSheet->setCellValue('P1','学习引导');
    	$objActSheet->setCellValue('Q1','描述');
    	$objActSheet->setCellValue('R1','资源供应商');
    	$objActSheet->setCellValue('S1','上传者');
    	$objActSheet->setCellValue('T1','海报');
    	$objActSheet->setCellValue('U1','资源编码');
    	$objActSheet->setCellValue('V1','使用对象');
    	$objActSheet->setCellValue('W1','资源推介');
    	$objActSheet->setCellValue('X1','关键字');
    	$objActSheet->setCellValue('Y1','备注');
        */

		$objActSheet->setCellValue('A1','所属课时');
        $objActSheet->setCellValue('B1','上层目录');
        $objActSheet->setCellValue('C1','课程（节）');
        $objActSheet->setCellValue('D1','序号');
        $objActSheet->setCellValue('E1','标题');
        $objActSheet->setCellValue('F1','制作要求');
        $objActSheet->setCellValue('G1','处理类型');
        $objActSheet->setCellValue('H1','资源类型');
        $objActSheet->setCellValue('I1','资源格式');
        $objActSheet->setCellValue('J1','课堂应用');
        $objActSheet->setCellValue('K1','导学案');
        $objActSheet->setCellValue('L1','拓展提升');
        $objActSheet->setCellValue('M1','课件');
        $objActSheet->setCellValue('N1','教学设计');
        $objActSheet->setCellValue('O1','链接类型');
        $objActSheet->setCellValue('P1','链接地址');
        $objActSheet->setCellValue('Q1','描述');
        $objActSheet->setCellValue('R1','资源供应商');
        $objActSheet->setCellValue('S1','上传者');
        $objActSheet->setCellValue('T1','资源编码');
        $objActSheet->setCellValue('U1','资源推介');
        $objActSheet->setCellValue('V1','关键字');
        $objActSheet->setCellValue('W1','备注');
		$objActSheet->setCellValue('X1','主题ID');
        $objActSheet->setCellValue('Y1','海报');
        $objActSheet->setCellValue('Z1','名师');

    	//手动设置前4列宽度
    	$objActSheet->getColumnDimension('B')->setWidth(35);
    	$objActSheet->getColumnDimension('C')->setWidth(25);

    	foreach ($data as $key=>$v){
    		//$objActSheet->setCellValueExplicit('A'.($key+2), $v['mulu'], \PHPExcel_Cell_DataType::TYPE_STRING);
    		$objActSheet->setCellValue('B'.($key+2),$v['mulu']);
    		$objActSheet->setCellValue('C'.($key+2),$v['ks_name']);
    		$objActSheet->setCellValue('D'.($key+2),'1');
    		if ($isEn==true){
    			$objActSheet->setCellValue('E'.($key+2),'校本资源：'.rtrim($v['ks_name']).'电子教材');
    		}else {
    			$objActSheet->setCellValue('E'.($key+2),'校本资源：《'.rtrim($v['ks_name']).'》电子教材');
    		}

    		$objActSheet->setCellValue('G'.($key+2),'新增');
    		$objActSheet->setCellValue('H'.($key+2),'电子教材');
    		$objActSheet->setCellValue('I'.($key+2),'动画');
    		$objActSheet->setCellValue('R'.($key+2),'郑州教研中心');
    		$objActSheet->setCellValue('S'.($key+2),'虚拟');
    	}

        //$filename = "resinfo.xlsx";
        // $filename = "resinfo.xls";
    	$filename = $bookname.".xls";
    	$objWriter->save($todayDir.$filename);  //保存到服务器


//     	ob_end_clean();//清除缓冲区,避免乱码
//     	$filename=iconv('utf-8', 'gbk', $filename);
//     	$filesize = filesize($filename);
//     	header( "Content-Type: application/force-download;charset=utf-8");
//     	header( "Content-Disposition: attachment; filename= ".$filename);
//     	header( "Content-Length: ".$filesize);
//     	ob_clean();
//     	readfile($filename);

//     	unlink($filename); //下载完成后删除服务器文件

    }



    /**
     * 生成excel（从中心库读数据）
     */
    public function ExcelFromCCM($bookid,$chapterArr,$bookname,$isEn){
        vendor('PHPExcel');
        $todayDir='download/'.$bookname.'/';
        if(!is_dir($todayDir)){
            mkdir($todayDir,0777);
        }
        $Model=M('',null,'mysql://vcom:2012rmsedu@218.28.20.154/RMS_Data');
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


        $sql ="SELECT IF(t.C1 IS NULL,'',IF (t.KS_CODE LIKE '00010c%',REPLACE (SUBSTRING_INDEX(t.C1, '.', 6),'.','/'),REPLACE (SUBSTRING_INDEX(t.C1, '.', 5),'.','/'))) AS '上层目录',IF(t.KS_NAME IS NULL,'',t.KS_NAME) as '课程（节）',IF(l.WEEK_SUBJECT IS NULL,'',l.WEEK_SUBJECT) as '主题ID',IF(l.R_EXT5 IS NULL,'',l.R_EXT5) as '序号',IF(l.R_TITLE IS NULL,'',l.R_TITLE) as '标题', '' as'制作要求','' as '处理类型',(select max(p2.rt_name) from RMS_RES_PARAM p2 where l.R_TYPECODE = p2.RT_CODE) as '资源类型',IF(m.RF_NAME IS NULL,'',m.RF_NAME) as '资源格式',IF(s.DETAIL_NAME IS NULL,'',s.DETAIL_NAME) as '课堂应用',IF(l.c3='1','是','否') as '导学案',IF (l.R_SFLAG = '1','是','否') as '拓展提升',(SELECT c.NAME FROM COMMON_TYPE c WHERE  l.LESSION_TYPE = c.ID) AS '课件',IF (l.R_AUTHOR = '','',l.R_AUTHOR) AS '名师',(SELECT c.NAME FROM COMMON_TYPE c WHERE  l.COURSE_TYPE = c.ID) AS '教学设计',IF (l.LINK_TYPE = '0','',IF(l.LINK_TYPE = '1','静态链接',IF(l.LINK_TYPE = '2','仿真实验',IF(l.LINK_TYPE = '3','英语同步练',IF(l.LINK_TYPE = '4','中心库资源',''))))) AS '链接类型',IF (l.LINK_VALUE IS NULL,'',IF(l.LINK_VALUE = 'experiment/diy/1','初中物理实验DIY',IF ( l.LINK_VALUE = 'experiment/diy/2','高中物理实验DIY',IF (l.LINK_VALUE LIKE '%run%',(SELECT NAME FROM win_fzsy WHERE id = SUBSTRING_INDEX(l.LINK_VALUE, '/' ,- 1)),l.LINK_VALUE)))) AS '链接地址',IF(l.R_DESC IS NULL,'',trim(REPLACE(l.R_DESC,'\r\n','<br>'))) as '描述',IF(l.R_PUBLISHER IS NULL,'',l.R_PUBLISHER) as '资源供应商',IF(l.R_EXT3 IS NULL,'',l.R_EXT3) as '上传者','' as '海报',l.R_CODE as '资源编码',IF(l.R_USED_OBJ='OBJ001','教师备课','教师备课授课') as '使用对象',IF(l.R_EXT6='ZYNEW     ','NEW',IF(l.R_EXT6='ZYHOT','HOT',IF(l.R_EXT6='ZYTJ','推荐',''))) as '资源推介',IF(l.R_KEYWORDS IS NULL,'',trim(REPLACE(l.R_KEYWORDS,'\r\n','<br>'))) as '关键字','' as '备注'  FROM ((RMS_RESOURCEINFO l INNER JOIN SHARE_KNOWLEDGE_STRUCTURE t ON l.R_KS_ID=t.KS_CODE AND  ".$sql_where." AND l.R_STATE='1' AND l.R_TYPECODE = 'RT106' AND t.FLAG='1' AND (l.R_HASHCODE='' OR l.R_HASHCODE is NULL) ) INNER JOIN RMS_RESOURCEFORMAT m ON l.R_FORMAT=m.RF_CODE)  LEFT JOIN RMS_DICTIONARY_DETAIL s ON l.c1=s.DETAIL_CODE order by l.R_KS_ID,l.R_EXT5;";
        // echo $sql;exit;
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
                if($arr[$i]=='v'||$arr[$i]=='C'){
                    //资源编码列，不让为科学计数法
                    //echo $arr[$i].($key+2).'||'.$v[$keyArray[$i]];exit;
                    $objActSheet->setCellValueExplicit($arr[$i].($key+2), $v[$keyArray[$i]],\PHPExcel_Cell_DataType::TYPE_STRING);
                }else if($arr[$i]=='G'){
                    $objActSheet->setCellValue($arr[$i].($key+2),'文件替换');
                }else{
                    $objActSheet->setCellValue($arr[$i].($key+2),$v[$keyArray[$i]]);
                }
            }

        }



        $filename .= $bookname.".xls";
        $objWriter->save($todayDir.$filename);

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


		$ua = $_SERVER["HTTP_USER_AGENT"];
		$encoded_filename = urlencode($showname);
		$encoded_filename = str_replace("+", "%20", $encoded_filename);
		header('Content-Type: application/octet-stream');
		if (preg_match("/MSIE/", $ua)) {
		  header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
		} else if (preg_match("/Firefox/", $ua)) {
		  header('Content-Disposition: attachment; filename*="utf8\'\'' . $showname . '"');
		} else {
		  header('Content-Disposition: attachment; filename="' . $showname . '"');
		}


    	//发送Http Header信息 开始下载
    	header("content-type:text/html; charset=utf-8");
    	header("Pragma: public");
    	header("Cache-control: max-age=".$expire);
    	//header('Cache-Control: no-store, no-cache, must-revalidate');
    	header("Expires: " . gmdate("D, d M Y H:i:s",time()+$expire) . "GMT");
    	header("Last-Modified: " . gmdate("D, d M Y H:i:s",time()) . "GMT");
    	//下面一行就是改动的地方，即用iconv("UTF-8","GB2312//TRANSLIT",$showname)系统函数转换编码为gb2312 
		//不管用了
    	//header("Content-Disposition: attachment; filename=". iconv("UTF-8","gb2312",$showname));
    	header("Content-Length: ".$length);
    	//header("Content-type: ".$type);
    	header('Content-Encoding: none');
    	header("Content-Transfer-Encoding: binary" );
    	ob_clean();

    	readfile($filename);
    	//exit();
    }


    /**
     * [makeResByCode description]
     * [批量生成电子教材资源清单]
     * @return [type] [description]
     */
    public function makeResByCode(){
        set_time_limit(0);
        vendor('fileDirUtil');
        $dir = new \fileDirUtil();
        //$bookid=I('bookid/s','');
        //$chapterid=I('chapterid/s','');
        //$str=I('str/s');
        //$chapterArr=explode('|', $str);//章节id数组


        $Model_book=M('','','DB_CONFIG')->table('t_test');
        $sql = "select l.id,t.bookid,t.bookname,t.pageflag from t_test l,t_book t WHERE l.bookid=t.bookid and l.id<>1015 AND (t.bookname like '%语文%' OR t.bookname like '%英语%' OR t.bookname  like '%音乐%')";

        $re_book = $Model_book->query($sql);

        // var_dump($re_book);exit;

        // $id = I('bookid/s','');
        // if($id==''){
        //     exit('bookid is null！');
        // }

        // $re_book[0]['bookid']=$id;
        foreach($re_book as $v){

            $bookid= $v['bookid'];

            $Model=M('','','DB_CONFIG');
            $sql_chapter = "SELECT chapterid FROM t_book_chapter WHERE bookid='".$v['bookid']."';";
            //echo $sql_chapter;exit;
            $re_chapter = $Model->query($sql_chapter);

            $chapterArr = array_column($re_chapter,'chapterid');
            //var_dump($chapterArr);
            $bookname=$v['bookname'];
            $count= explode('英语',$bookname);
            $isEn=false;
            if (count($count)>1){
                $isEn=true;
            }
            $pageflag=$v['pageflag'];
            $bookname=iconv('utf-8', 'gbk', $bookname);
            if ($isEn==true){
                $title='电子教材';
            }else {
                $title='》电子教材';
            }

            $title=iconv('utf-8', 'gbk', $title);

            for ($i=0;$i<count($chapterArr);$i++){
                $chapterid=$chapterArr[$i];
                //echo $chapterid.'<br/>';
                $this->copyRes1($bookid, $bookname, $pageflag, $chapterid, $title,$isEn);
            }

            $this->ExcelFromCCM($bookid,$chapterArr,$bookname,$isEn);

            $data['flag']=1;
            $Model_book=M('','','DB_CONFIG')->table('t_test');
            $Model_book->where('id="%d"',$v['id'])->save($data);
            //exit;//一本书结束
        }//循环结束


    }


    /**
     * 获取截取音频的翻译
     * @DateTime 2017-02-14T09:53:32+0800
     * @return   [type]                   [description]
     */
    public function getFanyi(){
        set_time_limit(0);
        vendor('fileDirUtil');
        $dir = new \fileDirUtil();
        $m_book = M('','','DB_CONFIG')->table('t_book_tmp');


        $data_book = $m_book->select();

        $baseDir = 'download/fanyi/';
        // echo $m_book->getLastSql();exit;
        // var_dump($data_book);exit;

        foreach($data_book as $v){
            $bookname = $v['name'];
            $bookname_gbk = iconv('utf-8','gbk',$bookname);
            $bookid = $v['bookid'];
            if(!is_dir($bookname_gbk)){
                $dir->createDir($baseDir.$bookname_gbk);
            }

            $m_hot =  M('','','DB_CONFIG')->table('t_book_page_hot_tmp');
            $data_hot = $m_hot->where('bookid="%s"',$bookid)->field('pagenum,hotmp3,fanyi')->order('pagenum')->select();

            foreach($data_hot as $vv){
                $mp3 = $vv['hotmp3'];
                $fanyi = $vv['fanyi'];
                $pagenum = $vv['pagenum'];
                $basemp3 = basename($mp3);

                $dir->copyFile('Uploads/audio/tmp/'.$mp3,$baseDir.$bookname_gbk.'/'.$basemp3);

                $content = $pagenum."     ".$basemp3."     ".$fanyi;
                file_put_contents($baseDir.$bookname_gbk.'/'.'1.txt', $content.PHP_EOL, FILE_APPEND);
                // exit;
            }
            echo $bookname.'complate!<br/>';
            ob_flush();
            flush();
        }
    }


    /**
     * 遍历所有大文件（音频）
     * @DateTime 2017-02-23T09:17:53+0800
     * @return   [type]                   [description]
     */
    public function delBigFile(){
        set_time_limit(0);
        $m =  M('','','DB_CONFIG');
        $sql = "SELECT l.id,l.bookid,l.pagenum,l.hotmp3,t.bookname FROM t_book_page_hot l LEFT JOIN t_book t on l.bookid=t.bookid WHERE l.isdel=0 AND l.iscut=1 AND l.isbig=0 ORDER BY l.bookid,l.pagenum";

        $data = $m->query($sql);
        // var_dump($data);exit;
        $count_1024 = 0 ;
        $count_500 = 0 ;
        $total = count($data);

        foreach($data as $key=>$v){
            // echo $key."<br/>";
            $pagenum = $v['pagenum'];
            $hotmp3 = "Uploads/audio/".$v['hotmp3'];
            $id = $v['id'];
            $bookname = $v['bookname'];
            $bookid = $v['bookid'];

            //查询当前页是否需要
            $sql_c = 'SELECT min(pagebeg) as pagebeg,max(pageend) as pageend from t_book_chapter WHERE bookid="'.$bookid.'"';
            $data_c = $m->query($sql_c);
            if($pagenum<$data_c[0]['pagebeg']||$pagenum>$data_c[0]['pageend']){
                //此页没用
                echo 'not use'.$bookid."|".$pagenum."|".$hotmp3;
                continue;
            }

            if(file_exists($hotmp3)){
                $byte = filesize($hotmp3);
                $size = $byte/1024;
                if($size>500&&$size<1024){
                    //文件过大 isdel = 2
                    // $hot['isdel'] = 2;
                    $count_500++;

                    $sql_chaptername = 'SELECT chaptername FROM t_book_chapter WHERE bookid="'.$bookid.'" AND pagebeg<='.$pagenum.' AND pageend>='.$pagenum;
                    // echo $sql_chaptername;
                    $data_chapter = $m->query($sql_chaptername);
                    $chaptername = $data_chapter[0]['chaptername'];
                    // var_dump($data_chapter);exit;
                    $str = $id."|".$bookname."|".$chaptername."|".$pagenum."|".basename($hotmp3);
                    file_put_contents("big.txt", $str.PHP_EOL, FILE_APPEND);
                }else if($size>1024){
                    //文件过大

                    $sql_u = "update t_book_page_hot set isdel=1 and isbig=1 where id=".$id;
                    $m->execute($sql_u);
                    $count_1024++;
                }else{
                    continue;
                }
            }else{
                //文件不存在 idel=4
                $sql_u = "update t_book_page_hot set isdel=1  where id=".$id;
                $m->execute($sql_u);

            }

        }
        echo '>1024-->total:'.$count_1024.'<br/>';
        echo '>500-->total:'.$count_500.'<br/>';
        echo 'zonggong:'.$total;
    }


    /**
     * 逻辑删除比较大的接口 isbig=1
     * @DateTime 2017-02-24T10:11:43+0800
     * @return   [type]                   [description]
     */
    public function delBigHot(){
        $bid = I('bid/d',0);
        if($bid==0){
            exit('先选中热区再删除');
        }
        $m=M('','','DB_CONFIG')->table('t_book_page_hot');
        $m->where('id="%d"',$bid)->setField('isbig',1);
        // $data['msg'] = 'ok';
        // $data['errcode'] = 1;
        // $this->ajaxReturn($data,'JSON');
        echo '删除成功！';
    }


    public function delpagehotbychapter(){
        $chapterid = I('chapterid/s','');
        $n=M('','','DB_CONFIG')->table('t_book_chapter');

        $data = $n->where('chapterid="%s"',$chapterid)->find();

        M('','','DB_CONFIG')->table('t_book_chapter')->where('chapterid="%s"',$chapterid)->delete();

        $pagebeg = $data['pagebeg'];
        $pageend = $data['pageend'];

        $a['pagenum'] = array('egt',$pagebeg);
        $b['pagenum'] = array('elt',$pageend);
        $m=M('','','DB_CONFIG')->table('t_book_page_hot');
        $m->where('bookid="%s"',$data['bookid'])->where($a)->where($b)->setField('isdel',1);


    }


    public function uploadpic(){
        $bookid = I('bookid/s','');

        $m=M('','','DB_CONFIG')->table('t_book_page');

        $data = $m->where('bookid="%s"',$bookid)->find();
        $basename = basename($data['pagefile']);
        $path = str_replace($basename,'',$data['pagefile']);

        $this->assign('path',$path);
        $this->assign('bookid',$bookid);
        $this->display();
    }


	public function delBook_hot(){
		$bookid = I('id/s','');
		$m=M('','','DB_CONFIG')->table('t_book_page_hot');
		$m->where('bookid="%s"',$bookid)->setField('isdel',1);
	}



}