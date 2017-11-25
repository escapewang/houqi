<?php
namespace app\admin\controller;
use think\Controller;
use think\Session;
use think\Db;
use think\Model;
use think\Cache;
class Index extends controller{
    // public function _initialize(){
    // 	parent::_initialize();
    // 	$this->assign('nav','index');
    	
    // }
    /*首页*/
    public function index(){
    	// $this->assign('index',2);      	
        return view();
    }

    public function welcome(){
        return view();
    }
}