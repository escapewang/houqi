<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;

//会员模块
 class Member extends Controller{

        public function index(){
            // var_dump(config('template.view_path'));
  			echo md5("admin888");
            return view();
        }

        public function add(){

        	return view();
        }
    }
 
