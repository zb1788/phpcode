<?php
namespace Home\Controller;
use Think\Controller;
class FileDirController extends Controller {
    public function index(){

    	//rename('caiji/⺮.png', 'caiji/2.png');exit();
        vendor('fileDirUtil');
    	$dir = new \fileDirUtil();



    	$files=$dir->dirList('tmp/','png');//本目录下的所有图片

    	var_dump($files);exit();

    	for($i=0;$i<count($files);$i++){
    		rename($files[$i],'tmp/'.$i.'.png');
    	}


exit();







    	$dirs=$dir->dirNodeTree('TmpUploads');//目录数组

    	//循环文件夹下的子文件夹
    	for ($i=0;$i<count($dirs);$i++){
    		$jiaocai= $dirs[$i];
    		//中文名称需要转码
    		$jiaocaiToUtf8= iconv('GBK', 'utf-8', $jiaocai);//文件夹名称：一年级-上学期-语文-人教版

    		$files=$dir->dirList('TmpUploads/'.$dirs[$i],'jpg');//本目录下的所有图片

    		for ($j=0;$j<count($pics);$j++){
    			//处理当前文件夹下的每个文件
    		}
    	}
    }
}