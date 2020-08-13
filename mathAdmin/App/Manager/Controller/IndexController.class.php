<?php
namespace Manager\Controller;
use Think\Controller;
class IndexController extends CheckController {
    public function index(){
        $this->display();
    }

    public function addgenre(){
        $this->display();
    }

    public function getGrades(){
        $arr['0001'] = '一年级';
        $arr['0002'] = '二年级';
        $arr['0003'] = '三年级';
        $arr['0004'] = '四年级';
        $arr['0005'] = '五年级';
        $arr['0006'] = '六年级';
        $arr['0007'] = '七年级';
        $arr['0008'] = '八年级';
        $arr['0009'] = '九年级';
        $this->ajaxReturn($arr);
    }
    public function getTerms(){
        $arr['0001'] = '上学期';
        $arr['0002'] = '下学期';
        $arr['0000'] = '全一册';
        $this->ajaxReturn($arr);
    }
    public function getSubjects(){
        $arr['0001'] = '语文';
        $arr['0002'] = '数学';
        $arr['0003'] = '英语';
        $arr['0004'] = '物理';
        $arr['0005'] = '化学';
        $arr['0006'] = '音乐';
        $arr['0007'] = '美术';
        $arr['0008'] = '科学';
        $arr['0009'] = '品德';
        $arr['0010'] = '生物';
        $arr['0011'] = '地理';
        $arr['0012'] = '政治';
        $arr['0013'] = '历史';
        $arr['0014'] = '信息技术';
        $arr['0015'] = '通用技术';
        $this->ajaxReturn($arr);
    }

    //口算王添加(弃用)
    public function addGenreData(){
        $type = I('type/s','');//add edit
        $id = I('id/d',0);//add的时候是sx_genre中的id;eidt的时候gid
        $option = I('option/d',0);//1代表是共用的分类
        $genreName = I('name/s','');
        $gradeForm = I('gradeForm/s','');
        $termForm = I('termForm/s','');
        $data['name'] = $genreName;
        $data['grade'] = $gradeForm;
        $data['term'] = $termForm;

        $m = M('genre');
        if($type == 'add'){
            if($id != 0 ){
                //选择其他年级下的分类
                $data = $m->where('id="%d"',$id)->find();
                $data['id'] = $id;
            }
            $re = $m->where('name="%s" and grade="%s" and term="%s" and isdel=0',$genreName,$gradeForm,$termForm)->find();
            
            if(empty($re)){
                $maxdata = $m->where('grade="%s" and term="%s" and isdel=0',$gradeForm,$termForm)->max('sortid');
                $data['sortid'] = ($maxdata+1);
                $addid = M('genre')->add($data);
                $this->recordUserOption('genre','add','id='.$addid);
                $info['status'] = 'ok';
                $info['msg'] = '添加成功';
            }else{
                $info['status'] = 'error';
                $info['msg'] = '已存在';
            }
        }else{
            $m->where('id="%d"',$id)->save($data);
            $this->recordUserOption('genre','edit','id='.$id);
            $info['status'] = 'ok';
            $info['msg'] = '保存成功';
        }
        $this->ajaxReturn($info);
    }

        //基础库分类添加
        public function addGenreDataAll(){
            $type = I('type/s','');//add edit
            $id = I('id/d',0);
            $genreName = I('name/s','');
            $grade = I('grade/s','');
            $term = I('term/s','');
            $subject = I('subject/s','');
            $isbase = I('isbase/d',0);
            $genrePic = I('genrePic/s','');
            if($isbase == 1){
                $table = 'base_genre';
                $m = M('base_genre');
            }else{
                $table = 'genre';
                $m = M('genre');
            }

            $data['name'] = $genreName;
            $data['grade'] = $grade;
            $data['term'] = $term;
            $data['subject'] = $subject;
            $data['pic'] = $genrePic;
    
            if($type == 'add'){

                $re = $m->where('name="%s" and grade="%s" and term="%s" and subject="%s" and isdel=0',$data['name'],$grade,$term,$subject)->find();
                
                if(empty($re)){
                    $maxdata = $m->where('grade="%s" and term="%s" and subject="%s" and isdel=0',$grade,$term,$subject)->max('sortid');
                    $data['sortid'] = ($maxdata+1);
					
                    $addid = $m->add($data);

                    $this->recordUserOption($table,'add','id='.$addid);
                    $info['status'] = 'ok';
                    $info['msg'] = '添加成功';
                }else{
                    $info['status'] = 'error';
                    $info['msg'] = '已存在';
                }
            }else{
				if($isbase != 1){
					$total = M('stage')->where('genreid="%d" and isdel=0',$id)->count();
					$data['total'] = $total;
				}
				
                $m->where('id="%d"',$id)->save($data);
                $this->recordUserOption($table,'edit','id='.$id);
                $info['status'] = 'ok';
                $info['msg'] = '保存成功';
            }
            $this->ajaxReturn($info);
        }

        //从其他分类复制
        public function addGenreDataFromOther(){
            $grade = I('grade/s','');
            $term = I('term/s','');
            $subject = I('subject/s','');

            $list = I('genrelist/s','');
            $str = str_replace('&quot;', '"', $list);
            $arr = json_decode($str,true);


            $data['grade'] = $grade;
            $data['term'] = $term;
            $data['subject'] = $subject;

            $m = M('genre');
            $stage = M('stage');
            $question = M('question');
            foreach($arr as $v){
                $id = $v;
                $re = $m->where('id="%d"',$id)->find();
                $data['name'] = $re['name'];
                $data['total'] = $re['total'];

                $maxdata = $m->where('grade="%s" and term="%s" and subject="%s" and isdel=0',$grade,$term,$subject)->max('sortid');
                $data['sortid'] = ($maxdata+1);
                //新增分类
                $addid = $m->add($data);
                //复制原来分类下的关卡和试题
                // $sql_insert_stage = 'INSERT INTO sx_stage (genreid,stagename,total,remark,totaltime,sortid,isdel) SELECT '.$addid.' as genreid,stagename,total,remark,totaltime,sortid,isdel  FROM sx_stage WHERE genreid='.$id.' AND isdel=0;';
                // M()->execute($sql_insert_stage);
                $data_old_stage =  $stage->where('genreid="%d" and isdel=0',$id)->select();
                foreach($data_old_stage as $vv){
                    $data_new_stage['genreid'] = $addid;
                    $data_new_stage['stagename'] = $vv['stagename'];
                    $data_new_stage['total'] = $vv['total'];
                    $data_new_stage['totaltime'] = $vv['totaltime'];
                    $data_new_stage['remark'] = $vv['remark'];
                    $data_new_stage['sortid'] = $vv['sortid'];
                    $data_new_stage['isdel'] = $vv['isdel'];
                    $newStageId = $stage->add($data_new_stage);
                    $oldStageId = $vv['id'];
                    //复制原来关卡下的试题
                    $data_old_ques = $question->where('stageid="%d" and isdel=0',$oldStageId)->select();
                    foreach($data_old_ques as $vvv){
                        $data_new_ques['stageid'] = $newStageId;
                        $data_new_ques['quesid'] = $vvv['quesid'];
                        $data_new_ques['isdel'] = $vvv['isdel'];
                        $question->add($data_new_ques);
                    }

                }

                $this->recordUserOption('genre','add','id='.$addid);
            }

            $info['status'] = 'ok';
            $info['msg'] = '成功';
            $this->ajaxReturn($info);

        }
    
    //口算王分类编辑
    public function editGenreData(){
        $type = I('type/s','');//add edit
        $id = I('id/d',0);
        $genreName = I('name/s','');
        $gradeForm = I('grade/s','');
        $termForm = I('term/s','');

        if($gradeForm == ''||$termForm == ''){
            $info['status'] = 'err';
            $info['msg'] = '年级或者学期不对';
        }else{
            $data['name'] = $genreName;
            $data['grade'] = $gradeForm;
            $data['term'] = $termForm;
    
            $m = M('genre');
            $m->where('id="%d"',$id)->save($data);
            $this->recordUserOption('genre','edit','id='.$id);
            $info['status'] = 'ok';
            $info['msg'] = '保存成功';
        }
        $this->ajaxReturn($info);
    }

    // 基础分类编辑
    public function editGenreDataBase(){
        $type = I('type/s','');//add edit
        $id = I('id/d',0);
        $genreName = I('name/s','');
        $gradeForm = I('grade/s','');
        $termForm = I('term/s','');

        if($gradeForm == ''||$termForm == ''){
            $info['status'] = 'err';
            $info['msg'] = '年级或者学期不对';
        }else{
            $data['name'] = $genreName;
            $data['grade'] = $gradeForm;
            $data['term'] = $termForm;
    
            $m = M('base_genre');
            $m->where('id="%d"',$id)->save($data);
            $this->recordUserOption('base_genre','edit','id='.$id);
            $info['status'] = 'ok';
            $info['msg'] = '保存成功';
        }
        $this->ajaxReturn($info);
    }

    //获取当前分类记录的信息
    public function getGenreInfo(){
        $id = I('id/d',0);
        $isbase = I('isbase/d',0);
        if($isbase == 1){
            $m = M('base_genre');
        }else{
            $m = M('genre');
        }
        
        $re = $m->where('id="%d"',$id)->find();
        $this->ajaxReturn($re);
    }

    public function addstep(){
        $id = I('id/d',0);
        $type = I('type/s','');
        if($type == 'edit'){
            $m = M('stage');        
            $re = $m->where('id="%d"',$id)->find();
            $this->assign('data',$re);
        }
        $this->display();
    }
    //获取当前关卡记录的信息
    public function getStageInfo(){
        $id = I('id/d',0);

        $m = M('stage');        
        $re = $m->where('id="%d"',$id)->find();
        $this->ajaxReturn($re);
    }

    public function getGenreListTest(){
        $grade = I('grade/s','0');
        $term = I('term/s','0');
        $where = ' where 1=1 ';
        if($grade != '0'){
            $where .= ' and grade="'.$grade.'"';
        }
        if($term != '0'){
            $where .= ' and term="'.$term.'"';
        }
        $sql = 'select * from sx_genre order by sortid';
        $re = M()->query($sql.$where);
        $str = '{ "data": [';

        foreach($re as $k=>$v){
            $str .= '["'.$v['id'].'","'.$v['name'].'"],';
        }
        $str = rtrim($str,',');
        $str .= ']}';

        echo $str;
    }

    //获取分类列表
    public function getGenreList(){
        $grade = I('grade/s','0');
        $term = I('term/s','0');
        $subject = I('subject/s','0');
        $draw = I('draw/d',0);
        $start = I('start/d',0);
        $length = I('length/d',0);



        $search = !empty($_REQUEST['search']['value'])?$_REQUEST['search']['value']:'';

        $sql_search = ' and (';
        //获取哪些字段要被搜索
        foreach($_REQUEST['columns'] as $k=>$v){
            if($v['searchable'] == 'true'){
                $sql_search .= $v['data'].' like "%'.$search.'%" or ';
            }
        };

        $sql_search = rtrim($sql_search,'or ').')';

        //获取哪些字段要被排序
        $order_column_index = $_REQUEST['order']['0']['column'];//哪一列排序，从0开始
        $order_dir = $_REQUEST['order']['0']['dir'];//ase desc 升序或者降序
        // var_dump($order_column_index);
        // var_dump($_REQUEST['columns']);
        if($_REQUEST['columns'][$order_column_index]['orderable'] == 'true'){
            $order_column = $_REQUEST['columns'][$order_column_index]['data'];//字段名称
            $sql_order = ' order by '.$order_column.' '.$order_dir;
        }else{
            //默认排序
            $sql_order = ' order by id asc';
        }

        //分页条件
        $sql_limit = '';
        $sql_limit .= ' limit '.intval($start).', '.intval($length);

        $where = '';
        if($grade != '0'){
            $where .= ' and grade="'.$grade.'"';
        }
        if($term !== '0'){
            $where .= ' and term="'.$term.'"';
        }
        if($subject != '0'){
            $where .= ' and subject="'.$subject.'"';
        }

        $m = M();

        $sql = 'select * from sx_genre where isdel=0 ';
        $all = $m->query($sql.$where);

        // echo $sql.$where.$sql_search.$sql_order.$sql_limit;exit;
        if(empty($search)){
            $rows = $m->query($sql.$where.$sql_order.$sql_limit);
            // echo $m->getLastSql();exit;
            $recordsFiltered = count($all);
        }else{
            $rows = $m->query($sql.$where.$sql_search.$sql_order.$sql_limit);
            $rows1 = $m->query($sql.$where.$sql_search.$sql_order);

            $recordsFiltered = count($rows1);
        }

        $data['draw'] =  $draw;
        $data['recordsTotal'] = count($all);
        $data['recordsFiltered'] = $recordsFiltered;
        $data['data'] = $rows;
        $this->ajaxReturn($data);
    }

    //获取分类
    public function getGenres(){
        $grade = I('grade/s','0');
        $term = I('term/s','0');
        $subject = I('subject/s','0');
        $grade_t = I('grade_t/s','0');
        $term_t = I('term_t/s','0');
        $subject_t = I('subject_t/s','0');

        $data = M('genre')->where('grade="%s" and term="%s" and subject="%s" and isdel=0',$grade_t,$term_t,$subject_t)->select();

        foreach($data as $k=>$v){
            $id = $v['id'];
            $re = M('genre')->where('grade="%s" and term="%s" and subject="%s" and id="%d" and isdel=0',$grade,$term,$subject,$id)->find();
           

            if(!empty($re)){
                unset($data[$k]);
            }
        }

        $this->ajaxReturn($data);
    }

        //获取分类列表
        public function getGenreListBase(){
            $grade = I('grade/s','0');
            $term = I('term/s','0');
            $subject = I('subject/s','0');
            $draw = I('draw/d',0);
            $start = I('start/d',0);
            $length = I('length/d',0);
    
            $search = !empty($_REQUEST['search']['value'])?$_REQUEST['search']['value']:'';
    
            $sql_search = ' and (';
            //获取哪些字段要被搜索
            foreach($_REQUEST['columns'] as $k=>$v){
                if($v['searchable'] == 'true'){
                    $sql_search .= $v['data'].' like "%'.$search.'%" or ';
                }
            };
    
            $sql_search = rtrim($sql_search,'or ').')';
    
            //获取哪些字段要被排序
            $order_column_index = $_REQUEST['order']['0']['column'];//哪一列排序，从0开始
            $order_dir = $_REQUEST['order']['0']['dir'];//ase desc 升序或者降序
            // var_dump($order_column_index);
            // var_dump($_REQUEST['columns']);
            if($_REQUEST['columns'][$order_column_index]['orderable'] == 'true'){
                $order_column = $_REQUEST['columns'][$order_column_index]['data'];//字段名称
                $sql_order = ' order by '.$order_column.' '.$order_dir;
            }else{
                //默认排序
                $sql_order = ' order by id asc';
            }
    
            //分页条件
            $sql_limit = '';
            $sql_limit .= ' limit '.intval($start).', '.intval($length);
    
            $where = '';
            if($grade != '0'){
                $where .= ' and grade="'.$grade.'"';
            }
            if($term != '0'){
                $where .= ' and term="'.$term.'"';
            }
            if($subject != '0'){
                $where .= ' and subject="'.$subject.'"';
            }
            $m = M();
    
            $sql = 'select * from sx_base_genre where  isdel=0 ';
            $all = $m->query($sql.$where);
    
            // echo $sql.$where.$sql_search.$sql_order.$sql_limit;exit;
            if(empty($search)){
                $rows = $m->query($sql.$where.$sql_order.$sql_limit);
                $recordsFiltered = count($all);
            }else{
                $rows = $m->query($sql.$where.$sql_search.$sql_order.$sql_limit);
                $rows1 = $m->query($sql.$where.$sql_search.$sql_order);
    
                $recordsFiltered = count($rows1);
            }
    
            $data['draw'] =  $draw;
            $data['recordsTotal'] = count($all);
            $data['recordsFiltered'] = $recordsFiltered;
            $data['data'] = $rows;
            $this->ajaxReturn($data);
        }

        //基础分类排序
        public function getGenreListBaseSort(){
            $grade = I('grade/s','0');
            $term = I('term/s','0');
            $data = M('base_genre')->where('grade="%s" and term="%s" and isdel=0',$grade,$term)->order('sortid')->select();
            $this->ajaxReturn($data);
        }
    
    //口算王分类排序
    public function sortGenreData(){
        $data = I('data/s','');
        $str = str_replace('&quot;', '"', $data);
        $arr = json_decode($str,true);
        
        
        foreach($arr as $v){
            M('genre')->where('id="%d"',$v['id'])->setField('sortid',$v['sortid']);
        }
        $re['status'] = 'ok';
        $re['msg'] = '排序成功';
        $this->ajaxReturn($re);
    }

    //基础库分类排序
    public function sortGenreDataBase(){
        $data = I('data/s','');
        $str = str_replace('&quot;', '"', $data);
        $arr = json_decode($str,true);

        foreach($arr as $v){
            M('base_genre')->where('id="%d"',$v['id'])->setField('sortid',$v['sortid']);
        }
        $re['status'] = 'ok';
        $re['msg'] = '排序成功';
        $this->ajaxReturn($re);
    }

    //关卡试题排序
    public function sortQuesData(){
        $data = I('data/s','');
        $str = str_replace('&quot;', '"', $data);
        $arr = json_decode($str,true);

        foreach($arr as $v){
            M('question')->where('id="%d"',$v['id'])->setField('sortid',$v['sortid']);
        }
        $re['status'] = 'ok';
        $re['msg'] = '排序成功';
        $this->ajaxReturn($re);
    }
    //删除口算王分类
    public function delGenre(){
        $id = I('id/d',0);
        M('genre')->where('id="%d"',$id)->setField('isdel',1);
        $this->recordUserOption('genre','del','id='.$id);
    }

    //删除基础分类
    public function delGenreBase(){
        $id = I('id/d',0);
        M('base_genre')->where('id="%d"',$id)->setField('isdel',1);
        $this->recordUserOption('base_genre','del','id='.$id);
    }

    //获取题目类型
    public function getQuestionType(){
        $data = M('base_question_type')->select();
        $this->ajaxReturn($data);
    }
  

    //上传图片
    public function uploadimg(){
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 51200000000;//50k
        $upload->rootPath = 'upload/pic/';
        $upload->savePath = '';
        $upload->saveName = array('uniqid','');//为空则文件名不变,uniqid生成唯一字符串
        $upload->exts     = array('jpg', 'gif', 'png', 'jpeg');
        $upload->autoSub  = true; //开启子目录
        $upload->subName  = array('date','Ymd'); // 子目录命名方式
        $info = $upload->upload();
        if(!$info) {// 上传错误提示错误信息
			$arr_return["issuc"] = 0;
			$arr_return["msg"] = $upload->getError();
        }else{// 上传成功
            //图片地址
            //var_dump($info);
            $filepath='upload/pic/'.$info["file"]['savepath'].$info["file"]['savename'];
            
            $arr_return["issuc"] = 1;
			$arr_return["msg"] = base64EncodeImage($filepath);
        }        
        $this->ajaxReturn($arr_return);
    }
    //上传选项图片
    public function uploadChoiceimg(){
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 51200000000;//50k
        $upload->rootPath = 'upload/pic/';
        $upload->savePath = '';
        $upload->saveName = array('uniqid','');//为空则文件名不变,uniqid生成唯一字符串
        $upload->exts     = array('jpg', 'gif', 'png', 'jpeg');
        $upload->autoSub  = true; //开启子目录
        $upload->subName  = array('date','Ymd'); // 子目录命名方式
        $info = $upload->upload();
        if(!$info) {// 上传错误提示错误信息
			$arr_return["issuc"] = 0;
			$arr_return["msg"] = $upload->getError();
        }else{// 上传成功
            //图片地址
            //var_dump($info);
            $filepath='upload/pic/'.$info["fileChoice"]['savepath'].$info["fileChoice"]['savename'];
            
            $arr_return["issuc"] = 1;
			$arr_return["msg"] = base64EncodeImage($filepath);
        }        
        $this->ajaxReturn($arr_return);
    }

    //获取分类信息
    public function getGenre(){
        $data = M('genre')->where('isdel=0')->select();
        $this->ajaxReturn($data);
    }

    //添加基础试题信息
    public function addquestiondataBase(){
        $type = I('type/s','');
        $id = I('id/d',0);
        $quesgenre = I('quesgenre/d',0);
        $questionType = I('questionType/d',0);
        $questionName = I('questionName/s','');
        $answerFlag = I('answerflag/s','');
        // $contentType = I('contentType/d',0);
        // $contentName = I('contentName/s','');
        // $contentPic = I('contentPic/s','');
        $choiceType = I('choiceType/d',0);
        $choiceArr = I('choiceArr/s','');
        $answerArr = I('answerArr/s','');
        $answerStr=str_replace('&quot;', '"', $answerArr);
        $choiceStr=str_replace('&quot;', '"', $choiceArr);
        $jiexi = I('jiexi/s','');
        

        $data['quesType'] = $questionType;
        $data['quesName'] = htmlspecialchars_decode($questionName);
        // $data['quesContentType'] = $contentType;
        // if($contentType == 1){
        //     $data['quesContent'] = $contentName;
        // }else{
        //     $data['quesContent'] = $contentPic;
        // }

        $data['quesChoiceType'] = $choiceType;
        $arr = json_decode($choiceStr,true);


        $data['answerFlag'] = $answerFlag;//单选题OR多选
        $arr_answer = json_decode($answerStr,true);
		
		$arr_f = array('A','B','C','D','E','F');
		foreach($arr as $k=>$v){
            $arr_new[$k]['flag'] = $v['flag'];
			$arr_new[$k]['content'] = htmlspecialchars_decode($v['content']);	
		}


        $data['quesChoice'] = $arr_new;
        $data['answer'] = $arr_answer;
        $data['jiexi'] = trim(htmlspecialchars_decode($jiexi));
        $info['genreid'] = $quesgenre;
        $info['content'] = json_encode($data);
        $info['questype'] = $questionType;

        if($type == 'add'){
            $addid = M('base_question')->add($info);
            $this->recordUserOption('base_question','add','id='.$addid);
        }else{
            M('base_question')->where('id="%d"',$id)->save($info);
            $this->recordUserOption('base_question','edit','id='.$id);
        }
        

        $re['msg'] = 'ok';
        $re['errcode'] = 0;
        $this->ajaxReturn($re);
    }


    //口算王添加试题信息
    public function addquestiondata(){
        $type = I('type/s','');
        $id = I('id/d',0);
        $stage = I('stage/d',0);
        $questionType = I('questionType/d',0);
        $questionName = I('questionName/s','');
        $contentType = I('contentType/d',0);
        $contentName = I('contentName/s','');
        $contentPic = I('contentPic/s','');
        $choiceType = I('choiceType/d',0);
        $choiceArr = I('choiceArr/s','');
        $answer = I('answer/s','');
        $choiceStr=str_replace('&quot;', '"', $choiceArr);
        
        

        $data['quesType'] = $questionType;
        $data['quesName'] = $questionName;
        $data['quesContentType'] = $contentType;
        if($contentType == 1){
            $data['quesContent'] = $contentName;
        }else{
            $data['quesContent'] = $contentPic;
        }

        $data['quesChoiceType'] = $choiceType;
        $arr = json_decode($choiceStr,true);
        $data['quesChoice'] = $arr;
        $data['answer'] = $answer;
        $data['answerFlag'] = 1; //单选题
        $info['stageid'] = $stage;
        $info['content'] = json_encode($data);
        $info['questype'] = $questionType;

        if($type == 'add'){
            $addid = M('question')->add($info);

            $re = M('question')->where('stageid="%d"',$stage)->select();
            M('stage')->where('id="%d"',$stage)->setField('total',count($re));
            $this->recordUserOption('question','add','id='.$addid);
        }else{
            M('question')->where('id="%d"',$id)->save($info);
            $this->recordUserOption('question','edit','id='.$id);
        }
        

        $re['msg'] = 'ok';
        $re['errcode'] = 0;
        $this->ajaxReturn($re);
    }

    public function addMathDataBase(){
        $type = I('type/s','');
        $id = I('id/d',0);
        $genreid = I('genreid/d',0);
        $questionType = I('questionType/d',0);
        $questionName = I('questionName/s','');
        $contentType = I('contentType/d',0);
        $contentName = I('contentName/s','');
        $answer = I('answer/s','');
        $answer=str_replace('&quot;', '"', $answer);

        $contentName = trim($contentName);
        $contentName = str_replace('（','(',$contentName);
        $contentName = str_replace('）',')',$contentName);

        $contentName=preg_replace('/\s+/','',$contentName);
        // preg_match_all('/\[(.*?)\]/',$contentName, $out, PREG_SET_ORDER);

        // if(!empty($out)){
        //     foreach($out as $k=>$v){
        //         $answer[$k] = $v[1];
        //     }
        // }
        
        $answerArr = json_decode($answer,true);

        if(count($answerArr)>1){
            $data['answerFlag'] = 2; //多选题
            foreach($answerArr as $k=>$v){
                $arrAnswer[$k] = explode('|',$v);
            }
        }else{
            $data['answerFlag'] = 1; //单选题
            $arrAnswer = explode('|',$answerArr[0]);
        }
        $data['answer'] = $arrAnswer;

        
        // $contentName = preg_replace('/\[(.*?)\]/','#',$contentName);
        $contentName = preg_replace('/\{(.*?)\}/','#',$contentName);
        
	
        $arr = split_str($contentName);

        $str = '';
        $fuhao = '+-×÷/()|[]{}#=';
        $suanshiArr = array();
        $count = count($arr);
        foreach($arr as $k=>$v){
            if(trim($v)==''){
                continue;
            }else{
                $index = strpos($fuhao,$v);
               if($index===false){
                   //匹配失败
                   $str .= $v;
                   if($k==($count-1)){
                    array_push($suanshiArr,$str);
                   }
               }else{
                   if($str!==''){
                    array_push($suanshiArr,$str);
                   }
                   $str = '';
                   array_push($suanshiArr,$v);
               }
              
            }
        }
        
        // var_dump($arr);
        // var_dump($contentName);
        // var_dump($suanshiArr);exit;

        foreach($suanshiArr as $k=>$v){
            if($v == '/'){
                $fenshu = array($suanshiArr[$k-1],$suanshiArr[$k],$suanshiArr[$k+1]);
                unset($suanshiArr[$k-1]);
                unset($suanshiArr[$k+1]);
                $suanshiArr[$k] = $fenshu;
            }
			if($contentType == 2){
				if($v == '|'){
					//unset($suanshiArr[$k]);
				}
			}
        }

        // $of = array_search('/',$suanshiArr);
        // if($of !==false){
        //     $fenshu = array($suanshiArr[$of-1],$suanshiArr[$of],$suanshiArr[$of+1]);
        //     unset($suanshiArr[$of-1]);
        //     unset($suanshiArr[$of+1]);
        //     $suanshiArr[$of] = $fenshu;
        // }

        // var_dump($suanshiArr);
        // exit;

        $data['quesType'] = $questionType;
        $data['quesName'] = $questionName;
        $data['quesContentType'] = $contentType;
        $data['quesContent'] = $suanshiArr;

        $info['questype'] = $questionType;
        $info['genreid'] = $genreid;
        $info['content'] = json_encode($data);

        if($type == 'add'){
            $addid = M('base_question')->add($info);
            $this->recordUserOption('base_question','add','id='.$addid);
        }else{
            M('base_question')->where('id="%d"',$id)->save($info);
            $this->recordUserOption('base_question','edit','id='.$id);
        }
        

        $re['msg'] = 'ok';
        $re['errcode'] = 0;
        $this->ajaxReturn($re);
    }


    //添加口算卡算式
    public function addMathData(){
        $type = I('type/s','');
        $id = I('id/d',0);
        $stage = I('stage/d',0);
        $questionType = I('questionType/d',0);
        $questionName = I('questionName/s','');
        $contentType = I('contentType/d',0);
        $contentName = I('contentName/s','');
        $answer = I('answer/s','');
        $answer=str_replace('&quot;', '"', $answer);

        $contentName = trim($contentName);
        $contentName = str_replace('（','(',$contentName);
        $contentName = str_replace('）',')',$contentName);

        $contentName=preg_replace('/\s+/','',$contentName);
        // preg_match_all('/\[(.*?)\]/',$contentName, $out, PREG_SET_ORDER);

        // if(!empty($out)){
        //     foreach($out as $k=>$v){
        //         $answer[$k] = $v[1];
        //     }
        // }
        
        $answerArr = json_decode($answer,true);

        if(count($answerArr)>1){
            $data['answerFlag'] = 2; //多选题
            foreach($answerArr as $k=>$v){
                $arrAnswer[$k] = explode('|',$v);
            }
        }else{
            $data['answerFlag'] = 1; //单选题
            $arrAnswer = explode('|',$answerArr[0]);
        }
        $data['answer'] = $arrAnswer;

        
        // $contentName = preg_replace('/\[(.*?)\]/','#',$contentName);
        $contentName = preg_replace('/\{(.*?)\}/','#',$contentName);
        
        $arr = split_str($contentName);

        $str = '';
        $fuhao = '+-×÷/()|[]{}#=';
        $suanshiArr = array();
        $count = count($arr);
        foreach($arr as $k=>$v){
            if(trim($v)==''){
                continue;
            }else{
                $index = strpos($fuhao,$v);
               if($index===false){
                   //匹配失败
                   $str .= $v;
                   if($k==($count-1)){
                    array_push($suanshiArr,$str);
                   }
               }else{
                   if(!empty($str)){
                    array_push($suanshiArr,$str);
                   }
                   $str = '';
                   array_push($suanshiArr,$v);
               }
              
            }
        }
        
        // var_dump($arr);
        // var_dump($contentName);
        // var_dump($suanshiArr);

        foreach($suanshiArr as $k=>$v){
            if($v == '/'){
                $fenshu = array($suanshiArr[$k-1],$suanshiArr[$k],$suanshiArr[$k+1]);
                unset($suanshiArr[$k-1]);
                unset($suanshiArr[$k+1]);
                $suanshiArr[$k] = $fenshu;
            }
        }

        // $of = array_search('/',$suanshiArr);
        // if($of !==false){
        //     $fenshu = array($suanshiArr[$of-1],$suanshiArr[$of],$suanshiArr[$of+1]);
        //     unset($suanshiArr[$of-1]);
        //     unset($suanshiArr[$of+1]);
        //     $suanshiArr[$of] = $fenshu;
        // }

        // var_dump($suanshiArr);
        // exit;

        $data['quesType'] = $questionType;
        $data['quesName'] = $questionName;
        $data['quesContentType'] = $contentType;
        $data['quesContent'] = $suanshiArr;

        $info['questype'] = $questionType;
        $info['stageid'] = $stage;
        $info['content'] = json_encode($data);

        if($type == 'add'){
            $addid = M('question')->add($info);
            $this->recordUserOption('question','add','id='.$addid);
        }else{
            M('question')->where('id="%d"',$id)->save($info);
            $this->recordUserOption('question','edit','id='.$id);
        }
        

        $re['msg'] = 'ok';
        $re['errcode'] = 0;
        $this->ajaxReturn($re);
    }

    //获取试题列表
    public function getQuestionList(){
        $stage = I('stage/d',0);
        $quesType = I('quesType/d',0);

        // $sql = 'SELECT m.*,n.stagename FROM sx_question m,sx_stage n WHERE m.stageid=n.id and m.stageid='.$stage;

        if($quesType != 0){
            // $sql .= ' and m.questype='.$quesType;
            $sql = 'SELECT m.id,m.quesid,(SELECT n.stagename FROM sx_stage n WHERE n.id=m.stageid ) stagename,q.genreid,q.questype,q.content FROM sx_question m LEFT JOIN (SELECT * FROM sx_base_question where isdel=0 ) q ON m.quesid=q.id WHERE m.isdel=0 and m.stageid='.$stage.' and q.questype='.$quesType.' order by m.sortid';
        }else{
            $sql = 'SELECT m.id,m.quesid,(SELECT n.stagename FROM sx_stage n WHERE n.id=m.stageid ) stagename,q.genreid,q.questype,q.content FROM sx_question m LEFT JOIN (SELECT * FROM sx_base_question where isdel=0 ) q ON m.quesid=q.id WHERE m.isdel=0 and m.stageid='.$stage.' order by m.sortid';
        }
        $data = M()->query($sql);
        $this->ajaxReturn($data);
    }

    //获取试题列表
    public function getQuestionListBase(){
        $genre = I('genre/d',0);
        $quesType = I('quesType/d',0);

        $sql = 'SELECT m.*,n.name FROM sx_base_question m,sx_base_genre n WHERE m.genreid=n.id and m.isdel=0 and n.isdel=0 and m.genreid='.$genre;

        if($quesType != 0){
            $sql .= ' and m.questype='.$quesType;
        }

        $data = M()->query($sql);
        $this->ajaxReturn($data);
    }


    public function getQuestionListFromBase(){
        $genre = I('genre/d',0);
        $stageid = I('stageid/d',0);
        $quesType = I('quesType/d',0);

        // $sql = 'SELECT m.*,n.name FROM sx_base_question m,sx_base_genre n WHERE m.genreid=n.id and m.genreid='.$genre;
        if($quesType != 0){
            $sql = 'SELECT l.* FROM (SELECT m.*,n.name FROM sx_base_question m,sx_base_genre n WHERE m.genreid=n.id and m.isdel=0 and n.isdel=0 and m.genreid='.$genre.' and m.questype='.$quesType.') l LEFT JOIN (SELECT quesid FROM sx_question WHERE isdel=0 and stageid='.$stageid.') t ON l.id=t.quesid WHERE t.quesid is NULL';
        }else{
            $sql = 'SELECT l.* FROM (SELECT m.*,n.name FROM sx_base_question m,sx_base_genre n WHERE m.genreid=n.id and m.isdel=0 and n.isdel=0 and m.genreid='.$genre.') l LEFT JOIN (SELECT quesid FROM sx_question WHERE isdel=0 and stageid='.$stageid.') t ON l.id=t.quesid WHERE t.quesid is NULL';
        }

        $data = M()->query($sql);
        $this->ajaxReturn($data);
    }
    //获取基础题库当前试题
    public function getQuestionInfoBase(){
        $id = I('id/d',0);
        $data = M('base_question')->where('id="%d"',$id)->find();
        $genre = M('base_genre')->where('id="%d"',$data['genreid'])->find();
        $data['grade'] = $genre['grade'];
        $data['term'] = $genre['term'];
        $data['genreid'] = $genre['id'];
        $this->ajaxReturn($data);
    }

    //获取口算王当前试题
    public function getQuestionInfo(){
        $id = I('id/d',0);
        $data = M('question')->where('id="%d"',$id)->find();
        $stage = M('stage')->where('id="%d"',$data['stageid'])->find();
        $genre = M('genre')->where('id="%d"',$stage['genreid'])->find();
        $data['grade'] = $genre['grade'];
        $data['term'] = $genre['term'];
        $data['genreid'] = $genre['id'];
        $data['stageid'] = $stage['id'];
        $this->ajaxReturn($data);
    }

    //获取关卡信息
    public function getStepList(){
        $genreid  = I('genreid/d',0);
        $sql = 'SELECT n.grade,n.term,n.`name`,m.id,m.stagename,m.total,m.sortid,m.remark,m.totaltime,m.remark FROM sx_stage m,sx_genre n WHERE m.genreid=n.id AND m.isdel=0 and n.isdel=0 and m.genreid='.$genreid.' order by m.sortid asc';
        $data = M()->query($sql);
        $this->ajaxReturn($data);
    }

    //添加
    public function addQuestionFromBase(){
        $stageid = I('stageid/d',0);
        $data = I('queslist/s','');
        $str = str_replace('&quot;', '"', $data);
        $arr = json_decode($str,true);
		
		$m = M('question');
        foreach($arr as $v){
            $re['stageid'] = $stageid;
            $re['quesid'] = $v;
			$info = $m->where('stageid="%d" and isdel=0',$stageid)->find();
			if(empty($info)){
				$re['sortid'] = 1;
			}else{
				$maxdata = $m->where('stageid="%d" and isdel=0',$stageid)->max('sortid');
				$re['sortid'] = ($maxdata+1);
			}
			$addid = $m->add($re);

            $this->recordUserOption('question','add','id='.$addid);
        }
        $re = M('question')->where('stageid="%d" and isdel=0',$stageid)->select();
        M('stage')->where('id="%d"',$stageid)->setField('total',count($re));
        $info['status'] = 'ok';
        $info['msg'] = '添加成功';
        $this->ajaxReturn($info);
    }

    //添加关卡数据
    public function addStageData(){
        $type = I('type/s','');
        $name = I('name/s','');
        $remark = I('remark/s','');
		$totaltime = I('totaltime/d',0);
        $genreid = I('genreid/d',0);
        $id = I('id/d',0);
        $rcode = I('rcode/s','');
        $rtitle = I('rtitle/s','');

        $data['genreid'] = $genreid;
        $data['stagename'] = $name;
        $data['remark'] = htmlspecialchars_decode($remark);
		$data['totaltime'] = $totaltime;
        $data['total'] = 0;
        $data['rcode'] = $rcode;
        $data['rtitle'] = $rtitle;

        $m = M('stage');
        if($type == 'add'){
            $re = $m->where('stagename="%s" and genreid="%d" and isdel=0',$name,$genreid)->find();
            if(empty($re)){
                $maxdata = $m->where('genreid="%d"',$genreid)->max('sortid');
                $data['sortid'] = ($maxdata+1);
                $addid = $m->add($data);
				M('genre')->where('id="%d"',$genreid)->setInc('total');
                $this->recordUserOption('stage','add','id='.$addid);
                $info['status'] = 'ok';
                $info['msg'] = '添加成功';
            }else{
                $info['status'] = 'error';
                $info['msg'] = '已存在';
            }
        }else{
            $m->where('id="%d"',$id)->save($data);
            $this->recordUserOption('stage','edit','id='.$id);
			$re = M('question')->where('stageid="%d" and isdel=0',$id)->select();
			M('stage')->where('id="%d"',$id)->setField('total',count($re));
            $info['status'] = 'ok';
            $info['msg'] = '修改成功';
        }
        $this->ajaxReturn($info);
    }

    //修改关卡名称
    public function editStage(){
        $id = I('id/d',0);
        $stagename = I('stagename/s','');
        $keypoint = I('keypoint/s','');
        $example = I('example/s','');
        $thinking = I('thinking/s','');
		$totaltime = I('totaltime/d',0);

        $data['stagename'] = $stagename;
        $data['keypoint'] = $keypoint;
        $data['example'] = $example;
        $data['thinking'] = $thinking;
		$data['totaltime'] = $totaltime;
        M('stage')->where('id="%d"',$id)->save($data);
        $this->recordUserOption('stage','edit','id='.$id);
        $info['status'] = 'ok';
        $info['msg'] = '修改成功';
        $this->ajaxReturn($info);
    }

    public function sortStageData(){
        $data = I('data/s','');
        $str = str_replace('&quot;', '"', $data);
        $arr = json_decode($str,true);
        
        foreach($arr as $v){
            M('stage')->where('id="%d"',$v['id'])->setField('sortid',$v['sortid']);
        }
        $re['status'] = 'ok';
        $re['msg'] = '排序成功';
        $this->ajaxReturn($re);
    }

    //口算王分类
    public function getAllGenre(){
        $grade = I('grade/s','');
        $term = I('term/s','');

        $data = M('genre')->where('grade="%s" and term="%s" and isdel=0',$grade,$term)->order('sortid')->select();
        $this->ajaxReturn($data);
    }

    //基础库分类获取
    public function getAllGenreBase(){
        $grade = I('grade/s','');
        $term = I('term/s','');
        $subject = I('subject/s','');

        $data = M('base_genre')->where('grade="%s" and term="%s" and subject="%s" and isdel=0',$grade,$term,$subject)->order('sortid')->select();
        $this->ajaxReturn($data);
    }

    public function getStage(){
        $genre = I('genre/d',0);
        $data = M('stage')->where('genreid="%d" and isdel=0',$genre)->order('sortid')->select();
        $this->ajaxReturn($data);
    }

    public function delStep(){
        $id = I('id/d',0);
        $data = M('stage')->where('id="%d"',$id)->setField('isdel',1);
		$re = M('stage')->where('id="%d"',$id)->field('genreid')->find();
		M('genre')->where('id="%d"',$re['genreid'])->setDec('total');
        $this->recordUserOption('stage','del','id='.$id);
        $re['status'] = 'ok';
        $re['msg'] = '删除成功';
        $this->ajaxReturn($re);
    }



    public function getTest(){
        $data = M('base_question')->where('id=10')->select();
        $this->ajaxReturn($data);
    }

    //上传图片
    public function uploadexcel(){
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 51200000000;//50k
        $upload->rootPath = 'upload/excel/';
        $upload->savePath = '';
        $upload->saveName = array('uniqid','');//为空则文件名不变,uniqid生成唯一字符串
        $upload->exts     = array('xlsx');
        $upload->autoSub  = true; //开启子目录
        $upload->subName  = array('date','Ymd'); // 子目录命名方式
        $info = $upload->upload();

        if(!$info) {// 上传错误提示错误信息
			$arr_return["issuc"] = 0;
			$arr_return["msg"] = $upload->getError();
        }else{// 上传成功
            //图片地址
            //var_dump($info);
            $filepath='upload/excel/'.$info["file"]['savepath'].$info["file"]['savename'];
            
            $arr_return["issuc"] = 1;
			$arr_return["msg"] = $filepath;
        }        
        $this->ajaxReturn($arr_return);
    }


    public function downloadExcel(){
        $filename='import.xlsx';
        // $filename=iconv('utf-8', 'gbk', $filename);
        $filesize = filesize($filename);
        header( "Content-Type: application/force-download;charset=utf-8");
        header( "Content-Disposition: attachment; filename= ".$filename);
        header( "Content-Length: ".$filesize);
        ob_clean();
        readfile($filename);
    }

    public function downloadExcelTiankong(){
        $filename='importtk.xlsx';
        // $filename=iconv('utf-8', 'gbk', $filename);
        $filesize = filesize($filename);
        header( "Content-Type: application/force-download;charset=utf-8");
        header( "Content-Disposition: attachment; filename= ".$filename);
        header( "Content-Length: ".$filesize);
        ob_clean();
        readfile($filename);
    }

        /**
     * 下载excel
     */
    public function makeExcel(){
    	$genreid=I('genreid/d',0);
        vendor('PHPExcel');
        
    	//var_dump($data);
    	$objExcel = new \PHPExcel();
    	$objWriter = new \PHPExcel_Writer_Excel2007($objExcel);

    	$objExcel->setActiveSheetIndex(0);
    	$objActSheet = $objExcel->getActiveSheet();
    	$objActSheet->setTitle('选择题导入');

    	$objActSheet->setCellValue('A1','分类ID');
    	$objActSheet->setCellValue('B1','题干');
    	$objActSheet->setCellValue('C1','内容');
    	$objActSheet->setCellValue('D1','答案选项');

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
    public function importExcelData(){
        $filepath = I('filepath/s','');
        $genreid = I('genreid/d',0);
    	vendor('PHPExcel');

        if(!file_exists($filepath)){
            $re['status'] = 'err';
            $re['msg'] = '文件不存在！';
            $this->ajaxReturn($re);
            exit;
        }

    	$arr_excel = readExcel($filepath);

    	$insert_flag = true; //是否写数据库标志
    	$errorinfo = array(); //错误信息数组
    	$success_num = 0; //导入成功数

        $question = M('base_question');

    	foreach ($arr_excel as $row=>$v){
    		$quesName=$v['Column']['A']; //题干
    		$quesContent=$v['Column']['B'];//内容 可以为空
            $options=$v['Column']['C'];//选项
            $options = trim(trim($options),'#');
    		$answer=$v['Column']['D'];//正确答案

    		/**
    		 * 判断excel里是否有空值
    		 */
    		if ($quesName == ""){
    			array_push($errorinfo, '题干不能为空' . '|' . $row); //记录错误信息
    			continue;//如果当前算式为空，跳出本次循环
    		}
    		if ($options == ""){
    			array_push($errorinfo, '选项不能为空' . '|' . $row); //记录错误信息
    			continue;//如果当前结果为空，跳出本次循环
    		}
    		if ($answer == ""){
    			array_push($errorinfo, '正确答案不能为空' . '|' . $row); //记录错误信息
    			continue;//如果当前结果为空，跳出本次循环
            }
            
            if(empty($quesContent)){
                $quesContent='';
            }

    		/**
    		 * 如果以上没有空值的话继续执行
    		 */

            $arr = explode('#',$options);
            $flagArr = array('A','B','C','D','E','F','G');

            $arrOption = array();
            $rightAnswer = '';
            foreach($arr as $kk=>$vv){
                $answerArr['content'] = $vv;
                $answerArr['flag'] = $flagArr[$kk];
                array_push($arrOption,$answerArr);
                if($vv==$answer){
                    $rightAnswer = $flagArr[$kk];
                }
            }

            if($rightAnswer==''){
                array_push($errorinfo, '正确答案不在所给的选项中' . '|' . $row); //记录错误信息
    			continue;//如果当前结果为空，跳出本次循环
            }

            $data['answer'] = $rightAnswer;//转化为ABCD
            $data['answerFlag'] = 1;//1单选2多选
            $data['quesType'] = 1;
            $data['quesName'] = $quesName;
            $data['quesContentType'] = 1;
            $data['quesContent'] = $quesContent;
            $data['quesChoiceType']= 1;
            $data['quesChoice'] = $arrOption;
            

            $info['questype'] = 1;
            $info['genreid'] = $genreid;
            $info['content'] = json_encode($data);


            $question->add($info);

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
     * 批量excel
     */
    public function importExcelDataTianKong(){
        $filepath = I('filepath/s','');
        $genreid = I('genreid/d',0);
    	vendor('PHPExcel');

        if(!file_exists($filepath)){
            $re['status'] = 'err';
            $re['msg'] = '文件不存在！';
            $this->ajaxReturn($re);
            exit;
        }

    	$arr_excel = readExcel($filepath);

    	$insert_flag = true; //是否写数据库标志
    	$errorinfo = array(); //错误信息数组
    	$success_num = 0; //导入成功数

        $question = M('base_question');

    	foreach ($arr_excel as $row=>$v){
    		$quesName=$v['Column']['A']; //题干 可以为空
    		$quesContent=$v['Column']['B'];//算式
            $type=$v['Column']['C'];//内容类型1普通算式2分数3余数
    		$answer=$v['Column']['D'];//正确答案 多个用竖杠分割

    		/**
    		 * 判断excel里是否有空值
    		 */
    		if ($type == ""){
    			array_push($errorinfo, '内容类型不能为空' . '|' . $row); //记录错误信息
    			continue;//如果当前算式为空，跳出本次循环
    		}
    		if ($quesContent == ""){
    			array_push($errorinfo, '算式不能为空' . '|' . $row); //记录错误信息
    			continue;//如果当前结果为空，跳出本次循环
    		}
    		if ($answer === ""){
    			array_push($errorinfo, '正确答案不能为空' . '|' . $row); //记录错误信息
    			continue;//如果当前结果为空，跳出本次循环
            }
            
            if(empty($quesName)){
                $quesName='';
            }


			$questionType = 2;
			$questionName = $quesName;
			$contentType = $type;
			$contentName = $quesContent;

			$contentName = trim($contentName);
			$contentName = str_replace('（','(',$contentName);
			$contentName = str_replace('）',')',$contentName);

			$contentName=preg_replace('/\s+/','',$contentName);
			// preg_match_all('/\[(.*?)\]/',$contentName, $out, PREG_SET_ORDER);

			// if(!empty($out)){
			//     foreach($out as $k=>$v){
			//         $answer[$k] = $v[1];
			//     }
			// }
			
			$answerArr = json_decode($answer,true);

			$data['answerFlag'] = 1; //单选题
			$arrAnswer = explode('|',$answer);
			$data['answer'] = $arrAnswer;

			
			// $contentName = preg_replace('/\[(.*?)\]/','#',$contentName);
			$contentName = preg_replace('/\{(.*?)\}/','#',$contentName);
			
			$arr = split_str($contentName);

			$str = '';
			$fuhao = '+-×÷/()|[]{}#=';
			$suanshiArr = array();
			$count = count($arr);
			foreach($arr as $k=>$v){
				if(trim($v)==''){
					continue;
				}else{
					$index = strpos($fuhao,$v);
				   if($index===false){
					   //匹配失败
					   $str .= $v;
					   if($k==($count-1)){
						array_push($suanshiArr,$str);
					   }
				   }else{
					   if($str!==''){
						array_push($suanshiArr,$str);
					   }
					   $str = '';
					   array_push($suanshiArr,$v);
				   }
				  
				}
			}
			
			// var_dump($arr);
			// var_dump($contentName);
			// var_dump($suanshiArr);exit;

			foreach($suanshiArr as $k=>$v){
				if($v == '/'){
					$fenshu = array($suanshiArr[$k-1],$suanshiArr[$k],$suanshiArr[$k+1]);
					unset($suanshiArr[$k-1]);
					unset($suanshiArr[$k+1]);
					$suanshiArr[$k] = $fenshu;
				}
			}

			// $of = array_search('/',$suanshiArr);
			// if($of !==false){
			//     $fenshu = array($suanshiArr[$of-1],$suanshiArr[$of],$suanshiArr[$of+1]);
			//     unset($suanshiArr[$of-1]);
			//     unset($suanshiArr[$of+1]);
			//     $suanshiArr[$of] = $fenshu;
			// }

			// var_dump($suanshiArr);
			// exit;

			$data['quesType'] = $questionType;
			$data['quesName'] = $questionName;
			$data['quesContentType'] = $contentType;
			$data['quesContent'] = $suanshiArr;

			$info['questype'] = $questionType;
			$info['genreid'] = $genreid;
			$info['content'] = json_encode($data);


            $addid = $question->add($info);
			$this->recordUserOption('base_question','add','id='.$addid);
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
     * 批量excel
     */
    public function importExcelData1111(){
        $filepath = I('filepath/s','');
        $genreid = I('genreid/d',0);
    	vendor('PHPExcel');

        if(!file_exists($filepath)){
            $re['status'] = 'err';
            $re['msg'] = '文件不存在！';
            $this->ajaxReturn($re);
            exit;
        }

    	$arr_excel = readExcel($filepath);

    	$insert_flag = true; //是否写数据库标志
    	$errorinfo = array(); //错误信息数组
    	$success_num = 0; //导入成功数

        $question = M('base_question');

    	foreach ($arr_excel as $row=>$v){
    		$quesName=$v['Column']['A']; //题干
    		$quesContent=$v['Column']['B'];//内容 可以为空
            $options=$v['Column']['C'];//选项
            $options = trim(trim($options),'#');
    		$answer=$v['Column']['D'];//正确答案

    		/**
    		 * 判断excel里是否有空值
    		 */
    		if ($quesName == ""){
    			array_push($errorinfo, '题干不能为空' . '|' . $row); //记录错误信息
    			continue;//如果当前算式为空，跳出本次循环
    		}
    		if ($options == ""){
    			array_push($errorinfo, '选项不能为空' . '|' . $row); //记录错误信息
    			continue;//如果当前结果为空，跳出本次循环
    		}
    		if ($answer == ""){
    			array_push($errorinfo, '正确答案不能为空' . '|' . $row); //记录错误信息
    			continue;//如果当前结果为空，跳出本次循环
    		}

    		/**
    		 * 如果以上没有空值的话继续执行
    		 */


            $contentName=preg_replace('/\s+/','',$contentName);
        
            // $contentName = preg_replace('/\[(.*?)\]/','#',$contentName);
            $contentName = preg_replace('/\{(.*?)\}/','#',$contentName);
        
            $arr = split_str($contentName);
    
            $str = '';
            $fuhao = '+-×÷/()|[]#=';
            $suanshiArr = array();
            $count = count($arr);
            foreach($arr as $k=>$v){
                if(trim($v)==''){
                    continue;
                }else{
                    $index = strpos($fuhao,$v);
                   if($index===false){
                       //匹配失败
                       $str .= $v;
                       if($k==($count-1)){
                        array_push($suanshiArr,$str);
                       }
                   }else{
                       if(!empty($str)){
                        array_push($suanshiArr,$str);
                       }
                       $str = '';
                       array_push($suanshiArr,$v);
                   }
                  
                }
            }
            foreach($suanshiArr as $k=>$v){
                //分数和余数都是数组
                if($v == '/'){
                    $fenshu = array($suanshiArr[$k-1],$suanshiArr[$k],$suanshiArr[$k+1]);
                    unset($suanshiArr[$k-1]);
                    unset($suanshiArr[$k+1]);
                    $suanshiArr[$k] = $fenshu;
                }
            }

            $answerArr = array();
            $tmpArr = explode('|',$answer);
            if(count($tmpArr)>1){
                foreach($tmpArr as $v){
                    $arr_answer = explode(',',$v);
                    array_push($answerArr,$arr_answer);
                }
                $data['answerFlag'] = 2;
            }else{
                $answerArr = explode(',',$tmpArr[0]);
                $data['answerFlag'] = 1;
            }
            $data['answer'] = $answerArr;
            $data['quesType'] = 2;
            $data['quesName'] = $quesName;
            $data['quesContentType'] = $quesContentType;
            $data['quesContent'] = $suanshiArr;
    
            $info['questype'] = 2;
            $info['stageid'] = $stageid;
            $info['content'] = json_encode($data);


            $question->add($info);

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



    //删除基础库的
    public function delBaseQuestionByid(){
        $id = I('id/d',0);
        $data = M('question')->where('quesid="%d" and isdel=0',$id)->find();
        if(empty($data)){
            M('base_question')->where('id="%d"',$id)->setField('isdel',1);
            $this->recordUserOption('base_question','del','id='.$id);
            $info['status'] = 'ok';
            $info['msg'] = '删除成功';
        }else{
            $stageid = $data['stageid'];
            $data_stage = M('stage')->where('id="%d" and isdel=0',$stageid)->find();
            $genreid = $data_stage['genreid'];
            $data_genre = M('genre')->where('id="%d" and isdel=0',$genreid)->find();
            $info['status'] = 'error';
            $grade = $this->getGradeName($data_genre['grade']);
            $term = $this->getTermName($data_genre['term']);
            $info['status'] = 'error';
            $info['msg'] = $grade.'-'.$term.'-'.$data_genre['name'].'-'.$data_stage['stagename'].'使用此题，无法删除!';
        }
        $this->ajaxReturn($info);
    }

    public function getGradeName($num){
        if ($num == '0001') {
            $gradeName = '一年级';
        } else if ($num == '0002') {
            $gradeName = '二年级';
        } else if ($num == '0003') {
            $gradeName = '三年级';
        } else if ($num == '0004') {
            $gradeName = '四年级';
        } else if ($num == '0005') {
            $gradeName = '五年级';
        } else if ($num == '0006') {
            $gradeName = '六年级';
        }
        return $gradeName;
    }

    public function getTermName($num) {
        if ($num == '0001') {
          $termName = '上学期';
        } else if ($num == '0002') {
          $termName = '下学期';
        }
        return $termName;
      }
  

    //删除口算卡关卡中的试题
    public function delQuestionByid(){
        $id = I('id/d',0);
        M('question')->where('id="%d"',$id)->setField('isdel',1);
        $data = M('question')->field('stageid')->where('id="%d"',$id)->find();
        $stageid = $data['stageid'];
        $re = M('question')->where('stageid="%d" and isdel=0',$stageid)->select();
        M('stage')->where('id="%d"',$stageid)->setField('total',count($re));
        $this->recordUserOption('question','del','id='.$id);
    }

    //查看分类下是否有关卡
    public function checkHasStep(){
        $genreid = I('genreid/d',0);

        $re = M('genre')->where('id="%d"',$genreid)->find();

        $genreid = $re['id'];

        $data = M('stage')->where('genreid="%d" and isdel=0',$genreid)->select();

        if(empty($data)){
            // if($re['id']!= $re['gid']){
            //     //其他年级也引用此分类
            //     $info['status'] = 'ok';
            //     $info['msg'] = '其他年级共用此分类，确定删除？';
            // }else{
            //     $info['status'] = 'ok';
            //     $info['msg'] = '确定要删除此分类？';
            // }

            $info['status'] = 'ok';
            $info['msg'] = '确定要删除此分类？';
        }else{
            $info['status'] = 'error';
            $info['msg'] = '分类下有关卡,不能删除';
        }
        $this->ajaxReturn($info);
    }
    //查看关卡下是否有试题
    public function checkHasQues(){
        $stageid = I('id/d',0);
        $data = M('question')->where('stageid="%d" and isdel=0',$stageid)->select();

        if(empty($data)){
            $info['status'] = 'ok';
            $info['msg'] = '无试题,可以删除';
        }else{
            $info['status'] = 'error';
            $info['msg'] = '关卡下有试题,不能删除';
        }
        $this->ajaxReturn($info);
    }

    //查看关卡下是否有试题
    public function checkHasQuesFromBase(){
        $genreid = I('id/d',0);
        $data = M('base_question')->where('genreid="%d" and isdel=0',$genreid)->select();

        if(empty($data)){
            $info['status'] = 'ok';
            $info['msg'] = '无试题,可以删除';
        }else{
            $info['status'] = 'error';
            $info['msg'] = '分类下有试题,不能删除';
        }
        $this->ajaxReturn($info);
    }


    //发布或者取消发布
    public function isshowGenre(){
        $id = I('id/d');
        $isshow = I('isshow/d',0);

        $data_stage = M('stage')->where('genreid="%d" and isdel=0',$id)->select();

        $flag = true;//可以发布
        if(empty($data_stage)){
            //分类下没有关卡，禁止发布
            $info['status'] = 'error';
            $info['msg'] = '分类下没有关卡，禁止发布';
            $flag = false;
        }else{
            foreach($data_stage as $k=>$v){
                $data_ques = M('question')->where('stageid="%d" and isdel=0',$v['id'])->select();
                if(empty($data_ques)){
                    //关卡下没有试题
                    $info['status'] = 'error';
                    $info['msg'] = $v['stagename'].'下没有试题，禁止发布';
                    $flag = false;
                    break;
                }else{
                    continue;
                }
            }
        }

        if($flag){
            $re = M('genre')->where('id="%d"',$id)->setField('isshow',$isshow);

            if($re){
                $this->recordUserOption('genre','isshow:'.$isshow,'id='.$id);
                $info['status'] = 'ok';
                $info['msg'] = '发布成功';
            }else{
                $info['status'] = 'error';
                $info['msg'] = '发布失败';
            }
        }
        $this->ajaxReturn($info);
    }

    //添加用户
    public function addUserData(){
        $id = I('id/d',0);
        $type = I('type/s','');
        $username = I('username/s','');
        $truename = I('truename/s','');
        $password = I('password/s','');
        $data['username'] = $username;
        $data['truename'] = $truename;
        $data['pwd'] = md5($password);


        $m = M('user_admin');
        if($type == 'add'){
            $re = $m->where('username="%s"',$username)->find();
            
            if(empty($re)){
                $m->add($data);
                $info['status'] = 'ok';
                $info['msg'] = '添加成功';
            }else{
                $info['status'] = 'error';
                $info['msg'] = '已存在';
            }
        }else{
            $m->where('id="%d"',$id)->save($data);
            $info['status'] = 'ok';
            $info['msg'] = '保存成功';
        }
        $this->ajaxReturn($info);
    }

    //获取所有用户
    public function getUserList(){
        if(session('ifadmin') == 1){
            $data = M('user_admin')->where('username<>"admin"')->select();
        }else{
            $data = M('user_admin')->where('id="%d"',session('userId'))->select();
        }
        
        $this->ajaxReturn($data);
    }

    //获取用户信息
    public function getUserInfo(){
        $id = I('id/d',0);
        $data = M('user_admin')->where('id="%d"',$id)->find();
        $this->ajaxReturn($data);
    }

    //删除用户
    public function delUser(){
        $id = I('id/d',0);
        M('user_admin')->where('id="%d"',$id)->delete();
    }

    public function recordUserOption($tablename,$option,$tableid){
        $dealtime = date('Y-m-d H:i:s');

        $m = M('user_history');
        $data['username'] = session('userName');
        $data['tablename'] = $tablename;
        $data['option'] = $option;
        $data['tableid'] = $tableid;
        $data['dealtime'] = $dealtime;

        $m->add($data);
    }

    public function getUserName(){
        $data['truename'] = session('trueName');
        $data['ifadmin'] = session('ifadmin');
        $this->ajaxReturn($data);
    }

    public function getjson(){
        $json = file_get_contents('a.json');
        // echo $json;
        $arr = json_decode($json,true);
        // var_dump($arr);
        $txt = 'a.txt';
        foreach($arr as $v){
            $grade = $v['title'];
            foreach($v['subjects'] as $vv){
                $subject = $vv['title'];
                foreach($vv['modules'] as $vvv){
                    $modulename = $vvv['title'];
                    $url = $vvv['url'];
                    $str = $grade.'|'.$subject.'|'.$modulename.'|'.$url;
                    file_put_contents($txt,$str.PHP_EOL,FILE_APPEND);
                }
            }
        }
    }

    public function getTreeNodesBase(){
        $id=I('id/s','');
        $idlist=I('idlist/s','');
        $pId = I('pId/s','');
        $level = I('level/d',0);
        $type = I('type/s','');//genre：分类页面
        $data_mulu = array();
        

        if($id==''){
            array_push($data_mulu,array('id'=>'0001','pId'=>'000','isParent'=>'true','name'=>'一年级','idlist'=>'000-0001'));
            array_push($data_mulu,array('id'=>'0002','pId'=>'000','isParent'=>'true','name'=>'二年级','idlist'=>'000-0002'));
            array_push($data_mulu,array('id'=>'0003','pId'=>'000','isParent'=>'true','name'=>'三年级','idlist'=>'000-0003'));
            array_push($data_mulu,array('id'=>'0004','pId'=>'000','isParent'=>'true','name'=>'四年级','idlist'=>'000-0004'));
            array_push($data_mulu,array('id'=>'0005','pId'=>'000','isParent'=>'true','name'=>'五年级','idlist'=>'000-0005'));
            array_push($data_mulu,array('id'=>'0006','pId'=>'000','isParent'=>'true','name'=>'六年级','idlist'=>'000-0006'));
            array_push($data_mulu,array('id'=>'0007','pId'=>'000','isParent'=>'true','name'=>'七年级','idlist'=>'000-0007'));
            array_push($data_mulu,array('id'=>'0008','pId'=>'000','isParent'=>'true','name'=>'八年级','idlist'=>'000-0008'));
            array_push($data_mulu,array('id'=>'0009','pId'=>'000','isParent'=>'true','name'=>'九年级','idlist'=>'000-0009'));

        }else{
            if($level==0){
                array_push($data_mulu,array('id'=>'0001','pId'=>$id,'isParent'=>'true','name'=>'上学期','idlist'=>$idlist.'-0001'));
                array_push($data_mulu,array('id'=>'0002','pId'=>$id,'isParent'=>'true','name'=>'下学期','idlist'=>$idlist.'-0002'));
                array_push($data_mulu,array('id'=>'0000','pId'=>$id,'isParent'=>'true','name'=>'全一册','idlist'=>$idlist.'-0000'));
            }else if($level == 1){
                //列学科
                if($type == 'genre'){
                    $isParent = 'false';
                }else{
                    $isParent = 'true';
                }
                array_push($data_mulu,array('id'=>'0002','pId'=>$id,'isParent'=>$isParent,'name'=>'数学','idlist'=>$idlist.'-0002'));
                array_push($data_mulu,array('id'=>'0005','pId'=>$id,'isParent'=>$isParent,'name'=>'化学','idlist'=>$idlist.'-0005'));
                array_push($data_mulu,array('id'=>'0011','pId'=>$id,'isParent'=>$isParent,'name'=>'地理','idlist'=>$idlist.'-0011'));
                array_push($data_mulu,array('id'=>'0012','pId'=>$id,'isParent'=>$isParent,'name'=>'政治','idlist'=>$idlist.'-0012'));
                array_push($data_mulu,array('id'=>'0013','pId'=>$id,'isParent'=>$isParent,'name'=>'历史','idlist'=>$idlist.'-0013'));
                array_push($data_mulu,array('id'=>'0006','pId'=>$id,'isParent'=>$isParent,'name'=>'音乐','idlist'=>$idlist.'-0006'));
                array_push($data_mulu,array('id'=>'0010','pId'=>$id,'isParent'=>$isParent,'name'=>'生物','idlist'=>$idlist.'-0010'));
            }else if($level == 2){
                //列分类
                list($top,$grade,$term,$subject) = explode('-',$idlist);
                if($type == 'step'){
                    $isParent = 'false';
                    $m = M('genre');
                }else if($type == 'quesbase'){
                    $isParent = 'false';
                    $m = M('base_genre');
                }else if($type == 'ques'){
                    $isParent = 'true';
                    $m = M('genre');
                }
                
                $data = $m->where('grade="%s" and term="%s" and subject="%s" and isdel=0 ',$grade,$term,$subject)->order('sortid')->select();
                foreach ($data as $v){
                    // if($type == 'quesbase'){
                    //     $arr = array('id'=>$v['id'],'pId'=>$id,'isParent'=>$isParent,'name'=>$v['name'],'idlist'=>$idlist.'-'.$v['id']);
                    // }else{
                    //     $arr = array('id'=>$v['gid'],'pId'=>$id,'isParent'=>$isParent,'name'=>$v['name'],'idlist'=>$idlist.'-'.$v['gid']);
                    // }
                    $arr = array('id'=>$v['id'],'pId'=>$id,'isParent'=>$isParent,'name'=>$v['name'],'idlist'=>$idlist.'-'.$v['id']);
                    array_push($data_mulu,$arr);
                }
            }else if($level == 3){
                //关卡
                $m = M('stage');
                $data = $m->where('genreid="%d" and isdel=0',$id)->order('sortid')->select();
                foreach($data as $v){
                    $arr = array('id'=>$v['id'],'pId'=>$id,'isParent'=>'false','name'=>$v['stagename'],'idlist'=>$idlist.'-'.$v['id']);
                    array_push($data_mulu,$arr);
                }
            }
        }
        
        $json="[";
        foreach ($data_mulu as $v){
            $json.='{id:"'.$v['id'].'", pId:"'.$v['pId'].'",isParent:"'.$v['isParent'].'", name:"'.$v['name'].'",idlist:"'.$v['idlist'].'"},';
        }
        $json = rtrim($json,',');
        $json = $json.']';

        echo $json;
    }

    public function getTreeNodes(){
        $id=I('id/s','');
        $pId = I('pId/s','');
        $level = I('level/d',0);
        $data_mulu = array();
        

        if($id==''){
            array_push($data_mulu,array('id'=>'0001','pId'=>'000','isParent'=>'true','name'=>'一年级'));
            array_push($data_mulu,array('id'=>'0002','pId'=>'000','isParent'=>'true','name'=>'二年级'));
            array_push($data_mulu,array('id'=>'0003','pId'=>'000','isParent'=>'true','name'=>'三年级'));
            array_push($data_mulu,array('id'=>'0004','pId'=>'000','isParent'=>'true','name'=>'四年级'));
            array_push($data_mulu,array('id'=>'0005','pId'=>'000','isParent'=>'true','name'=>'五年级'));
            array_push($data_mulu,array('id'=>'0006','pId'=>'000','isParent'=>'true','name'=>'六年级'));

        }else{
            if($level==0){
                array_push($data_mulu,array('id'=>'0001','pId'=>$id,'isParent'=>'true','name'=>'上学期'));
                array_push($data_mulu,array('id'=>'0002','pId'=>$id,'isParent'=>'true','name'=>'下学期'));
            }else if($level == 1){
                $data = M('genre')->where('grade="%s" and term="%s" and isdel=0',$pId,$id)->order('sortid')->select();
                foreach ($data as $v){
                    $arr = array('id'=>$v['id'],'pId'=>$id,'isParent'=>'true','name'=>$v['name']);
                    array_push($data_mulu,$arr);
                }
            }else if($level==2){
                $data = M('stage')->where('genreid="%d" and isdel=0',$id)->order('sortid')->select();
                $data_info = M('genre')->where('id="%d" and isdel=0',$id)->find();
                foreach ($data as $v){
                    $arr = array('id'=>$v['id'],'pId'=>$id,'isParent'=>'false','name'=>$v['stagename'],'grade'=>$data_info['grade'],'term'=>$data_info['term']);
                    array_push($data_mulu,$arr);
                }
            }
        }
        
        $json="[";
        foreach ($data_mulu as $v){
            if(empty($v['grade'])){
                $json.='{id:"'.$v['id'].'", pId:"'.$v['pId'].'",isParent:"'.$v['isParent'].'", name:"'.$v['name'].'"},';
            }else{
                $json.='{id:"'.$v['id'].'", pId:"'.$v['pId'].'",isParent:"'.$v['isParent'].'", name:"'.$v['name'].'",grade:"'.$v['grade'].'",term:"'.$v['term'].'"},';
            }
        }
        $json = rtrim($json,',');
        $json = $json.']';

        echo $json;
    }


    public function importFromDb(){

        //二年级
        $arr[17]=8;
        $arr[18]=25;
        $arr[19]=26;
        $arr[20]=27;
        $arr[21]=28;
        $arr[22]=29;
        $arr[23]=30;
        $arr[24]=31;

        //三年级
        $arr[25]=32;
        $arr[26]=33;
        $arr[27]=34;
        $arr[28]=35;
        $arr[29]=36;
        $arr[30]=37;
        $arr[31]=38;
        $arr[32]=39;
        $arr[33]=40;
        $arr[34]=41;
        $arr[36]=42;
        $arr[37]=43;
        $arr[38]=44;
        $arr[39]=45;

        $data_math = M('math')->where('grade="0002" and mathtype=1')->select();
        $data_math = M('math')->where('id=24')->select();
        for($i=0;$i<count($data_math);$i++){

            $genreid = $arr[$data_math[$i]['id']];//目标分类

            $data = M('mathinfo')->where('mathid="%d"',$data_math[$i]['id'])->select();

            
            foreach($data as $k=>$v){
                if($data_math[$i]['type']==3){
                    //余数
                    $contentType = 3;
                    list($a,$b) = explode('/',$v['result']);

                    $str = $v['equation'].'={'.$a.'}|{'.$b.'}';
                }else if($data_math[$i]['type']==2){
                    //分数
                    $contentType = 2;
                    list($a,$b) = explode('/',$v['result']);

                    $str = $v['equation'].'={'.$a.'}/{'.$b.'}';
                }else if($data_math[$i]['type']==1){
                    //普通
                    $contentType = 1;//普通算式
                    $str = $v['equation'].'={'.$v['result'].'}';
                }
                
                $this->addMathDataBaseCopy('add',0,$genreid,2,'',$contentType,$str,$v['result']);
            }
        }
    }



	public function importTing(){
		$data_math = M('math')->where('mathtype=2')->select();
        for($i=0;$i<count($data_math);$i++){
			$genreid = $data_math[$i]['id'];//目标分类
            $data = M('mathinfo')->where('mathid="%d"',$data_math[$i]['id'])->select();

            foreach($data as $k=>$v){
                if($data_math[$i]['type']==3){
                    //余数
                    $contentType = 3;
                    list($a,$b) = explode('/',$v['result']);

                    $str = '{'.$a.'}|{'.$b.'}';
                }else if($data_math[$i]['type']==2){
                    //分数
                    $contentType = 2;
                    list($a,$b) = explode('/',$v['result']);

                    $str = '{'.$a.'}/{'.$b.'}';
                }else if($data_math[$i]['type']==1){
                    //普通
                    $contentType = 1;//普通算式
                    $str = '{'.$v['result'].'}';
                }
                $id = $v['id'];
                // echo $id;exit;
                // $this->addMathDataBaseCopy('add',0,$genreid,2,'',$contentType,$str,$v['result']);
                $this->addMathDataBaseCopy('save',$id,$genreid,2,'',$contentType,$str,$v['result']);
            }
            // break;
        }
	}

    public function addMathDataBaseCopy($type,$id,$genreid,$questionType,$questionName,$contentType,$contentName,$answer){

        $contentName = trim($contentName);
        $contentName = str_replace('（','(',$contentName);
        $contentName = str_replace('）',')',$contentName);

        $contentName=preg_replace('/\s+/','',$contentName);

        $data['answerFlag'] = 1; //单选题

        $abc = explode('/',$answer);
        if(count($abc)>1){
            $arrAnswer = $abc;
        }else{
            $arrAnswer = array($answer);
        }
        
        $data['answer'] = $arrAnswer;   

// var_dump($contentName);    
        // $contentName = preg_replace('/\[(.*?)\]/','#',$contentName);
        $contentName = preg_replace('/\{(.*?)\}/','#',$contentName);
// var_dump($contentName);        
        $arr = split_str($contentName);
// var_dump($arr);
        $str = '';
        $fuhao = '+-×÷/()|[]{}#=';
        $suanshiArr = array();
        $count = count($arr);
        foreach($arr as $k=>$v){
            if(trim($v)==''){
                continue;
            }else{
                $index = strpos($fuhao,$v);
               if($index===false){
                   //匹配失败
                   $str .= $v;
                   if($k==($count-1)){
                    array_push($suanshiArr,$str);
                   }
               }else{
                   if($str!==''){
                    array_push($suanshiArr,$str);
                   }
                   $str = '';
                   array_push($suanshiArr,$v);
               }
              
            }
        }
        
// var_dump($suanshiArr);
        foreach($suanshiArr as $k=>$v){
            if($v == '/'){
                $fenshu = array($suanshiArr[$k-1],$suanshiArr[$k],$suanshiArr[$k+1]);
                unset($suanshiArr[$k-1]);
                unset($suanshiArr[$k+1]);
                $suanshiArr[$k] = $fenshu;
            }
        }
// var_dump($suanshiArr);
// exit;
        
        $data['quesType'] = $questionType;
        $data['quesName'] = $questionName;
        $data['quesContentType'] = $contentType;
        $data['quesContent'] = $suanshiArr;


        $info['questype'] = $questionType;
        $info['genreid'] = $genreid;
        $info['content'] = json_encode($data);

        if($type == 'add'){
            $addid = M('base_question')->add($info);
            $this->recordUserOption('base_question','add','id='.$addid);
        }else{
            // echo $id;exit;
            // var_dump($data);exit;
            M('mathinfo')->where('id="%d"',$id)->setField('content',json_encode($data));
            // M('base_question')->where('id="%d"',$id)->save($info);
            // $this->recordUserOption('base_question','edit','id='.$id);
        }

    }



	//预览关卡
	public function getQuesView(){
        $stageid = I('stageid/d',0);
		
        $sql_ques = 'SELECT t.* FROM (SELECT quesid FROM sx_question  WHERE stageid='.$stageid.' and isdel=0 order by sortid) l  LEFT JOIN sx_base_question t ON  l.quesid=t.id';
		$sql_stage = 'SELECT remark,totaltime FROM sx_stage WHERE id ='.$stageid;
			
        $data_ques = M()->query($sql_ques);
		$data_stage = M()->query($sql_stage);
	

		$sql = 'SELECT subject FROM sx_genre WHERE id=(SELECT genreid FROM sx_stage WHERE id= '.$stageid.')';
		$re = M()->query($sql);
		if($re[0]['subject'] == '0002'){
			if(count($data_ques)<15){
				//不够15道题
				$size = floor(15/count($data_ques));
				$data_ques_new = array();
				$data_ques_new = array_merge($data_ques_new,$data_ques);
				for($i=0;$i<$size;$i++){
					$data_ques_new = array_merge($data_ques_new,$data_ques);
				}
				if(count($data_ques_new)>15){
					shuffle($data_ques_new);
					array_splice($data_ques_new,15,count($data_ques_new)-15);	
				}
				$data_ques = $data_ques_new;
			}else if(count($data_ques)>15){
				shuffle($data_ques);
				array_splice($data_ques,15,count($data_ques)-15);
			}else{
				//刚好15道题
				shuffle($data_ques);
			}		
		}


		$info['ques'] = $data_ques;
		$info['stage'] = $data_stage;
        $this->ajaxReturn($info);
	}

	//预览试题
	public function getQuesAloneView(){
        $quesid = I('quesid/d',0);
		
        $sql_ques = 'SELECT * FROM sx_base_question  where  id='.$quesid;
			
        $data_ques = M()->query($sql_ques);
		$data_stage[0]['remark']='';
		$data_stage[0]['totaltime']=180;
	

		$info['ques'] = $data_ques;
		$info['stage'] = $data_stage;
        $this->ajaxReturn($info);
	}

    public function getResTreeNodes(){
        $id=I('id/s','');
        $m=M('',null,'mysql://vcom:2012rmsedu@192.168.151.50/RMS_Data');
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
    public function queryRes(){
        $ks_code=I('ks_code/s',0);
        $Model=M('',null,'mysql://vcom:2012rmsedu@192.168.151.50/RMS_Data');
        $sql="SELECT ro.R_CODE,ro.R_TITLE,se.c1 FROM RMS_RESOURCEINFO ro,SHARE_KNOWLEDGE_STRUCTURE se WHERE ro.R_KS_ID=se.KS_CODE AND ro.R_STATE='1' and R_FORMAT_MARK='mp4' AND (ro.R_HASHCODE='' OR ro.R_HASHCODE IS NULL) AND se.KS_CODE='%s' ORDER BY ro.R_EXT5";
        //echo $sql;
        $data=$Model->query($sql,$ks_code);
        $this->ajaxReturn($data);
    }
    public function queryRes2(){
        $search_value=I('search_value/s',0);
        $Model=M('',null,'mysql://vcom:2012rmsedu@192.168.151.50/RMS_Data');
        $sql="SELECT ro.R_CODE,ro.R_TITLE,se.c1 FROM RMS_RESOURCEINFO ro,SHARE_KNOWLEDGE_STRUCTURE se WHERE ro.R_KS_ID=se.KS_CODE AND ro.R_STATE='1' and R_FORMAT_MARK='mp4' AND (ro.R_HASHCODE='' OR ro.R_HASHCODE IS NULL) AND ro.R_TITLE like '%".$search_value."%' ORDER BY ro.R_EXT5";
        //echo $sql;
        $data=$Model->query($sql);
        $this->ajaxReturn($data);
    }




}