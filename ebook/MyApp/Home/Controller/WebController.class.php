<?php
/**
 * 用户登录首页
 * @author Zhangbo1
 *
 */
namespace Home\Controller;
use Think\Controller;
class WebController extends Controller {
    public function index(){
        $chapterid = I('chapterid/s','');
        $m = M('','','DB_CONFIG')->table('t_book_chapter');
        $data = $m->where('chapterid="%s"',$chapterid)->find();
        $bookid = $data['bookid'];
        $pagebeg = $data['pagebeg'];
        $pageend = $data['pageend'];
        $this->assign('bookid',$bookid);
        $this->assign('pagebeg',$pagebeg);
        $this->assign('pageend',$pageend);
        $this->display();
    }


    public function index_zpo(){
        $chapterid = I('chapterid/s','');
        $m = M('','','DB_CONFIG')->table('t_book_chapter');
        $data = $m->where('chapterid="%s"',$chapterid)->find();
        $bookid = $data['bookid'];
        $pagebeg = $data['pagebeg'];
        $pageend = $data['pageend'];
        $this->assign('bookid',$bookid);
        $this->assign('pagebeg',$pagebeg);
        $this->assign('pageend',$pageend);
        $this->display();
    }

    public function getInfo(){
        $bookid = I('bookid/s','');
        $pagenum = I('pagenum/d',0);

        $m = M('','','DB_CONFIG')->table('t_book_page');
        $data_page = $m->where('bookid="%s" and pagenum="%d"',$bookid,$pagenum)->find();
        $n = M('','','DB_CONFIG')->table('t_book_page_hot');
        $data_hot = $n->where('bookid="%s" and pagenum="%d" ',$bookid,$pagenum)->field('plist,vbeg,vend,hotmp3')->order('id asc')->select();
        $pic = 'http://192.168.151.126:8051/'.$data_page['pagefile'];

        $picInfo = getimagesize($pic);
        $w_d = $picInfo[0];
        $h_d = $picInfo[1];

        $data['hot'] = $data_hot;
        $data['pw'] = $w_d;
        $data['ph'] = $h_d;
        $data['pic'] = $pic;
        $this->ajaxReturn($data);
    }


    public function index_fromxml(){
        $xml = I('xml/s','');
        $xml = 'ebookres/data.xml';


        $this->assign('xml',$xml);
        $this->display();
    }

   public function getXml(){
        $xml = 'data.xml';

        $xml = I('xml/s','');

        $doc = new \DOMDocument();
        $doc->load($xml);

        $pages = $doc->getElementsByTagName('page');
        $leftpages = $doc->getElementsByTagName('leftpage');
        $rightpages= $doc->getElementsByTagName('rightpage');



        $info = array();

        $pages_array = array();


        // var_dump($leftpages);

        foreach($leftpages as $key=>$leftpage){
            $hotmusics= $leftpage->getElementsByTagName('hotmusic');
            // var_dump($hotmusics);
            foreach($hotmusics as $kk=>$hotmusic){
                $mp3 = $hotmusic->getElementsByTagName('link')->item(0)->nodeValue;
                $plist = $hotmusic->getElementsByTagName('plist')->item(0)->nodeValue;
                $play = $hotmusic->getElementsByTagName('play')->item(0)->nodeValue;
                $stop = $hotmusic->getElementsByTagName('stop')->item(0)->nodeValue;

                $info[$key*2]['hot'][$kk]['plist'] = $plist;
                $info[$key*2]['hot'][$kk]['vbeg'] = $play;
                $info[$key*2]['hot'][$kk]['vend'] = $stop;
                $info[$key*2]['hot'][$kk]['urlname'] = $mp3;

            }
        }




        foreach($rightpages as $key=>$rightpage){
            $hotmusics= $rightpage->getElementsByTagName('hotmusic');
            // var_dump($hotmusics);
            foreach($hotmusics as $kk=>$hotmusic){
                $mp3 = $hotmusic->getElementsByTagName('link')->item(0)->nodeValue;
                $plist = $hotmusic->getElementsByTagName('plist')->item(0)->nodeValue;
                $play = $hotmusic->getElementsByTagName('play')->item(0)->nodeValue;
                $stop = $hotmusic->getElementsByTagName('stop')->item(0)->nodeValue;


                $info[$key*2+1]['hot'][$kk]['plist'] = $plist;
                $info[$key*2+1]['hot'][$kk]['vbeg'] = $play;
                $info[$key*2+1]['hot'][$kk]['vend'] = $stop;
                $info[$key*2+1]['hot'][$kk]['urlname'] = $mp3;

            }
        }

        ksort($info);
        // var_dump($info);


        // $pages_array[0] = '';
        // $info[0] = '';

        foreach($pages as $key=>$page){
            // var_dump($page->nodeValue);
            array_push($pages_array,$page->nodeValue);
            $info[$key]['pic'] = $page->nodeValue;

            $picInfo = getimagesize('./ebookres/'.$page->nodeValue);

            $info[$key]['pw'] = $picInfo[0];
            $info[$key]['ph'] = $picInfo[1];
        }

        // exit(count($pages));

        $res['num'] = count($pages_array);
        $res['info'] = $info;

        $this->ajaxReturn($res);
    }



}