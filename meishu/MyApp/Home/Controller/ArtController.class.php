<?php
namespace Home\Controller;
use Think\Controller;
class ArtController extends Controller {
    /**
     * 背景音乐管理
     */
    public function music(){
        $Model=M('music_group');
        $data=$Model->field('id,title')->select();
        $this->assign('music_type',$data);
        $Model_music_info=M();
        $sql="SELECT yp.title,yo.id,yo.music_name,yo.music_filename,yo.music_filepath FROM art_music_info yo,art_music_group yp WHERE yo.groupid=yp.id";
        $data_info=$Model_music_info->query($sql);
        $this->assign('music_info',$data_info);
        $this->display();
    }

    /**
     * 背景管理
     */
    public function backInfos(){
        $Model=M('music_group');
        $data=$Model->field('id,title')->select();
        $this->assign('music_type',$data);
        $this->display();
    }
    /**
     * 背景添加
     */
    public function addmusicbg(){
        $title=I('title/s','');
        $Model=M('music_group');
        $num=$Model->where('title="%s"',$title)->find();
        //var_dump($num);
        if(is_array($num)){
            echo '已存在';
        }else{
            $data['title']=$title;
            $id=$Model->add($data);
            echo $id;
        }
    }
    /**
     * 背景编辑
     */
    public function editmusicbg(){
        $id=I('id/d',0);
        $title_new=I('title_new/s','');
        $Model=M('music_group');
        $data['title']=$title_new;
        $Model->where('id="%d"',$id)->save($data);
    }
    /**
     * 背景删除
     */
    public function delmusicbg(){
        $id=I('id/d',0);
        $Model_music_info=M('music_info');
        $Model_music_info->where('groupid="%d"',$id)->delete();
        $Model=M('music_group');
        $data=$Model->where('id="%d"',$id)->delete();
        echo $data;
    }
    /**
     * 添加音乐页面
     */
    public function addMusicFiles(){
        $Model=M('music_group');
        $data=$Model->field('id,title')->select();
        $this->assign('music_type',$data);
        $this->display();
    }
    /**
     * 添加音乐
     */
    public function addFile(){
        $groupid=I('groupid/d',0);
        $musicname=I('musicname/s','');
        $filename=I('filename/s','');
        $filepath=I('filepath/s','');
        $Model=M('music_info');
        $re=$Model->where('music_name="%s"',$musicname)->find();
        if(is_array($re)){
            echo '音乐名称已存在';
        }else{
            $data['groupid']=$groupid;
            $data['music_name']=$musicname;
            $data['music_filename']=$filename;
            $data['music_filepath']=$filepath;
            $id=$Model->add($data);
            echo $id;
        }
    }
    /**
     * 编辑音乐界面
     */
    public function editMusicFiles(){
        $id=I('id/d',0);
        $this->assign('id',$id);
        $Modle=M('music_info');
        $data=$Modle->where('id="%d"',$id)->find();
        $this->assign('music_info',$data);
        $Model_group=M('music_group');
        $data_group=$Model_group->field('id,title')->select();
        $this->assign('music_type',$data_group);
        $this->display();
    }
    /**
     * 编辑音乐内容保存
     */
    public function editFile(){
        $id=I('id/d',0);
        $groupid=I('groupid/d',0);
        $musicname=I('musicname/s','');
        $filename=I('filename/s','');
        $filepath=I('filepath/s','');
        $Model=M('music_info');
        if ($filename==''){
            $data['groupid']=$groupid;
            $data['music_name']=$musicname;
            $Model->where('id="%d"',$id)->save($data);
        }else{
            $data['groupid']=$groupid;
            $data['music_name']=$musicname;
            $data['music_filename']=$filename;
            $data['music_filepath']=$filepath;
            $Model->where('id="%d"',$id)->save($data);
        }
    }
    /**
     * 删除音频
     */
    public function delmusicfile(){
        $id=I('id/d',0);//music_info表的id
        $Model=M('music_info');
        $data=$Model->where('id="%d"',$id)->field('music_filepath')->find();
        unlink('uploads/'.$data['music_filepath']);
        $Model->delete($id);

    }
    /**
     * 切换音乐背景列出当前背景下的音乐
     */
    public function changeType(){
        $groupid=I('groupid/d',0);
        $Model=M();
        if($groupid==0){
            $sql="SELECT yp.title,yo.id,yo.music_name,yo.music_filename,yo.music_filepath FROM art_music_info yo,art_music_group yp WHERE yo.groupid=yp.id";
            $data=$Model->query($sql);
        }else{
            $sql="SELECT yp.title,yo.id,yo.music_name,yo.music_filename,yo.music_filepath FROM art_music_info yo,art_music_group yp WHERE yo.groupid=yp.id and yo.groupid='%d'";
            $data=$Model->query($sql,$groupid);
        }
        $this->ajaxReturn($data);
    }
    /**
     * 播放mp3
     */
    public function playMp3(){
        $filename=I('filename/s',0);
        $filepath=I('filepath/s',0);
        //$filepath='uploads/'.$filepath;
        $this->assign('filename',$filename);
        $this->assign('filepath',$filepath);
        $this->display();
    }
    /**
     * 课程管理页面
     */
    public function kecheng(){
        $this->display();
    }

    /**
     * 查询课程版本
     * @return [type] [description]
     */
    public function kcbanben(){
        $nianji = I("nianji/s");
        $xueqi = I("xueqi/s");

        $gradeCode = $this->getGradeCode($nianji);
        switch($xueqi){
            case '上学期':
                $termCode = '0001';
                break;
            case '下学期';
                $termCode = '0002';
                break;
            default:
                $termCode = '';
                break;
        }

        $sql_where = '';
        if($termCode <> ''){
            $sql_where = ' and t.r_volume="'.$termCode.'" ';
        }

        $m = M();
        $sql = "select DISTINCT t.r_version,q.detail_name FROM art_rms_unit t ,art_rms_dictionary q WHERE t.r_version=q.detail_code AND q.dictionary_code='edition' AND  t.r_grade='".$gradeCode."'".$sql_where;
        $data = $m->query($sql);

        $this->ajaxReturn($data);
    }

    /**
     * 查询已有课程单元
     * @return [type] [description]
     */
    public function kckecheng(){
        $nianji = I("nianji/s");
        $xueqi = I("xueqi/s");
        $versionid = I("versionid/s");

        $gradeCode = $this->getGradeCode($nianji);
        $termCode = $xueqi=='上学期'?'0001':'0002';


        $m = M();
        $sql = "select l.id,t.ks_name FROM art_text_kecheng l LEFT JOIN art_rms_unit t ON l.ks_code=t.ks_code WHERE   t.r_grade='".$gradeCode."' and t.r_volume='".$termCode."' AND t.r_subject='0001' AND t.r_version='".$versionid."' order by t.display_order";

        $data = $m->query($sql);

        $this->ajaxReturn($data);
    }


    public function kallKecheng(){
        $nianji = I('nianji/s');
        $xueqi = I("xueqi/s");
        $versionid = I("versionid/s");

        $gradeCode = $this->getGradeCode($nianji);
        $termCode = $xueqi=='上学期'?'0001':'0002';
        $sql = "select ks_name,ks_code from art_rms_unit where r_grade='".$gradeCode."' and r_volume='".$termCode."'  and r_version='".$versionid."' order by display_order";
        $m = M();
        $data = $m->query($sql);
        $this->ajaxReturn($data);
    }


    public function getGradeCode($gradeName){
        switch($gradeName){
            case '一年级':
                return '0001';
                break;
            case '二年级':
                return  '0002';
                break;
            case '三年级':
                return  '0003';
                break;
            case '四年级':
                return  '0004';
                break;
            case '五年级':
                return  '0005';
                break;
            case '六年级':
                return  '0006';
                break;
        }
    }

    /**
     * 添加课程页面
     */
    public function addkecheng(){
        $Model_group=M('music_group');
        $data_group=$Model_group->field('id,title')->select();
        $this->assign('music_group',$data_group);
        $this->display();
    }
    /**
     * 编辑课程页面
     */
    public function editkecheng(){
        $id=I('id/d',0);
        $this->assign('id',$id);
        $Model_group=M('music_group');
        $data_group=$Model_group->field('id,title')->select();
        $this->assign('music_group',$data_group);

        /*
        $Modle_kecheng=M('text_kecheng');
        $data_kecheng=$Modle_kecheng->where('id="%d"',$id)->find();
        $this->assign('kecheng_info',$data_kecheng);
        */
        $m = M();
        $sql = 'select l.*,(select p.detail_name from art_rms_dictionary p where p.detail_code=t.r_grade and p.dictionary_code="grade") as r_grade,(select p.detail_name from art_rms_dictionary p where p.detail_code=t.r_volume and p.dictionary_code="volume") as r_volume,t.r_version from art_kecheng l,art_rms_unit t where l.ks_code=t.ks_code and l.id='.$id;
        $data = $m->query($sql);

        foreach ($data[0] as $key => $value) {
            $data_kecheng[$key] = $value;
        }
        $this->assign('kecheng_info',$data_kecheng);

        //var_dump($data_kecheng);exit;

        $Model_music_info=M('music_info');
        $data_music_info=$Model_music_info->where('id="%d"',$data_kecheng['musicid'])->field('groupid')->find();
        $this->assign('groupid_now',$data_music_info['groupid']);
        $this->display();
    }

    public function delkecheng(){
        $id =I('id/d');
        $m = M('kecheng');
        $m->where('id="%d"',$id)->delete();
    }
    /**
     * 查询当前背景下的音乐
     */
    public function queryMusic(){
        $groupid=I('music_group_id/d',0);
        $Model=M('music_info');
        $data=$Model->where('groupid="%d"',$groupid)->field('id,music_name')->select();
        $this->ajaxReturn($data);
    }
    public function kechenginfo(){
        $id = I('id/d');
        $this->assign('bid',$id);
        $this->display();
    }

    /**
     * 获取课程下的栏目
     * @DateTime 2016-05-30T20:16:19+0800
     * @return   [type]                   [description]
     */
    public function getColumns(){
        $id = I('id/d');
        $columns = M('columns');
        $data = $columns->where('kid="%d"', $id)->order('sortid')->select();
        $this->ajaxReturn($data);
    }

    public function delColumns(){
        $id = I('id/d');
        $columns = M('columns');
        $columns->where('id="%d"', $id)->delete();
    }
    /**
     * 添加课程信息到数据库
     */
    public function addKechengInfo(){
        $grade=I('grade/s','');
        $term=I('term/s','');
        $version=I('version/s','');
        $kecheng=I('kecheng/s','');
        $bieming = I('bieming/s');
        //$music=I('music/d',0);
        $music_file=I('music_file/d',0);
        $moban=I('moban/d',0);
        $filepath=I('filepath/s','');
        $filename=I('filename/s','');
        $types=I('types/d',0);
        echo $types;
        //var_dump($types);
        $Model=M('kecheng');
        /**
         * 添加前判断课程是否存在
         */
        //$re=$Model->where('nianji="%s" and xueqi="%s" and banben="%s" and kecheng="%s"',$grade,$term,$version,$kecheng)->find();
        //现在不判断()
        $re = '';
        if(is_array($re)){
            echo '已存在';
        }else{
            /*
            $data['nianji']=$grade;
            $data['xueqi']=$term;
            $data['banben']=$version;
            $data['kecheng']=$kecheng;
            $data['musicid']=$music_file;
            $data['mobanid']=$moban;
            $data['filepath']=$filepath;
            $data['filename']=$filename;
            $data['types']=$types;
            $Model->add($data);
            */
            $data['ks_code'] = $kecheng;
            $data['title'] = $bieming;
            $data['musicid']=$music_file;
            $data['mobanid']=$moban;
            $data['filepath']=$filepath;
            $data['filename']=$filename;
            $data['types']=$types;
            $Model->add($data);

        }
    }
    /**
     * 编辑课程信息写入数据库
     */
    public function updateKechengInfo(){
        $id=I('id/d',0);
        $grade=I('grade/s','');
        $term=I('term/s','');
        $version=I('version/s','');
        $banben = I('banben/s');
        $kecheng=I('kecheng/s','');
        $unit = I('unit/s');
        $bieming = I('bieming/s');
        $music_file=I('music_file/d',0);
        $filepath=I('filepath/s','');
        $filename=I('filename/s','');
        $mobanid=I('moban/d',0);
        $types=I('types/d',0);
        $Model=M('kecheng');
        if ($filepath==''){
            /*
            $data['nianji']=$grade;
            $data['xueqi']=$term;
            $data['banben']=$version;
            $data['kecheng']=$kecheng;
            $data['musicid']=$music_file;
            $data['mobanid']=$mobanid;
            $data['types']=$types;
            */
            $data['ks_code'] = $kecheng;
            $data['title'] = $bieming;
            $data['musicid']=$music_file;
            $data['mobanid']=$mobanid;
            $data['types']=$types;
            $Model->where('id="%d"',$id)->save($data);
        }else{
            /*
            $data['nianji']=$grade;
            $data['xueqi']=$term;
            $data['banben']=$version;
            $data['kecheng']=$kecheng;
            $data['musicid']=$music_file;
            $data['filepath']=$filepath;
            $data['filename']=$filename;
            $data['mobanid']=$mobanid;
            $data['types']=$types;
            */
            $data['ks_code'] = $kecheng;
            $data['title'] = $bieming;
            $data['musicid']=$music_file;
            $data['filepath']=$filepath;
            $data['filename']=$filename;
            $data['mobanid']=$mobanid;
            $data['types']=$types;
            $Model->where('id="%d"',$id)->save($data);
        }
        echo $grade.'|'.$term.'|'.$banben.'|'.$unit.'|'.$bieming.'|'.$mobanid.'|'.$types;
    }

    /**
     * 分页展示课程信息
     */
    public function fenye(){
        $pageCurrent=I('pageCurrent/d',0);
        $page_size=I('page_size/d',0);
        $grade=I('grade/s','');
        $term=I('term/s','');
        $version=I('version/s','');
        $kecheng=I('kecheng/s','');
        $Model=M();

        $gradeCode = $this->getGradeCode($grade);
        $termCode = $term=='上学期'?'0001':'0002';

        $sql_where='where t.r_subject="0007"';
        if($grade==''){
            $sql_where.='';
        }else{
            //$sql_where.=' and nianji="'.$grade.'"';
            $sql_where.=' and t.r_grade="'.$gradeCode.'" ';
        }
        if($term==''){
            $sql_where.='';
        }else{
            //$sql_where.=' and xueqi="'.$term.'"';
            $sql_where.=' AND t.r_volume="'.$termCode.'" ';
        }
        if($version==''){
            $sql_where.='';
        }else{
            //$sql_where.=' and banben like "%'.$version.'%"';
            $sql_where.=' and t.r_version=(SELECT p.DETAIL_CODE FROM rms_dictionary_detail p WHERE p.detail_name="'.$version.'" and p.dictionary_code="edition") ';
        }
        if($kecheng==''){
            $sql_where.='';
        }else{
            $sql_where.=' and l.title like "%'.$kecheng.'%"';
        }
        //$sql='select count(*) as num from cn_text_kecheng ';
        //$sql1='select * from cn_text_kecheng ';


        $sql = 'SELECT count(l.id) as num FROM art_kecheng l LEFT JOIN art_rms_unit t ON l.ks_code=t.ks_code ';
        $sql1 = 'SELECT l.id,l.mobanid,l.types,l.filepath,t.ks_name as kecheng,l.title,(SELECT p.detail_name FROM rms_dictionary_detail p WHERE p.DETAIL_CODE=t.r_grade and  p.dictionary_code="grade") as nianji,(SELECT p.detail_name FROM rms_dictionary_detail p WHERE p.DETAIL_CODE=t.r_volume and  p.dictionary_code="volume") as xueqi,(SELECT p.detail_name FROM rms_dictionary_detail p WHERE p.DETAIL_CODE=t.r_version and  p.dictionary_code="edition") as banben FROM art_kecheng l LEFT JOIN art_rms_unit t ON l.ks_code=t.ks_code ';

        //echo $sql1;exit;
        $sql_limit=' limit '.($pageCurrent-1)*$page_size.','.$page_size;
        $sql_order=' order by l.id desc';
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
        //echo $sql1.$sql_where.$sql_order.$sql_limit;
        $word[$page]=$data;
        $this->ajaxReturn($word);
    }

    /**
     * [addLanmu description]
     */
    public function addLanmu(){
        $type = I('type/s');
        $id = I('id/d','');//课程id
        $cid = I('cid/d','');//栏目ID

        $M_kecheng = M('kecheng');

        $columns = M('columns');
        if($type == 'edit'){
            $data = $columns->where('id = "%d"', $cid)->find();
            $this->assign('data', $data);
            $data_kecheng = $M_kecheng->where('id="%d"',$data['kid'])->field('mobanid')->find();
        }else{
            $data_kecheng = $M_kecheng->where('id="%d"',$id)->field('mobanid')->find();
        }

        $this->assign('mobanid',$data_kecheng['mobanid']);
        $this->assign('kid', $id);
        $this->assign('cid', $cid);
        $this->assign('type', $type);
        $this->display();
    }


    public function addColumns(){
        $title=I('title/s',0);
        $type = I('type/s');
        $remark=I('remark/s','');
        $remark2=I('remark2/s','');
        $kid=I('kid/d','');
        $cid=I('cid/d','');
        $select=I('select/d','');

        $Model=M('columns');

        $data['name']=trim($title);
        $data['remark']=trim($remark);
        $data['remark2']=trim($remark2);
        $data['type'] = $select;


        if($type == 'add'){
            $re=$Model->where('name="%s" and kid="%d"',$title, $kid)->find();
            if(!empty($re)){
                echo '名称已存在';
            }else{
                $maxid = $Model->where('kid="%d"',$kid)->max('sortid');
                if(is_null($maxid)){
                    $data['sortid'] = 1;
                }else{
                    $data['sortid'] = $maxid+1;
                }
                $data['kid'] = $kid;
                $id=$Model->add($data);;
            }
        }else{
            $data['id'] = $cid;
            $Model->save($data);
        }


    }

    /**
     * 更新排序
     * @DateTime 2016-05-30T20:46:49+0800
     * @return   [type]                   [description]
     */
    public function updateSort(){
        $sortsInfo = I('sortsInfo/s');
        $sortsInfo=str_replace('&quot;', '"', $sortsInfo);
        $arr = json_decode($sortsInfo,true);
        $columns = M('columns');
        foreach($arr as $v){
            $columns->where('id="%d"',$v['id'])->setField('sortid',$v['sortid']);
        }
    }

    /**
     * 准备上传图片页面
     * @DateTime 2016-05-30T21:55:07+0800
     * @return   [type]                   [description]
     */
    public function piclist(){
        $type = I('type/s');
        $id = I('id/d');//栏目ID

        $this->assign('cid', $id);
        $this->assign('type', $type);
        $this->display();
    }

    /**
     * 上传图片页面
     * @DateTime 2016-05-30T21:55:29+0800
     */
    public function addPiclist(){
        $type = I('type/s');
        $id = I('id/d');//栏目ID
        $this->assign('cid', $id);
        $this->assign('type', $type);
        $this->display();
    }

    public function editPiclist(){
        $id = I('id/d');//栏目ID

        $m = M('column_pics');
        $data = $m->where('id="%d"',$id)->find();
        $this->assign('id', $id);
        $this->assign('picinfo', $data['content']);
        $this->assign('picans', $data['answer']);
        $this->display();
    }

    public function editPic(){
        $id = I('id/d');
        $content = I('content/s');
        $answer = I('answer/s');

        $model = M('column_pics');
        $data['content'] = $content;
        $data['answer'] = $answer;
        $model->where('id="%d"',$id)->save($data);

    }



    /**
     * 上传图片写入数据库
     * @DateTime 2016-05-30T22:02:58+0800
     */
    public function addPic(){
        $cid = I('cid/d');
        $type = I('type/s');
        $content = I('content/s');
        $answer = I('answer/s');
        $filepath = I('filepath/s');

        empty($filepath) ? $filepath : $filepath = 'uploads/pic/'.$filepath;

        $data['pic'] = $filepath;
        $data['cid'] = $cid;
        $data['content'] = $content;
        $data['answer'] = $answer;

        $model = M('column_pics');

        $type == 'thumb'?$type=0:$type=1;
        $data['type'] = $type;

        $maxid = $model->where('cid="%d" and type="%d"',$cid, $type)->max('sortid');
        if(is_null($maxid)){
            $data['sortid'] = 1;
        }else{
            $data['sortid'] = $maxid+1;
        }

        $model->add($data);


    }


    /**
     * 加载当前栏目下的图片
     * @DateTime 2016-05-31T13:35:38+0800
     * @return   [type]                   [description]
     */
    public function getPiclist(){
        $cid = I('id/d');
        $type = I('type/s');

        $model = M('column_pics');
        $type == 'thumb'?$type=0:$type=1;

        $data = $model->where('type="%d" and cid="%d"', $type, $cid)->order('sortid')->select();
        $this->ajaxReturn($data);
    }

    /**
     * 更新图片顺序
     * @DateTime 2016-05-31T13:53:08+0800
     * @return   [type]                   [description]
     */
    public function updatePicSort(){
        $sortsInfo = I('sortsInfo/s');
        $sortsInfo=str_replace('&quot;', '"', $sortsInfo);
        $arr = json_decode($sortsInfo,true);
        //var_dump($arr);
        $columns = M('column_pics');
        foreach($arr as $v){
            $columns->where('id="%d"',$v['id'])->setField('sortid',$v['sortid']);
            //echo $columns->getLastSql();exit;
        }
    }

    /**
     * 删除图片
     * @DateTime 2016-05-31T13:54:41+0800
     * @return   [type]                   [description]
     */
    public function delPic(){
        $id = I('id/d');
        $model = M('column_pics');
        $data = $model->where('id="%d"', $id)->field('pic')->find();
        unlink($data['pic']);
        $model->where('id="%d"', $id)->delete();
    }


    /**
     * 在线编辑器
     * @return [type] [description]
     */
    public function online(){
        $xml = I('xml/s');
        $kid = I('kid/d');
        $ip = C('IPADDRESS');
        $xml = 'http://'.$ip.'/'.$xml;
        $resultUrl = 'http://'.$ip.'/Home/Art/getResult';
        $url = "mainXmlUrl=".urlencode($xml)."&resultUrl=".urlencode($resultUrl);

        $this->assign('url',$url);
        $this->assign('kid',$kid);
        $this->display();
    }


    function isMake(){
        $kid = I('kid/d');
        $model = M();
        $sql = 'SELECT t.* FROM art_column_pics t WHERE t.width is not null AND t.height is not null AND t.x is not null AND t.y is not null AND t.maskscalex is not null AND t.cid in (SELECT l.id FROM art_columns l WHERE l.kid='.$kid.' )';
        $data = $model->query($sql);
        if(empty($data)){
            $info['err'] = 1;
        }else{
            $info['err'] = 0;
        }
        $this->ajaxReturn($info);
    }


    /**
     * 生成缩略图的xml
     * @return [type] [description]
     */
    public function makexml(){
        $id = I('id/d');
        $ip = C('IPADDRESS');

        $sql_qt = 'SELECT t.* FROM art_column_pics t WHERE t.cid = (SELECT l.id FROM art_columns l WHERE l.kid='.$id.' AND l.type = 2 limit 1 ) order by t.sortid';
        $sql_xxhz = 'SELECT t.* FROM art_column_pics t WHERE t.cid = (SELECT l.id FROM art_columns l WHERE l.kid='.$id.' AND l.type = 3 limit 1 ) order by t.sortid';

        $m = M();
        $xml = "<data>\n";

        $data = array();
        //生成趣图园的xml
        //echo $sql_qt;
        $data_qt = $m->query($sql_qt);

        if(empty($data_qt)){
            $data['qt'] = '趣图园不存在!';
        }else{
            if(count($data_qt)!=3&&count($data_qt)!=5){
                $data['qt'] = '趣图园图片数量必须是3或5张!';
            }else{
                $data['qt'] = 'ok';
                $xml .= "   <趣图>\n";
                foreach($data_qt as $v){
                    $xml .= '       <图片地址>'."\n";
                    $xml .= '       <imgurl>';
                    $xml .= 'http://'.$ip.'/'.$v['pic'];
                    $xml .= '</imgurl>'."\n";
                    $xml .= '       </图片地址>'."\n";
                }
                $xml .= '   </趣图>'."\n";
            }
        }

        //生成小小画展xml
        $data_xxhz = $m->query($sql_xxhz);

        if(empty($data_xxhz)){
            $data['xxhz'] = '小小画展不存在!';
        }else{
            if(count($data_xxhz)!=3&&count($data_xxhz)!=5){
                $data['xxhz'] = '小小画展图片数量必须是3或5张!';
            }else{
                $data['xxhz'] = 'ok';
                $xml .= '   <画展>'."\n";
                foreach($data_xxhz as $v){
                    $xml .= '       <图片地址>'."\n";
                    $xml .= '       <imgurl>';
                    $xml .= 'http://'.$ip.'/'.$v['pic'];
                    $xml .= '</imgurl>'."\n";
                    $xml .= '       </图片地址>'."\n";
                }
                $xml .= '   </画展>'."\n";
            }
        }

        $xml .= '</data>';
        //echo $xml;
        vendor('fileDirUtil');
        $dir = new \fileDirUtil();

        $xmldir = 'tmpxml/'.$id.'/';

        if(!is_dir($xmldir)){
            $dir->createDir($xmldir);
        }

        $xmlFile = $xmldir.'data.xml';

        if(file_exists($xmlFile)){
            unlink($xmlFile);
        }

        $dir->writeFile($xmlFile,$xml);


        $data['path'] = $xmlFile;

        $this->ajaxReturn($data);
    }


    /**
     * 获取编辑器创作的图片结果
     * @return [type] [description]
     */
    public function getResult(){
        $xml = I('xml');
        if(empty($xml)){
            //exit('返回数据为空！');
        }
        $xml = htmlspecialchars_decode($xml);
        //file_put_contents('flash/a.txt', $xml);
        //$xml = file_get_contents('flash/b.txt');
        preg_match_all('/<趣图>(.*?)<\/趣图>/s', $xml, $out1);
        //var_dump($out1);
        $this->insertXY($out1);

        preg_match_all('/<画展>(.*?)<\/画展>/s', $xml, $out2);
        $this->insertXY($out2);
    }
    /**
     * 编辑器生成的数据，插入数据库
     * @param  [type] $arr [description]
     * @return [type]      [description]
     */
    protected function insertXY($arr){
        $M_column_pics = M('column_pics');
        foreach($arr[0] as $v){
            preg_match_all('/<imgurl>(.*?)<\/imgurl>/s',$v,$imgurl);
            preg_match_all('/<width>(.*?)<\/width>/',$v,$width);
            preg_match_all('/<height>(.*?)<\/height>/',$v,$height);
            preg_match_all('/<x>(.*?)<\/x>/',$v,$x);
            preg_match_all('/<y>(.*?)<\/y>/',$v,$y);
            preg_match_all('/<maskX>(.*?)<\/maskX>/',$v,$maskX);
            preg_match_all('/<maskY>(.*?)<\/maskY>/',$v,$maskY);
            preg_match_all('/<maskparent>(.*?)<\/maskparent>/',$v,$maskparent);

            $pic = str_replace('http://'.C('IPADDRESS').'/','',$imgurl[1][0]);

            //echo $pic;
            $data['width']  = $width[1][0];
            $data['height'] = $height[1][0];
            $data['x']      = $x[1][0];
            $data['y']      = $y[1][0];
            $data['maskscalex']  = $maskX[1][0];
            $data['maskscaley']  = $maskY[1][0];
            $data['maskparent']  = $maskparent[1][0];
            //echo $pic .'|'.$maskX[1][0].'<br>';
            $M_column_pics->where('pic="%s"',$pic)->save($data);
        }
    }


    /**
     * 下载flash
     * @return [type] [description]
     */
    public function flashdown(){
        header('content-type:text/html;charset=utf-8');
        $kechengid = I("kechengid/d",0);

        $Model_kecheng = M("kecheng"); // 实例化User对象
        $data = $Model_kecheng ->where('id =%d',$kechengid)->find();
        if (empty($data)) {
            exit();
        }
        else
        {
            if(empty($data['title'])){
                $Model_unit = M('rms_unit');
                $data_unit = $Model_unit->where('ks_code="%s"',$data["ks_code"])->field('ks_name')->find();
                $title = $data_unit['ks_name'];
            }else{
                $title = $data['title'];
            }
            $ks_code      = $data["ks_code"];
            $moban        = $data["mobanid"];
            $arr_template =  C('CONST_TEMPLATE');
            $template     = $arr_template[$moban];
            if (empty($template)) {
            exit('模版信息有误!');
            }
        }



        Vendor("fileDirUtil");
        $dir = new \fileDirUtil();

        $todir ='./flash/' . $kechengid.'/';
        $dir->unlinkDir($todir);
        $dir->copyDir('./Template/'.$template,$todir,true);

        $Model_music = M('music_info');
        $data_music = $Model_music->where('id="%d"',$data['musicid'])->find();
        if(empty($data_music)){
            exit('背景音乐不能为空！');
        }

        //复制背景音乐
        $dir->copyFile('uploads/audio/'.$data_music['music_filepath'], $todir.'com/mp3/'.basename($data_music['music_filepath']),true);

        $mp3 = basename($data_music['music_filepath']);


        $Model_columns = M('columns');

        //创作说明
        $data_columns = $Model_columns->where('kid="%d" and type =4',$kechengid)->find();
        $chuangzuoshuoming = $data_columns['remark'];
        $chuangzuoshuoming2 = $data_columns['remark2'];

        //知识
        $m = M();
        $sql_zsc = 'SELECT t.* FROM art_column_pics t WHERE t.cid = (SELECT l.id FROM art_columns l WHERE l.kid='.$kechengid.' AND l.type = 1 limit 1 ) order by t.sortid';
        $data_zsc = $m->query($sql_zsc);
        if(empty($data_zsc)){
            exit('知识窗没有图片信息！');
        }
        $zhishi = '';
        foreach($data_zsc as $key=>$v){
            $dir->copyFile($v['pic'], $todir.'com/img/'.basename($v['pic']),true);
            $img = 'img/'.basename($v['pic']);
            $answer = $v['answer'];
            if(empty($answer)){
                $answer = 'null';
            }else{
                $answer = '"'.$answer.'"';
            }

            $content = $v['content'];

            $zhishi .= '            [Embed(source = "'.$img.'")]'."\r\n";
            $zhishi .= '            var zhishi'.($key+1).':Class;'."\r\n";
            if($answer=='null'&&empty($content)){
                $zhishi .= '            record.zhishiArr.push([zhishi'.($key+1).']);'."\r\n";
            }else{
                $zhishi .= '            record.zhishiArr.push([zhishi'.($key+1).',"'.$v['content'].'",'.$answer.']);'."\r\n";
            }

        }

        //创作

        $sql_cz = 'SELECT t.* FROM art_column_pics t WHERE t.cid = (SELECT l.id FROM art_columns l WHERE l.kid='.$kechengid.' AND l.type = 4 limit 1 ) order by t.sortid';
        $data_cz = $m->query($sql_cz);
        if(empty($data_cz)){
            exit('艺术创作没有图片信息！');
        }
        $chuangzuo = '';
        foreach($data_cz as $key=>$v){
            $dir->copyFile($v['pic'], $todir.'com/img/'.basename($v['pic']),true);
            $img = 'img/'.basename($v['pic']);
            $chuangzuo .= '            [Embed(source = "'.$img.'")]'."\r\n";
            $chuangzuo .= '            var chuangzuoimg'.($key+1).':Class;'."\r\n";
            $chuangzuo .= '            record.chuangzuoArr.push([chuangzuoimg'.($key+1).',"'.$v['content'].'"]);'."\r\n";
        }

        //趣图园
        $sql_qt = 'SELECT t.* FROM art_column_pics t WHERE t.cid = (SELECT l.id FROM art_columns l WHERE l.kid='.$kechengid.' AND l.type = 2 limit 1 ) order by t.sortid';
        $data_qt = $m->query($sql_qt);
        if(empty($data_qt)){
            exit('趣图园没有图片信息！');
        }
        $qt = '';
        foreach($data_qt as $key=>$v){
            if(empty($v['x'])){
                exit('趣图图没有设置缩略图！');
            }
            $dir->copyFile($v['pic'], $todir.'com/img/'.basename($v['pic']),true);
            $img = 'img/'.basename($v['pic']);
            $qt .= '            [Embed(source = "'.$img.'")]'."\r\n";
            $qt .= '            var qvtuimg'.($key+1).':Class;'."\r\n";
            $qt .= '            record.qvtuArr.push([qvtuimg'.($key+1).','.$v['width'].','.$v['height'].','.$v['x'].','.$v['y'].',"polygon'.($key+1).'",'.$v['maskscalex'].','.$v['maskscaley'].', "'.$v['maskparent'].'","'.$v['content'].'" ]);'."\r\n";
        }

        //小小画展
        $sql_xxhz = 'SELECT t.* FROM art_column_pics t WHERE t.cid = (SELECT l.id FROM art_columns l WHERE l.kid='.$kechengid.' AND l.type = 3 limit 1 ) order by t.sortid';
        $data_xxhz = $m->query($sql_xxhz);
        if(empty($data_xxhz)){
            exit('小小画展没有图片信息！');
        }
        $xxhz = '';
        foreach($data_xxhz as $key=>$v){
            if(empty($v['x'])){
                exit('小小画展没有设置缩略图！');
            }
            $dir->copyFile($v['pic'], $todir.'com/img/'.basename($v['pic']),true);
            $img = 'img/'.basename($v['pic']);
            $xxhz .= '            [Embed(source = "'.$img.'")]'."\r\n";
            $xxhz .= '            var huazhan'.($key+1).':Class;'."\r\n";
            $xxhz .= '            record.huazhanArr.push([huazhan'.($key+1).','.$v['width'].','.$v['height'].','.$v['x'].','.$v['y'].',"qiqiu'.($key+1).'",'.$v['maskscalex'].','.$v['maskscaley'].', "'.$v['maskparent'].'","'.$v['content'].'" ]);'."\r\n";
        }



        $asfile = $todir . 'com/Datas.as';
        //写入AS文件
        $as = $dir->readsFile($asfile);

        $jsflfile = $todir . 'run.jsfl';
        //写入AS文件
        $jsfl = $dir->readsFile($jsflfile);


        if($template == 'K2'||$template == 'K4'){
            //花朵有拓展
            $sql_tz = 'SELECT t.* FROM art_column_pics t WHERE t.cid = (SELECT l.id FROM art_columns l WHERE l.kid='.$kechengid.' AND l.type = 5 limit 1 ) order by t.sortid';
            $data_tz = $m->query($sql_tz);
            if(empty($data_tz)){
                exit('拓展没有图片信息！');
            }
            $tuozhan = '';
            foreach($data_tz as $key=>$v){
                $dir->copyFile($v['pic'], $todir.'com/img/'.basename($v['pic']),true);
                $img = 'img/'.basename($v['pic']);
                $tuozhan .= '            [Embed(source = "'.$img.'")]'."\r\n";
                $tuozhan .= '            var tuozhan'.($key+1).':Class;'."\r\n";
                $tuozhan .= '            record.tuozhanArr.push(tuozhan'.($key+1).');'."\r\n";
            }
            $as = str_replace('$$tuozhan$$', $tuozhan, $as);//拓展
        }else{
            $as = str_replace('$$tuozhan$$', "", $as);
        }


        $title = str_replace('美术一点通','',$title);
        $as = str_replace('$$biaoti$$', $title, $as);//标题
        $as = str_replace('$$chuangzuoshuoming$$', $chuangzuoshuoming, $as);//创作说明
        $as = str_replace('$$chuangzuoshuoming2$$', $chuangzuoshuoming2, $as);//创作说明
        $as = str_replace('$$zhishi$$', $zhishi, $as);//知识
        $as = str_replace('$$qutu$$', $qt, $as);//趣图园
        $as = str_replace('$$xxhz$$', $xxhz, $as);//趣图园
        $as = str_replace('$$chuangzuo$$', $chuangzuo, $as);//趣图园
        $dir->writeFile($asfile, $as);


        $jsfl = str_replace('$$kecheng$$','《'.$title.'》美术一点通',$jsfl);
        $jsfl = str_replace('$$mp3$$',$mp3,$jsfl);
        $dir->writeFile($jsflfile, $jsfl);

        Vendor("PHPZip");
        $Zip = new \PHPZip();
        $zifile = './flash/' . $kechengid . '.zip';
        $dir->unlinkDir($zifile);

        ini_set('memory_limit','256M');
        $Zip->Zip($todir, $zifile);

        $this->download ($zifile,' 《'.str_replace('·', '', $title) . '》美术一点通.zip');
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
        exit();
    }









}