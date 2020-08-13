<?php
namespace Home\Model;
use Think\Model;
class BookModel extends Model{
    public function selectOne(){
       $data=  $this->where('id=124')->find();
       return $data;
    }
}
