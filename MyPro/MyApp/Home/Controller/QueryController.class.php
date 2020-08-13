<?php
namespace Home\Controller;
use Think\Controller;
class QueryController extends Controller {


    public function phpQueryDemo(){
        /**
         *  注意html()获取的值跟网站编码一致
         */

        vendor('phpQuery.phpQuery');
        \phpQuery::$defaultCharset="gbk";
        \phpQuery::newDocumentFile('http://www.php100.com/html/dujia/2015/0213/8636.html');

        //根据ID查询 获取到的值与本机的编码一致
        $inputValue = pq('#bdyz')->val();
        $inputType = pq('#bdyz')->attr('type');
        //根据class查询
        $inputClass= pq('.keywords')->val();


        $litxt = pq('.top')->children('ul')->children('li')->eq(0)->children('a')->text();

        echo $inputValue.'|'.$inputType.'|'.$inputClass.'|'.$litxt.'<br/>';

        $li = pq('.sub')->find('li');
        //var_dump($li);

        foreach($li as $v){
            echo pq($v)->text().'<br>';
        }
    }








}