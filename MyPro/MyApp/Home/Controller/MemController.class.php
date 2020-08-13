<?php
namespace Home\Controller;
use Think\Controller;
class MemController extends Controller {
    /*
     *Memcache运用
     */
    public function index(){

        //初始化一个Memcache的对象
        $mem = new \Memcache;

        //连接到我们的Memcache服务器端，第一个参数是服务器的IP地址，也可以是主机名，第二个参数是Memcache的开放的端口：
        if(!$mem->connect("localhost", 11211)){
            die('连接失败！');
        }


        //保存一个数据到Memcache服务器上，第一个参数是数据的key，用来定位一个数据，第二个参数是需要保存的数据内容，这里是一个字符串，第三 个参数是一个标记，一般设置为0或者MEMCACHE_COMPRESSED就行了，第四个参数是数据的有效期，就是说数据在这个时间内是有效的，如果过 去这个时间，那么会被Memcache服务器端清除掉这个数据，单位是秒，如果设置为0，则是永远有效，我们这里设置了60，就是一分钟有效时间：


        //设置缓存
        //以前写过一个php的memcache操作类，里面提到了在设置memcache有效期时，不能大于30天，其实memcache是可以设置大于30天有效期的。
        //$mem->set($key, $val, 0,  time()+31*24*60*60);


        //key的加密方式
        //我这里选用了MD5方式加密SQL做为key ，也可用其他加密方式，如Base64等
        //$query = "select UID,username,password,gender from pw_members order by uid desc limit 10;";
        //$m_key=md5($query);
        /*
            if(!$result=$mem->get($m_key)){
                echo "这是从数据库读出来的结果!";
                $connection=mysql_connect($host,$user,$passwd) or die ("Unable to conect!" );
                mysql_select_db($db);
                $result=mysql_query($query) or die("Error query!".mysql_error());
                while($row=mysql_fetch_row($result)){
                    $memdata[]=$row;
                }
                //查询数据
                $mem->add($m_key,$memdata);
                mysql_free_result($result);
                mysql_close($connection);
            }else{
                echo "这是从Memcached Server读出来的结果!\n";
            }
        */


        //1 增加字符串

        $mem->set('key1', 'hello world', 0, 10);

        //2 增加数值

        $mem->set('key2', 100, 0, 10);

        //3 增加数组

        $arr = array('a','b','c','d','e');

        $mem->set('key3', $arr, 0, 10);

        //4 增加对象

        /*
        class Dog{
            public $name;
            public $age;
            public function __construct($name,$age){
                $this->name=$name;
                $this->age=$age;
            }
        }
        $dog1=new Dog('小狗',50);
        if($mem->set('key4',$dog1,MEMCACHE_COMPRESSED,60)){
            echo '添加对象ok';
        }
        */

        //5 增加null 布尔值

        $mem->set('key5', false, 0, 10);

        //6 增加资源类型

        /*
        $con=mysql_connect("127.0.0.1","root","root");
        if(!$con){
            die('连接数据库失败');
        }
        var_dump($con);
        echo "<br/>";
        if($mem->set('key6',$con,MEMCACHE_COMPRESSED,60)){
            echo '添加资源ok';
        }
        */

        //查询内容

        $val = $mem->get('key1');

        echo "Get key1 value: " . $val ."<br />";

        //替换修改内容

        //可以使用replace
        if($mem->replace("key1",'hello',MEMCACHE_COMPRESSED,60)){
            echo 'replace ok';
        }else{
            echo 'replace no ok';
        }

        //删除
        if($mem->delete('key1')){
            echo 'key1 删除';
        }else{
            echo 'key1不存在';
        }

        //清除所有数据
        $mem->flush();
        $val2 = $mem->get('key1');
        print_r($val2);

        //关闭连接
        $mem->close();
    }

    public function test(){
        $mem = new \Memcache;
        if(!$mem->connect('localhost',11211)){
            echo '连接失败！';
        }
        //$array = array('a', 'b', 'c');
        //$mem->set('key',$array,0,15);
        $val = $mem->get('key');
        var_dump($val);
        $mem->close;
    }














}