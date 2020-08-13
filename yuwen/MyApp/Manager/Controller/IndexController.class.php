<?php
namespace Manager\Controller;
use Think\Controller;

/** 
* 框架
*  
* @author         gm 
* @since          1.0 
*/  
class IndexController extends CheckController {
    
    // public function index(){ 
    //     $M = M('zi');
    //     $rs = $M-> select(); 
    //     foreach($rs as $v) {
    //         $id = $v["id"];
    //         $zi = $v["zi"];
    //         $bihuaswf = $v["bihuaswf"];

    //         if (file_exists('./hzviedo/' . $bihuaswf)) {
    //             $M->id=$id ;
    //             $name  = "uploads/" . $this->randChar() . '.swf';
    //             copy('./hzviedo/' . $bihuaswf, './'. $name);
    //             $M->bihuaswf= $name;
    //             $M->save();
    //         }
    //         else
    //         {
    //             $M->id=$id ;
    //             $M->bihuaswf= "";
    //             $M->save();
    //         }
    //         echo $id . "<br>";
    //     }

    //     $M = M('ziyin');
    //     $rs = $M-> select(); 
    //     foreach($rs as $v) {
    //         $id = $v["id"]; 
    //         $wav = $v["wav"];

    //         if (file_exists('./wav/' . str_replace("mp3", "wav", $wav))) {
    //             $M->id=$id ;
    //             $name  = "uploads/" . $this->randChar() . '.wav';
    //             copy('./wav/' . str_replace("mp3", "wav", $wav), './'. $name);
    //             $M->wav= $name;
    //             $M->save();
    //         }
    //         else
    //         {
    //             $M->id=$id ;
    //             $M->wav= "";
    //             $M->save();
    //         }
    //         echo $id . "<br>";
    //     }

    //     // $this->display();


    // 	// if (file_exists('./wav/111111.wav')) {
 
    // 	// }  ;
    // }

    protected  function randChar($n=13)
    {
        $str = "0123456789abcdefghijklmnopqrstuvwxyz";//输出字符集 
        $len = strlen($str)-1;
        for($i=0 ; $i<$n; $i++){
            $s .= $str[rand(0,$len)];
        }
        return $s;
    }
}

 
