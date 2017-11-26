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

    public function add(Request $request)
    {   
      if($request->isPost()){
        // return 111;
        $data=input('post.');
        $query=Db::table('wd_admin_user');
        $user=$query->where('username',$data['username'])->find();
        if($user){
            return json(array('msg'=>'管理员已存在','code'=>100));
        }
 
       $datas=[
            'username'=>$data['username'],
            'password'=>md5($data['password']),
            'create_time'=>$data['create_time'],
        ];
        
        $res=Db::name('admin_user')->insert($datas);   
        // return $res;
        if($res){
            return json(array('msg'=>'添加成功','code'=>200));
        }else{
            return json(array('msg'=>'添加失败','code'=>0));
        }
      }
        return view();
    }

   public function test(){
        
       if($request->isPost){
           $data=input('get.');
            
        }
   }

   public function del(Request $request)
   {
        if($request->isPost()){
            $data=input('get.');
            $query=Db::table('wd_admin_user');
            $res=$query->where('id',$data['id'])->delete();   //真删除
            // $res=$query->update('status',0)->where($data['id']);  //伪删除
            if($res){
                return json(array('msg'=>'删除成功','code'=>200));
            }else{
                return json(array('msg'=>'删除失败','code'=>0));
            }
        }
   }


   public function edit(Request $request)
   {  
       $id=input('get.');

        // if($request->isPost){
         var_dump($id['id']) ;
            
        // }
        return view();
   }


   public function check()
   {

   }
}