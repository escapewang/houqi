<?php
namespace app\index\controller;
use org\Http;
use think\Controller;
use think\Session;
use think\Db;
use think\Config;
use think\Model;
use think\Cache;
use think\Request;
use think\captcha\Captcha;
use app\index\model\User as UserModel;
class Reg extends Controller{
    public function _initialize(){
    	parent::_initialize();
    	
    }    
    /*注册*/
    public function index(){
        
    	if (request()->isPost()) { 
    	   $data = input('post.');
    	   $member = new UserModel();
		   if(!$data['usertype']){
    	   	return json(array('code' => 0, 'msg' => '请选择用户类型'));
    	   }
    	   if(!$data['nickname']){
    	   	return json(array('code' => 0, 'msg' => '昵称不能为空'));
    	   }
    	   if(!$data['email']){
    	   	return json(array('code' => 0, 'msg' => '邮箱不能为空'));
    	   }
    	   $map['email']=$data['email'];
    	   $user = $member->where($map)->find();
           if($user){
           	return json(array('code' => 0, 'msg' => '邮箱已注册，请更换或直接登录'));
           }
    	   if(!$data['phone']){
    	   	return json(array('code' => 0, 'msg' => '手机号码不能为空'));
    	   }
    	   
    	   if(!$data['pass']){
    	   	return json(array('code' => 0, 'msg' => '密码不能为空'));
    	   }
    	   $member->username=$data['nickname'];
    	   $member->email=$data['email'];
    	   $member->mobile=$data['phone'];
    	    $member->password=sha1(md5($data['pass']));
    	    $member->regtime=date('Y-m-d H:i:s');
    	   $request = Request::instance();
			$member->usertype=$data['usertype'];
    	   $member-> userip=$request->ip();
    	    $member->status=1;
    	  
    	   $member->save();
    	   if($member->id){
    	   	  return json(array('code' => 200, 'msg' => '注册成功'));
    	   }else{
    	   	  return json(array('code' => 0, 'msg' => '注册失败'));
    	   }
    	}
        return view();
    }
    /*登录*/
   public function login(){
    	$member = new UserModel();
    	if (request()->isPost()) { 
    	   $data = input('post.');
    	   $member = new UserModel();
		  
    	   if(!$data['email']){
    	   	return json(array('code' => 0, 'msg' => '邮箱不能为空'));
    	   }
    	   if(!$data['pass']){
    	   	return json(array('code' => 0, 'msg' => '密码不能为空'));
    	   }
    	   $map['email']=$data['email'];
    	   $user = $member->where($map)->find();
           if($user){
           	    if($user['password']==sha1(md5($data['pass']))){
           		    session('phone', $user['mobile']);
    				session('email', $user['email']);
    				session('userid', $user['id']);
    				
    				$member->update(
    						[
    								'last_login_time' => date('Y-m-d H:i:s'),
    								'last_login_ip'   => $this->request->ip(),
    								'id'              => $user['id']
    						]
    				);
           		return json(array('code' => 200, 'msg' => '登录成功'));
           	}else{
           		return json(array('code' => 0, 'msg' => '密码不正确'));
           	}
           }else{
           	return json(array('code' => 0, 'msg' => '邮箱不存在，请更换或立即注册'));
           }
    	   
    	   
    	   
    	}
        return view();
    }
    /*退出*/
    public function logout(){
    	session("phone", NULL);
        session("userid", NULL);
       session("email", NULL);
        return json(array('code' => 200, 'msg' => '退出成功'));
      //  return NULL;
    }
}