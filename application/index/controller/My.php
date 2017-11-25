<?php
namespace app\index\controller;
use org\Http;
use think\Controller;
use think\Session;
use think\Db;
use think\Config;
use think\Model;
use think\Cache;
use think\captcha\Captcha;
use app\common\model\Upload as UploadModel;
use app\common\model\User as UserModel;
use app\common\model\Ver as VerModel;
use app\common\model\Makecon as MakeconModel;
class My extends Controller{
    public function _initialize(){
    	parent::_initialize();
    	if(session('?userid') && session('?email')){
    		
    	}else{
    		$this->redirect('reg/login');
    	}
    }    
      
    /*个人修改显示*/    
    public function index(){
    	$this->assign('my','index'); 
    	$user=new UserModel();
    	if(!session('userid')){
    		exit;
    	}
    	$uone=$user->where('id='.session('userid'))->find();
//  	$ubirth=explode(',',$uone['birth']);
//  	
//  	$this->assign('ubirth',$ubirth);
    	$this->assign('uone',$uone);
    	
        return view();
    }
    /*实名认证显示*/
   public function ver(){
   	if(!session('userid')){
    		exit;
    	}
    	$this->assign('my','ver');  
    	 $ver=new VerModel();
    	 $vres=$ver->where('uid='.session('userid'))->find();  	
        $this->assign('vone',$vres['vid']);
        return view();
    }
    /*发布作品显示*/
    public function pub(){
    	if(!session('userid')){
    		exit;
    	}
    	$this->assign('my','pub');     	
        return view();
    }
    /*上传图片*/
    function upuserhead(){
    	if(!session('userid')){
    		exit;
    	}
    	$file=new UploadModel();
 	    return json($file->upfile('images'));
    }
    /*提交个人资料修改*/
    function upuser(){
    	if(!session('userid')){
    		exit;
    	}
    	if (request()->isPost()) {
    		$data = input('post.');
    		$user=new UserModel(); 
    		if(!$data['username']){
    			return json(array('code' => 0, 'msg' => '昵称不能为空'));
    		}
    		if(!$data['name']){
    			return json(array('code' => 0, 'msg' => '姓名不能为空'));
    		}
    		if(!$data['mobile']){
    			return json(array('code' => 0, 'msg' => '电话不能为空'));
    		}
    		if(!$data['hangye']){
    			return json(array('code' => 0, 'msg' => '行业不能为空'));
    		}
    		if(!$data['birth'] || $data['birth']=="--"){
    			unset($data['birth']);
    		}
    		if(!$data['userhome'] || $data['userhome']=="请选择,请选择,请选择"){
    			unset($data['userhome']);
    		}
	    	if(session('?userid')){
	    		$map['uid']=session('userid');
//	    		foreach($data as $key=>$val){
//	    			if($val){
//	    				$con[$key]=$val;
//	    			}
//	    		}
	    		$cval=$user->where('id',$map['uid'])->update($data);
	    		if($cval){
	    			return json(array('code' => 200, 'msg' => '保存成功'));
	    		}else{
	    			return json(array('code' => 0, 'msg' => '保存失败'));
	    		}
	    	}
	    }
    }
    /*实名认证提交*/
    function identy(){
    	if(!session('userid')){
    		exit;
    	}
    	if (request()->isPost()) {
    		$data = input('post.');
    		$ver=new VerModel();
    		$con['vimg']=$data['identy'];
    		if(!$con['vimg']){
    			return json(array('code' => 0, 'msg' => '请上传身份证或护照'));
    		}
    		$con['vyear']=$data['year'];
    		$con['vzhiye']=$data['zhiye'];
    		if(!$con['vzhiye']){
    			return json(array('code' => 0, 'msg' => '职业不能为空'));
    		}
    		$con['uid']=session('userid');
    		$con['vstatus']=1;
    		$con['vdate']=date('Y-m-d H:i:s');
    		$nums=$ver->where('uid='.session('userid'))->count();
    		if($nums){
    			return json(array('code' => 0, 'msg' => '你已提交过，不要重复提交'));
    		}
    		$res=$ver->data($con)->save();
    		if($res){
    			return json(array('code' => 200, 'msg' => '提交成功'));
    		}else{
    			return json(array('code' => 0, 'msg' => '提交失败'));
    		}
    	}
    }
    /*发布作品提交*/
    function makecon(){
    	if(!session('userid')){
    		exit;
    	}
    	if (request()->isPost()) {
    		$data = input('post.');
    		$m=new MakeconModel();
    		$con['prourl']=$data['fengmian'];
    		if(!$con['prourl']){
    			return json(array('code' => 0, 'msg' => '请上传封面'));
    		}
    		$con['pname']=$data['pro_name'];
    		if(!$con['pname']){
    			return json(array('code' => 0, 'msg' => '作品名称不能为空'));
    		}
    		$con['pdate']=date('Y-m-d H:i:s');
    		$con['path']=$data['url_pro'];
    		if(!$con['path']){
    			return json(array('code' => 0, 'msg' => '作品链接不能为空'));
    		}
    		$con['uid']=session('userid');
    		$con['pstatus']=1;
    		
    		$res=$m->data($con)->save();
    		if($res){
    			return json(array('code' => 200, 'msg' => '提交成功'));
    		}else{
    			return json(array('code' => 0, 'msg' => '提交失败'));
    		}
    	}
    }




}