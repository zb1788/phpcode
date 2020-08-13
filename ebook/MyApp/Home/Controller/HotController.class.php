<?php
/**
 * 用户登录首页
 * @author Zhangbo1
 *
 */
namespace Home\Controller;
use Think\Controller;
class HotController extends Controller {
    public function index(){

        $xml = I('xml/s','');//接收xml文件

        //$xml = file_get_contents('php://input');

        $content = $xml;
        $content = htmlspecialchars_decode($content);

        $xmltxt = 'Uploads/hot.xml';
        file_put_contents($xmltxt, $content);
        //$content = file_get_contents($xml);


        $content = str_replace(array("\r\n", "\r", "\n"), "", $content);
        preg_match_all('/bookid="(.*?)"/', $content, $book, PREG_SET_ORDER);
        preg_match_all('/yema="(.*?)"/', $content, $yema, PREG_SET_ORDER);


        $bookid = $book[0][1];//书本id
        $page = $yema[0][1];//页码

        //echo 'bookid='.$bookid.'and pagenum='.$page;
        preg_match_all('/<hotmusic>(.*?)<\/hotmusic>/', $content, $hot, PREG_SET_ORDER);

        $m=M('book_page_hot_en', 't_', 'DB_CONFIG');

        $m->where(' bookid="%s" and pagenum="%d" ',$bookid,$page)->delete();

        foreach($hot as $v){
            //var_dump($v);
            preg_match_all('/<link>(.*?)<\/link>/', $v[1], $link, PREG_SET_ORDER);
            //preg_match_all('/<linkName>(.*?)<\/linkName>/', $v[1], $linkName, PREG_SET_ORDER);
            preg_match_all('/<plist>(.*?)<\/plist>/', $v[1], $zuobiao, PREG_SET_ORDER);
            preg_match_all('/<play>(.*?)<\/play>/', $v[1], $play, PREG_SET_ORDER);
            preg_match_all('/<stop>(.*?)<\/stop>/', $v[1], $stop, PREG_SET_ORDER);
            preg_match_all('/<yuanwen>(.*?)<\/yuanwen>/', $v[1], $yuanwen, PREG_SET_ORDER);
            preg_match_all('/<fanyi>(.*?)<\/fanyi>/', $v[1], $fanyi, PREG_SET_ORDER);

            $mp3url = $link[0][1];//音频地址
            //$truename = $linkName[0][1];//音频真实名称
            $vbeg = $play[0][1];//音频开始时间
            $vend = $stop[0][1];//音频结束时间
            $plist = $zuobiao[0][1];//坐标信息
            $old = $yuanwen[0][1];//原文
            $new = $fanyi[0][1];//翻译

            if(empty($mp3url)){
                continue;
            }

            //echo 'mp3url='.$mp3url.'<br>';
            //echo 'plist='.$plist;


            $data['urlname']=$mp3url;
            //$data['truename']=$truename;
            $data['vbeg']=$vbeg;
            $data['vend']=$vend;
            $data['plist']=$plist;
            $data['pagenum']=$page;
            $data['bookid']=$bookid;
            $data['old']=$old;
            $data['new']=$new;


            $m->add($data);

        }





    }


    public function getMp3(){
          if ($_FILES ["Filedata"] ["error"] > 0) {
            exit("Error: " . $_FILES ["Filedata"]["error"]);
        }

        $fileName = iconv("utf-8","gb2312", $_FILES ["Filedata"]["name"]);
        $reallyName = "Uploads/".$fileName;

        if (file_exists ($reallyName)) {
            unlink($reallyName);
        }


             // if (!is_dir("upload")) {
             //     mkdir("upload");
             // }

           move_uploaded_file( $_FILES ["Filedata"]["tmp_name"],  $reallyName);
           echo "Stored in: " . "Uploads/" . $fileName;
    }



    public function getRes(){
        header("Content-type:text/xml");
        $bookid = I('bookid/s','');
        $page = I('page/d','');


        $m=M('', 't_', 'DB_CONFIG');
        //$data = $m->where('bookid="%s" and pagenum="%d"', $bookid ,$page)->select();

        $sql = "SELECT l.*,t.pagefile,t.mp3url,t.truename FROM t_book_page_hot_en l RIGHT JOIN t_book_page t ON l.bookid=t.bookid AND l.pagenum=t.pagenum WHERE t.bookid='".$bookid."' AND t.pagenum='".$page."'";
        //echo $sql;
        $data = $m->query($sql);

        //var_dump($data);
        $xml="<?xml version=\"1.0\" encoding=\"utf-8\" ?>\r\n";
        //$xml.="<data name=\"电子课本CION图标\">\r\n";
        $xml .= "<page bookid=\"".$bookid."\" yema=\"".$page."\">";
        foreach($data as $v){
            $xml .= "<hotmusic>";
            $xml .= "<name>热点</name>";
            $xml .= "<link>".$v['mp3url']."</link>";
            $xml .= "<linkName>".$v['truename']."</linkName>";
            $xml .= "<picurl>".$v['pagefile']."</picurl>";
            $xml .= "<plist>".$v['plist']."</plist>";
            $xml .= "<play>".$v['vbeg']."</play>";
            $xml .= "<stop>".$v['vend']."</stop>";
            $xml .= "<yuanwen>".$v['old']."</yuanwen>";
            $xml .= "<fanyi>".$v['new']."</fanyi>";
            $xml .= "</hotmusic>";
        }
        $xml .="</page>";

        echo $xml;
    }



    public function makexml(){
    	$userid=I('userid/s','');//用户id
    	$areaid=I('areaid/s','');//用户区域
    	$bookid=I('bookid/s','');//书本id

        $areaid='13.';

        //echo $userid.'|'.$bookid.'|'.$areaid;

    	$xml="<?xml version=\"1.0\" encoding=\"utf-8\" ?>\r\n";
    	$xml.="<data name=\"电子课本CION图标\">\r\n";
    	//$m=M('','','DB_CONFIG')->table('t_book_page_file');
        $m=M('book_page_file','t_');
    	$data=$m->where('bookid="%s" and userid="%s" and areaid="%s"',$bookid,$userid,$areaid)->field('link,ran,x,y,name,type,page')->select();

    	foreach ($data as $v){
    		$xml.="		<subobj>\r\n			<name>".$v['ran']."</name>\r\n			<iconx>".$v['x']."</iconx>\r\n			<icony>".$v['y']."</icony>\r\n			<notes>".$v['name']."</notes>\r\n			<link>".$v['link']."</link>\r\n			<type>".$v['type']."</type>\r\n			<pagenum>".$v['page']."</pagenum>\r\n		</subobj>\r\n";
    	}
    	$xml.="</data>";
    	echo $xml;
    	//var_dump($xml);
//      	$handle=fopen("icon.xml","w");
//      	fwrite($handle,$xml);
    }
}