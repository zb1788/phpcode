<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends CheckController {
    public function index(){
        $this->display();
    }
}