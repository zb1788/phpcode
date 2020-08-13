<?php
namespace Home\Controller;
use Think\Controller;
class PagingController extends CheckController {
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
    	$Model=M();
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
    	$sub_pages=5;
    	/* 实例化一个分页对象 */
    	Vendor('SubPages');
    	$subPages=new \SubPages($page_size,$total,$pageCurrent,$sub_pages,'pagelist');
    	$page= $subPages->subPageCss4();
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
    	$Model_book=M('book');
    	$Model_book->where('bookid="%s"',$id)->delete();
    	//删除章节对应页码
    	$Model_bc=M('book_chapter');
    	$Model_bc->where('bookid="%s"',$id)->delete();
    	//删除音频
    	$Model_bcm=M('book_chapter_mp3');
    	//删除数据库前先删物理文件
    	$data_bcm=$Model_bcm->where('bookid="%s"',$id)->field('musicfile')->select();
    	foreach ($data_bcm as $v){
    		unlink('Uploads/audio/'.$v['musicfile']);//删除音频
    	}
    	$Model_bcm1=M('book_chapter_mp3');
    	$Model_bcm1->where('bookid="%s"',$id)->delete();

    	//删除图片
    	$Model_bp=M('book_page');
    	//删除数据库前先删物理文件
    	$data_bp=$Model_bp->where('bookid="%s"',$id)->field('pagefile')->select();
    	foreach ($data_bp as $v){
    		unlink($v['pagefile']);//删除图片
    	}
    	$Model_bp1=M('book_page');
    	$Model_bp1->where('bookid="%s"',$id)->delete();

    	$Model_bph=M('book_page_hot');
    	//逻辑删除
    	$data['isdel']=1;
    	$Model_bph->where('bookid="%s"',$id)->save($data);

    }
    /**
     * 更新排序
     */
    public function  updateSort(){
        $sortsInfo=I('sortsInfo/s',0);
    	$sortsInfo=str_replace('&quot;', '"', $sortsInfo);
    	foreach (json_decode($sortsInfo,true) as $v){
    		$Model_book=M('book');
    		$data['sortid']=$v['sortid'];
    		$Model_book->where('id="%d"',$v['id'])->save($data);
    	}

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
    	//判断find查询结果是否为数组，如果不是数组表示查询结果为空
    	if (is_array($da)){
    		$m->where('id="%d"',$id)->save($data);
    	}else {
    		$m->add($data);
    	}
    }



    /**
     * ajax刷下拉框
     * @return [type] [description]
     */
    public function getCourseinfo(){
        $arr[0]['id']='1';
        $arr[0]['name']='奔驰';
        $arr[1]['id']='2';
        $arr[1]['name']='宝马';
        $arr[2]['id']='3';
        $arr[2]['name']='奥迪';
        $this->ajaxReturn($arr);
    }










}