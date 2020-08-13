<?php
namespace Home\Controller;
use Think\Controller;
class ZipController extends Controller {
    public function button(){
        $this->display();
    }
    /**
     * 打包并下载
     */
    public function zipToDownload(){
    	$path="Uploads/中文zip";//要打包的路径
    	$path=iconv('utf-8', 'gbk', $path);//包含中文的路径需要转码
    	$zip=filesToZip($path);//打包文件夹，生成$zip压缩包
    	download($zip,' 1.zip');//下载$zip压缩包，并重命名,如果中文名需要转成utf-8
    	vendor('fileDirUtil');
    	$dir = new \fileDirUtil();
    	$dir->unlinkFile($zip);//下载完成后删除压缩包

    }


    /**
     * 压缩文件
     *
     */

    public function fileToZip(){
        vendor('fileDirUtil');
        $dir = new \fileDirUtil();
        Vendor("PHPZip");
        $Zip = new \PHPZip();
        $path = './caiji/zip';
        $zipfile = './caiji/test.zip';
        $dir->unlinkDir($zipfile);
        $Zip->Zip($path, $zipfile);
    }


    /**
     * 方法2
     */
    public function fileToZip2(){
        $zip=new \ZipArchive();
        $zifile = 'download/' . $bookid . '.zip';
        if($zip->open($zifile, \ZipArchive::OVERWRITE)=== TRUE){
            $this->addFileToZip('download/'.$bookname, $zip); //调用方法，对要打包的根目录进行操作，并将ZipArchive的对象传递给方法
            $zip->close(); //关闭处理的zip文件
        }

    }



}