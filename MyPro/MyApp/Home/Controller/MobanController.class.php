<?php
namespace Home\Controller;
use Think\Controller;
class MobanController extends CheckController {
    public function button(){
        $this->display();
    }
    public function editWindow(){
    	$this->display();
    }
    public function listWindow(){
    	$this->display();
    }
}