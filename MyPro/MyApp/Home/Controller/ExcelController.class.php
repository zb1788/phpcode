<?php
namespace Home\Controller;
use Think\Controller;

/**
* 文件上传
*
* @author         gm
* @since          1.0
*/
class ExcelController extends Controller {
    public function import(){
    	$this->display();
	}
	public function importData(){
		$filepath=I('filepath');
		$this->importExcelData($filepath);
	}
	/**
	 * 读取excel
	 */
	public function importExcelData($path){
		vendor('PHPExcel');
		$arr_excel = readExcel($path);

		$insert_flag = true; //是否写数据库标志
		$errorinfo = array(); //错误信息数组
		$success_num = 0; //导入成功数

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
			/**
			 * 写数据库
			$Model_book_c=M('','','DB_CONFIG')->table('t_book_chapter');
			$data['bookid']=$bookid;
			$data['chapterid']=$chapterid;
			$data['chaptername']=$chaptername;
			$data['pagebeg']=(int)$pagebeg;
			$data['pageend']=(int)$pageend;
			$data_c=$Model_book_c->where('bookid="%s" and chapterid="%s"',$bookid,$chapterid)->find();
			$Model_book_chapter=M('','','DB_CONFIG')->table('t_book_chapter');
			if (empty($data_c)){
				$Model_book_chapter->add($data);
			}else {
				$Model_book_chapter->where('bookid="%s" and chapterid="%s"',$bookid,$chapterid)->save($data);
			}
			*/

			$success_num = $success_num + 1;
		}

		    	if (count($errorinfo) > 0) {
		    		echo "一共导入了" . $success_num . "条记录<br>";
		    		echo "以下数据没有导入<br>";
		    		for ($i = 0; $i < count($errorinfo); $i++) {
		    			$temp_info = explode("|", $errorinfo[$i]);
		    			echo "第" . $temp_info[1] . "行错误信息：" . $temp_info[0] . "<br>";
		    		}
		    	} else {
		    		echo "数据导入成功<br>";
		    		echo "一共导入了" . $success_num . "条记录<br>";
		    	}

	}



	/**
	 * 下载excel
	 */
	public function makeExcel(){
		$id=I('id/s');
		vendor('PHPExcel');

		$Model=M('','','DB_CONFIG1');
		$sql="SELECT KS_CODE,KS_NAME FROM SHARE_KNOWLEDGE_STRUCTURE WHERE is_unit=0 and flag>0 and ks_type=0 AND KS_CODE LIKE '".$id."%' AND P_ID='".$id."' ORDER BY DISPLAY_ORDER";
		//$data=$Model->query($sql);

        $data = array(
                array('ks_code'=>'111','ks_name'=>'unit1'),
                array('ks_code'=>'222','ks_name'=>'unit2'),
                array('ks_code'=>'333','ks_name'=>'unit3'),
            );
		//var_dump($data);
        //创建一个excel
		$objExcel = new \PHPExcel();
        //保存excel-2007格式
		$objWriter = new \PHPExcel_Writer_Excel2007($objExcel);
        //或者$objWriter = new PHPExcel_Writer_Excel5($objExcel); 非2007格式

		$objExcel->setActiveSheetIndex(0);
		$objActSheet = $objExcel->getActiveSheet();
		$objActSheet->setTitle('目录表');

		$objActSheet->setCellValue('A1','ID');
		$objActSheet->setCellValue('B1','章节名称');
		$objActSheet->setCellValue('C1','起始页码');
		$objActSheet->setCellValue('D1','结束页码');

        //合并单元格
        $objActSheet->mergeCells('A18:E22');
        //分离单元格
        $objActSheet->unmergeCells('A28:B28');

        //保护cell
        $objActSheet->getProtection()->setSheet(true); // Needs to be set to true in order to enable any worksheet protection!
        $objActSheet->protectCells('A3:E13', 'PHPExcel');

        //设置font
        $objActSheet->getStyle('A1')->getFont()->setName('Candara');
        $objActSheet->getStyle('A1')->getFont()->setSize(20);
        $objActSheet->getStyle('A1')->getFont()->setBold(true);
        $objActSheet->getStyle('A1')->getFont()->setUnderline(\PHPExcel_Style_Font::UNDERLINE_SINGLE);
        $objActSheet->getStyle('A1')->getFont()->getColor()->setARGB(\PHPExcel_Style_Color::COLOR_WHITE);

        //设置border
        $objActSheet->getStyle('B2')->getBorders()->getTop()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
        $objActSheet->getStyle('B2')->getBorders()->getLeft()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
        $objActSheet->getStyle('B2')->getBorders()->getBottom()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
        $objActSheet->getStyle('B2')->getBorders()->getTop()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);


        //设置四个边框颜色
        $objActSheet->getStyle('B2')->getBorders()->getLeft()->getColor()->setARGB('FF993300');
        $objActSheet->getStyle('B2')->getBorders()->getRight()->getColor()->setARGB('FF993300');
        $objActSheet->getStyle('B2')->getBorders()->getBottom()->getColor()->setARGB('FF993300');
        $objActSheet->getStyle('B2')->getBorders()->getTop()->getColor()->setARGB('FF993300');

        //填充颜色
        $objActSheet->getStyle('A1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
        $objActSheet->getStyle('A1')->getFill()->getStartColor()->setARGB('FF808080');

		//手动设置前4列宽度
		$objActSheet->getColumnDimension('A')->setWidth(15);
		$objActSheet->getColumnDimension('B')->setWidth(35);

        //设置自动换行
        $objActSheet->getStyle('B1')->getAlignment()->setWrapText(TRUE);
        $objActSheet->getStyle('B')->getAlignment()->setWrapText(TRUE);

        //水平居中
        $objExcel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $objExcel->getActiveSheet()->getStyle('B')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);


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
     * 生成excel（入库单）
	 * 根据sql 的别名(as)来生成第一列的列名
	 * 根据查询的结果动态生成相应列的数据（不固定列）
     */
    public function ExcelForCCM($chapterArr){
        vendor('PHPExcel');
        $todayDir='download/ccmlist/';
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

        //echo $sql_where;exit;
        $sql = "SELECT IF(t.C1 IS NULL,'',REPLACE(SUBSTRING_INDEX(t.C1,'.',5),'.','/')) AS '上层目录',IF(t.KS_NAME IS NULL,'',t.KS_NAME) as '课程（节）',IF(l.WEEK_SUBJECT IS NULL,'',l.WEEK_SUBJECT) as '主题ID',IF(l.R_EXT5 IS NULL,'',l.R_EXT5) as '序号',IF(l.R_TITLE IS NULL,'',l.R_TITLE) as '标题', '' as'制作要求','' as '处理类型',(select max(p2.rt_name) from RMS_RES_PARAM p2 where l.R_TYPECODE = p2.RT_CODE) as '资源类型',IF(m.RF_NAME IS NULL,'',m.RF_NAME) as '资源格式',IF(s.DETAIL_NAME IS NULL,'',s.DETAIL_NAME) as '课堂应用',IF (l.LINK_TYPE = '0','',IF(l.LINK_TYPE = '1','静态链接',IF(l.LINK_TYPE = '2','仿真实验',IF(l.LINK_TYPE = '3','英语同步练',IF(l.LINK_TYPE = '4','中心库资源',''))))) AS '链接类型',IF (l.LINK_VALUE IS NULL,'',IF (l.LINK_VALUE LIKE '%experiment%' ,(SELECT name FROM win_fzsy  WHERE id = SUBSTRING_INDEX(l.LINK_VALUE,'/',-1)),l.LINK_VALUE)) AS '链接地址',IF (p.KNOWLEDGE_NEXT_TYPE = 'ZZDLX','课前预习',IF (p.KNOWLEDGE_NEXT_TYPE = 'TSDXLX','课后提升',IF (p.KNOWLEDGE_NEXT_TYPE = 'GLZYLX','专题类型',''))) AS '导学类型',IF(p.K_DESC IS NULL,'',trim(REPLACE(p.K_DESC,'\r\n','<br>'))) as '学习目标',IF(p.KNOWLEDGE_NAME IS NULL,'',trim(REPLACE(p.KNOWLEDGE_NAME,'\r\n','<br>'))) as '导学应用',IF(p.KNOWLEDGE_DESC IS NULL,'',trim(REPLACE(p.KNOWLEDGE_DESC,'\r\n','<br>'))) as '学习引导',IF(l.R_DESC IS NULL,'',trim(REPLACE(l.R_DESC,'\r\n','<br>'))) as '描述',IF(l.R_PUBLISHER IS NULL,'',l.R_PUBLISHER) as '资源应商',IF(l.R_EXT3 IS NULL,'',l.R_EXT3) as '上传者','' as '海报',l.R_CODE as '资源编码',IF(l.R_USED_OBJ='OBJ001','教师备课','教师备课授课') as '使用对象',IF(l.R_EXT6='ZYNEW','NEW',IF(l.R_EXT6='ZYHOT','HOT',IF(l.R_EXT6='ZYTJ','推荐',''))) as '资源推介',IF(l.R_KEYWORDS IS NULL,'',trim(REPLACE(l.R_KEYWORDS,'\r\n','<br>'))) as '关键字','' as '备注'  FROM ((RMS_RESOURCEINFO l INNER JOIN SHARE_KNOWLEDGE_STRUCTURE t ON l.R_KS_ID=t.KS_CODE AND  ".$sql_where." AND l.R_STATE='1' AND t.FLAG='1' AND (l.R_HASHCODE='' OR l.R_HASHCODE is NULL) ) INNER JOIN RMS_RESOURCEFORMAT m ON l.R_FORMAT=m.RF_CODE) LEFT JOIN RMS_KNOWLEDGE_RESINFO q ON l.R_CODE=q.R_CODE LEFT JOIN RMS_KNOWLEDGE p ON p.KNOWLEDGE_CODE=q.KNOWLEDGE_CODE LEFT JOIN RMS_DICTIONARY_DETAIL s ON l.c1=s.DETAIL_CODE order by l.R_KS_ID,l.R_EXT5;";

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


        //手动设置前4列宽度
        $objActSheet->getColumnDimension('A')->setWidth(35);
        $objActSheet->getColumnDimension('B')->setWidth(25);


        foreach ($data as $key=>$v){
            for($i=0; $i<count($keyArray); $i++){
               /// echo $arr[$i].($key+2).'||'.$v[$keyArray[$i]].'<br>';continue;
                if($arr[$i]=='U'){
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





}


