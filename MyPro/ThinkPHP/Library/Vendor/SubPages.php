<?php
//namespace Org\Util;
class SubPages{

private $each_disNums;//每页显示的条目数
private $nums;//总条目数
private $current_page;//当前被选中的页
private $sub_pages;//每次显示的页数,最好是奇数
private $pageNums;//总页数
private $page_array = array();//用来构造分页的数组
private $funName;//js分页函数的名称
//private $subPage_type;//显示分页的类型
   /*
   __construct是SubPages的构造函数，用来在创建类的时候自动运行.
   @$each_disNums   每页显示的条目数
   @nums     总条目数
   @current_num     当前被选中的页
   @sub_pages       每次显示的页数
   @subPage_type    显示分页的类型

   当@subPage_type=1的时候为普通分页模式
         example：   共4523条记录,每页显示10条,当前第1/453页 [首页] [上页] [下页] [尾页]
         当@subPage_type=2的时候为经典分页样式
         example：   当前第1/453页 [首页] [上页] 1 2 3 4 5 6 7 8 9 10 [下页] [尾页]
   */
function __construct($each_disNums,$nums,$current_page,$sub_pages,$funName){
   $this->each_disNums=intval($each_disNums);
   $this->nums=intval($nums);
    if(!$current_page){
    $this->current_page=1;
    }else{
    $this->current_page=intval($current_page);
    }
   $this->sub_pages=intval($sub_pages);
   $this->pageNums=ceil($nums/$each_disNums);
   $this->funName = $funName;
   //$this->show_SubPages($subPage_type);
   //echo $this->pageNums."--".$this->sub_pages;
}


/*
    __destruct析构函数，当类不在使用的时候调用，该函数用来释放资源。
   */
function __destruct(){
    unset($each_disNums);
    unset($nums);
    unset($current_page);
    unset($sub_pages);
    unset($pageNums);
    unset($page_array);
   // unset($subPage_type);
   }

/*
    show_SubPages函数用在构造函数里面。而且用来判断显示什么样子的分页
   */
// function show_SubPages($subPage_type){
//     if($subPage_type == 1){
//     $this->subPageCss1();
//     }elseif ($subPage_type == 2){
//     $this->subPageCss2();
//     }elseif ($subPage_type == 3){
//     $this->subPageCss3();
//     }
//    }


/*
    用来给建立分页的数组初始化的函数。
   */
function initArray(){
    for($i=0;$i<$this->sub_pages;$i++){
    $this->page_array[$i]=$i;
    }
    return $this->page_array;
   }


/*
    construct_num_Page该函数使用来构造显示的条目
    即使：[1][2][3][4][5][6][7][8][9][10]
   */
function construct_num_Page(){
    if($this->pageNums < $this->sub_pages){
    $current_array=array();
     for($i=0;$i<$this->pageNums;$i++){
     $current_array[$i]=$i+1;
     }
    }else{
    $current_array=$this->initArray();
     if($this->current_page <= 3){
      for($i=0;$i<count($current_array);$i++){
      $current_array[$i]=$i+1;
      }
     }elseif ($this->current_page <= $this->pageNums && $this->current_page > $this->pageNums - $this->sub_pages + 1 ){
      for($i=0;$i<count($current_array);$i++){
      $current_array[$i]=($this->pageNums)-($this->sub_pages)+1+$i;
      }
     }else{
      for($i=0;$i<count($current_array);$i++){
      $current_array[$i]=$this->current_page-2+$i;
      }
     }
    }

    return $current_array;
   }

/*
   构造普通模式的分页
   共4523条记录,每页显示10条,当前第1/453页 [首页] [上页] [下页] [尾页]
   */
function subPageCss1(){
   $subPageCss1Str="";

    if($this->current_page > 1){
    //调用查询页面的js方法pagelist()
    $subPageCss1Str.='[<a href="javascript:;" onclick="'.$this->funName.'(1,'.$this->each_disNums.')">首页</a>] ';
    $subPageCss1Str.='[<a href="javascript:;" onclick="'.$this->funName.'('.($this->current_page-1).','.$this->each_disNums.')">上一页</a>] ';
    }else {
        $subPageCss1Str.='<a style="disabled:true;color:#ccc;">首页</a> ';
        $subPageCss1Str.='<a style="disabled:ture;color:#ccc;">上一页</a> ';
    }

    if($this->current_page < $this->pageNums){
    //调用查询页面的js方法pagelist()
    $subPageCss1Str.='[<a href="javascript:;" onclick="'.$this->funName.'('.($this->current_page+1).','.$this->each_disNums.')">下一页</a>] ';
    $subPageCss1Str.='[<a href="javascript:;" onclick="'.$this->funName.'('.$this->pageNums.','.$this->each_disNums.')">尾页</a>] ';
    }else {
        $subPageCss1Str.='<a style="disabled:ture;color:#ccc;">下一页</a> ';
        $subPageCss1Str.='<a style="disabled:ture;color:#ccc;">尾页</a> ';
    }

    $subPageCss1Str.="共".$this->nums."条记录，";
    $subPageCss1Str.="每页显示".$this->each_disNums."条，";
    $subPageCss1Str.="当前第".$this->current_page."/".$this->pageNums."页 ";
    return $subPageCss1Str;

   }


/*
   构造经典模式的分页
 [首页] [上页] 1 2 3 4 5 6 7 8 9 10 [下页] [尾页] 当前第1/453页
   */
function subPageCss2(){
   $subPageCss2Str="";

    if($this->current_page > 1){
    //调用查询页面的js方法pagelist()
    $subPageCss2Str.='<a href="javascript:;" onclick="'.$this->funName.'(1,'.$this->each_disNums.')">首页</a> ';
    $subPageCss2Str.='<a href="javascript:;" onclick="'.$this->funName.'('.($this->current_page-1).','.$this->each_disNums.')">上一页</a> ';
    }else {
        $subPageCss2Str.='<a style="disabled:true;color:#ccc;">首页</a> ';
        $subPageCss2Str.='<a style="disabled:ture;color:#ccc;">上一页</a> ';
    }

   $a=$this->construct_num_Page();
    for($i=0;$i<count($a);$i++){
	    $s=$a[$i];
	    if($s == $this->current_page ){
	        $subPageCss2Str.='<a href="javascript:;" style="background:#CCCCCC; font-weight:bold;" >'.$s.'</a>';
	     }else{
	     //调用查询页面的js方法pagelist()
	     $subPageCss2Str.='<a href="javascript:;" onclick="'.$this->funName.'('.$s.','.$this->each_disNums.')">'.$s.'</a>';
	     }
    }
    if($this->current_page < $this->pageNums){
    //调用查询页面的js方法pagelist()
    $subPageCss2Str.='<a href="javascript:;" onclick="'.$this->funName.'('.($this->current_page+1).','.$this->each_disNums.')">下一页</a> ';
    $subPageCss2Str.='<a href="javascript:;" onclick="'.$this->funName.'('.$this->pageNums.','.$this->each_disNums.')">尾页</a> ';
    }else {
        $subPageCss2Str.='<a style="disabled:ture;color:#ccc;">下一页</a> ';
        $subPageCss2Str.='<a style="disabled:ture;color:#ccc;">尾页</a> ';
    }
    $subPageCss2Str.="共".$this->nums."条记录，";
    $subPageCss2Str.="每页显示".$this->each_disNums."条，";
    $subPageCss2Str.="当前第".$this->current_page."/".$this->pageNums."页 ";
    return $subPageCss2Str;
   }
/*
    构造下拉框的分页
   [首页] [上页]  4  [下页] [尾页] 当前第1/453页 [下拉框]
      需要配合查询页面的选中下拉框的出发事件
   */
   function subPageCss3(){
	   	$subPageCss3Str="";

	   	if($this->current_page > 1){
	   		//调用查询页面的js方法pagelist()
	   		$subPageCss3Str.='<a href="javascript:;" onclick="'.$this->funName.'(1,'.$this->each_disNums.')">首页</a> ';
	   		$subPageCss3Str.='<a href="javascript:;" onclick="'.$this->funName.'('.($this->current_page-1).','.$this->each_disNums.')">上一页</a> ';
	   	}else {
        $subPageCss3Str.='<a style="disabled:true;color:#ccc;">首页</a> ';
        $subPageCss3Str.='<a style="disabled:ture;color:#ccc;">上一页</a> ';
	   	}

	   	$subPageCss3Str.='<a href="javascript:;" style="background-color:#f7f7f7;">'.$this->current_page.'</a> ';

	   	if($this->current_page < $this->pageNums){
	   		//调用查询页面的js方法pagelist()
	   		$subPageCss3Str.='<a href="javascript:;" onclick="'.$this->funName.'('.($this->current_page+1).','.$this->each_disNums.')">下一页</a> ';
	   		$subPageCss3Str.='<a href="javascript:;" onclick="'.$this->funName.'('.$this->pageNums.','.$this->each_disNums.')">尾页</a> ';
	   	}else {
        $subPageCss3Str.='<a style="disabled:ture;color:#ccc;">下一页</a> ';
        $subPageCss3Str.='<a style="disabled:ture;color:#ccc;">尾页</a> ';
	   	}

	   	$subPageCss3Str.='<select name="SelectPages" class="select" id="SelectPages" style="height:27px; line-height:27px;">';

	   	for ($i=1;$i<=$this->pageNums;$i++){
	   		if($i == $this->current_page)
	   		{
	   			$selected = 'selected';
	   		}
	   		else{
	   			$selected = '';
	   		}
	   		$subPageCss3Str.='<option value="'.$i.'" '.$selected.' >'.$i.'</option>';
	   	}

	   	$subPageCss3Str.='</select>';
	   	$subPageCss3Str.="&nbsp;&nbsp;共".$this->nums."条记录，";
	   	$subPageCss3Str.="每页显示".$this->each_disNums."条，";
	   	$subPageCss3Str.="当前第".$this->current_page."/".$this->pageNums."页 ";
	   	$subPageCss3Str.='<input type="hidden" id="current_page" value="'.$this->current_page.'" />';
	   	$subPageCss3Str.='<input type="hidden" id="total_page" value="'.$this->pageNums.'" />';
	   	$subPageCss3Str.='<input type="hidden" id="nums" value="'.$this->nums.'" />';
	   	 return  $subPageCss3Str;
   }//subPageCss3方法结束

     /*
     带省略号的分页
   [首页] [上页] ... 4 5 6 7 8 9 10... [下页] [尾页] 当前第1/453页
     */
    function subPageCss4(){
      $subPageCss4Str="";
      //计算偏移量
      $pageOffSet = ($this->sub_pages-1)/2;

      if($this->current_page > 1){
        //调用查询页面的js方法pagelist()
        $subPageCss4Str.='<a href="javascript:;" onclick="'.$this->funName.'(1,'.$this->each_disNums.')">首页</a> ';
        $subPageCss4Str.='<a href="javascript:;" onclick="'.$this->funName.'('.($this->current_page-1).','.$this->each_disNums.')">上一页</a> ';
      }else {
        $subPageCss4Str.='<a style="disabled:true;color:#ccc;">首页</a> ';
        $subPageCss4Str.='<a style="disabled:ture;color:#ccc;">上一页</a> ';
      }


      //初始化数据
      $start = 1;//第一页
      $end = $this->pageNums;//共几页

      //echo $start.'|'.$end;exit;
      //总页数大于显示的页数
      if($this->pageNums>$this->sub_pages){
        if($this->current_page>$pageOffSet+1){
          $subPageCss4Str.='<a>...</a>';
        }
        if($this->current_page>$pageOffSet){
          $start = $this->current_page-$pageOffSet;
          $end = $this->pageNums>$this->current_page+$pageOffSet?$this->current_page+$pageOffSet:$this->pageNums;
        }else{
          $start = 1;
          $end = $this->pageNums>$this->sub_pages?$this->sub_pages:$this->pageNums;
        }
        if($this->current_page+$pageOffSet>$this->pageNums){
          $start = $start - ($this->current_page + $pageOffSet - $end);
        }
      }

      //echo $start.'|'.$end.'|'.$this->current_page;exit;
      for($i = $start; $i<=$end; $i++){
        if($i == $this->current_page){
          $subPageCss4Str.='<a href="javascript:;" style="background:#CCCCCC; font-weight:bold;" >'.$i.'</a>';
        }else{
           $subPageCss4Str.='<a href="javascript:;" onclick="'.$this->funName.'('.$i.','.$this->each_disNums.')">'.$i.'</a> ';
        }
      }

      //尾部省略
      if($this->pageNums > $this->sub_pages && $this->pageNums > $this->current_page + $pageOffSet){
        $subPageCss4Str.='<a>...</a>';
      }

      if($this->current_page < $this->pageNums){
        //调用查询页面的js方法pagelist()
        $subPageCss4Str.='<a href="javascript:;" onclick="'.$this->funName.'('.($this->current_page+1).','.$this->each_disNums.')">下一页</a> ';
        $subPageCss4Str.='<a href="javascript:;" onclick="'.$this->funName.'('.$this->pageNums.','.$this->each_disNums.')">尾页</a> ';
      }else {
        $subPageCss4Str.='<a style="disabled:ture;color:#ccc;">下一页</a> ';
        $subPageCss4Str.='<a style="disabled:ture;color:#ccc;">尾页</a> ';
      }
      return  $subPageCss4Str;

    }





}
?>