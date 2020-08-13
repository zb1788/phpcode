<?php
namespace Home\Controller;
use Think\Controller;
class RedisController extends Controller {
    /*
     *Redis运用
     */
    public function index(){

        $redis = new \Redis();
        //连接redis
        $redis->connect('127.0.0.1',6379);
        //设置字符串
        $redis->set('age',33);
        echo $redis->get('age');
        //给多个key赋值
        $redis->mset(array('key0' => 'value0', 'key1' => 'value1'));
        //设置list
        $redis->lpush('listDemo','bmw');
        $redis->lpush('listDemo','benchi');
        $redis->lpush('listDemo','audi');
        //获取list存储的数据
        //start 从0开始,start和end相当于数据的key
        $listArr = $redis->lrange('listDemo', 0, 2);
        var_dump($listArr);

        //获取redis所有的key
        $keyArr = $redis->keys("*");
        var_dump($keyArr);

        //删除某个key
        $redis->delete('hash1');

        //哈希hash,Redis hash 是一个string类型的field和value的映射表，hash特别适合用于存储对象。
        //hSet，单个添加,一次添加一个属性
        $redis->hSet('hash1','name','kite');
        $hash1 = $redis->hGet('hash1','name');
        var_dump($hash1);

        //批量添加对象
       $redis->hMset('hash2',array('age' => 11, 'sex'=>'male'));
        $hash2 = $redis->hMget('hash2',array('age', 'sex'));
        var_dump($hash2);

        //rename 给key重命名
        $redis->set('x',44);
        $redis->rename('x','y');
        $redis->get('y');


        //清除所有数据
        $redis->flushDB();

    }
















}