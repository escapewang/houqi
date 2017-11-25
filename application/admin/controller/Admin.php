<?php
namespace app\admin\controller;
use think\Controller;
use think\Session;
use think\Db;
use think\Model;
use think\Cache;
use think\Request;


//后台管理员模块
class Admin extends controller
{
    // public function _initialize(){
    // 	parent::_initialize();
    // 	$this->assign('nav','index');   	
    // }
    


    public function index()
    {
    	$data=Db::query('select * from wd_admin_user');
        $this->assign('data',$data); 	
        return view();
    }

    public function add()
    {   

      if(request()->isPost()){
        echo 111;
      }
        return view();
    }
    
}