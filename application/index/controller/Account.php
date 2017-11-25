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
use app\common\model\Upload as UploadModel;
use app\common\model\Forum as ForumModel;
use app\common\model\Comment as CommentModel;
use app\common\model\User as UserModel;
use app\common\model\Love as LoveModel;
use app\common\model\Connect as ConnectModel;
use app\common\model\Makecon as MakeconModel;
class Account extends Controller{
    public function _initialize(){
    	parent::_initialize();
    	$this->assign('nav','1'); 
    	 $user=new UserModel();
    	 $uid=input('get.uid','','intval');
    	 if(session('?userid')){
    	 	$uid=session('userid');
    	 }
    	 if(!empty($uid)){
    	 	$userone=$user->where('id',$uid)->find();
    	 	
    	 	$userone['age']=$this->birthday($userone['birth']);
    	 	if($userone['tech']){
    	 		$uarr=explode('、',$userone['tech']);
    	 	}
    	 	if($userone['userhome']){
    	 		$uaddr=explode(',',$userone['userhome']);
    	 	}
    	 	
    	 	$this->assign('uarr',$uarr);
    	 	$this->assign('uaddr',$uaddr[0]);
    	 	$this->assign('userone',$userone);
    	 	/*关注数量*/
    	 	$connect=new ConnectModel();
    	 	$cuser=$connect->where('userobj='.$uid)->count();
    	 	$userc=$connect->where('usered='.$uid)->count();
    		$this->assign('cuser',$cuser);
    		$this->assign('userc',$userc);
    	 }
    	 
    	 
    }    
    /*作品列表*/
    public function index(){
    	$this->assign('type','index');
    	$this->assign('nav','account');  
    	$user = new UserModel();
    	$member = new MakeconModel();
    	$love = new LoveModel();
    	$uid=input('get.uid','','intval');
    	if(session('?userid')){
    		$uid=session('userid');
    	}
    	if($uid){
    		$map['uid']=$uid;
//  		$map['ftype']=1;
    		$cval=$member->where($map)->order('pdate desc')->limit(9)->select();
    		foreach($cval as $key=>$val){
    			$lres=$love->where('mid='.$val['id'])->count();
    			$cval[$key]['love']=$lres;
    			$ures=$user->where('id='.$val['uid'])->find();
    			$cval[$key]['name']=$ures['username'];
    			$cval[$key]['userhead']=$ures['userhead'];
    			$cval[$key]['zhiwei']=$ures['zhiwei'];
    		}
    		$this->assign('cval',$cval);

    	}else{
    		
    	}
    	      	
        return view();
    }
    /*个人简介*/
    public function info(){ 
    	$this->assign('nav','account'); 
    	$this->assign('type','info');  
    	$u=New UserModel();
    	$uid=input('uid','','intval');
    	if(session('userid')){
    		$uid=session('userid');
    	}else{
    		if(!$uid){
    			exit;
    		}
    	}
    	$uone=$u->where('id='.$uid)->find();    	
        $uone['age']=$this->birthday($uone['birth']);
        $date=explode('-',$uone['birth']);
        $uone['xingzuo']=$this->get_xingzuo($date[1],$date[2]);        
        $this->assign('uone',$uone);
        return view();
    }
    /*星座*/
    function get_xingzuo($month, $day){ 
	    // 检查参数有效性 
	    if ($month < 1 || $month > 12 || $day < 1 || $day > 31) 
	    {
	        return (false);
	    } 
	    // 星座名称以及开始日期 
	    $signs = array( 
	        array( "20" => "宝瓶座"), 
	        array( "19" => "双鱼座"), 
	        array( "21" => "白羊座"), 
	        array( "20" => "金牛座"), 
	        array( "21" => "双子座"), 
	        array( "22" => "巨蟹座"), 
	        array( "23" => "狮子座"), 
	        array( "23" => "处女座"), 
	        array( "23" => "天秤座"), 
	        array( "24" => "天蝎座"), 
	        array( "22" => "射手座"), 
	        array( "22" => "摩羯座") 
	    );
	    list($sign_start, $sign_name) = each($signs[(int)$month-1]); 
	    if ($day < $sign_start){
	        list($sign_start, $sign_name) = each($signs[($month -2 < 0) ? $month = 11: $month -= 2]); 
	    }
	    return $sign_name; 
	}
	/*生日*/
    function birthday($birthday){ 
		 $age = strtotime($birthday); 
		 if($age === false){ 
		  return false; 
		 } 
		 list($y1,$m1,$d1) = explode("-",date("Y-m-d",$age)); 
		 $now = strtotime("now"); 
		 list($y2,$m2,$d2) = explode("-",date("Y-m-d",$now)); 
		 $age = $y2 - $y1; 
		 if((int)($m2.$d2) < (int)($m1.$d1)){
		 	$age -= 1;
		 }		   
		 return $age; 
	} 
    /*圈子*/
    public function loc(){ 
    	$this->assign('nav','account'); 
    	$this->assign('type','loc');      	
        return view();
    }
    /*喜欢列表*/
    public function love(){   
    	$this->assign('nav','account'); 
    	$this->assign('type','love');
    	$user = new UserModel();
    	$member = new MakeconModel();
    	$love = new LoveModel();
    	$uid=input('get.uid','','intval');
    	if(!$uid){
    		if(session('?userid')){
    			$map['uid']=session('userid');
    		}else{
    			exit;
    		}
    	}else{
    		$map['uid']=$uid;
    	}
    	
		$cval=$member->where($map)->order('pdate desc')->limit(3)->select();
		foreach($cval as $key=>$val){
			$lres=$love->where('mid='.$val['id'])->count();
			$cval[$key]['love']=$lres;
			$ures=$user->where('id='.$val['uid'])->find();
    			$cval[$key]['name']=$ures['username'];
    			$cval[$key]['userhead']=$ures['userhead'];
    			$cval[$key]['zhiwei']=$ures['zhiwei'];
		}
		$this->assign('cval',$cval);

    	
    	
    	/*文章*/
		$forum = new ForumModel();
    	if(session('?userid')){
    		$m['uid']=session('userid');
    		$m['ftype']=1;
    		$conval=$forum->where($m)->order('time desc')->limit(2)->select();
    		
    	}else{
    		if(!$uid){
    			exit;
    		}
    		$m['uid']=$uid;
    		$m['ftype']=1;
    		$conval=$forum->where($m)->order('time desc')->limit(2)->select();
    		
    	}
    	foreach($conval as $key=>$val){
    		$ures=$user->where('id='.$val['uid'])->find();
    		$conval[$key]['name']=$ures['username'];
			$conval[$key]['userhead']=$ures['userhead'];
			$conval[$key]['zhiwei']=$ures['zhiwei'];
    	}
    	  $this->assign('conval',$conval); 	
        return view();
    }
    
    /*更多作品列表*/
    public function filmlist(){     	
    	$member = new MakeconModel();
    	$love = new LoveModel();
    	$user = new UserModel();
    	if (request()->isPost()) { 
//	    	if(session('?userid')){
	    		$map['uid']=input('post.uid','','intval');
	    		if($map['uid']){
	    			
	    		}else{
	    			if(session('?userid')){
	    				$map['uid']=session('userid');
	    			}else{
	    				echo 'error';exit;
	    			}	    			
	    		}
	    		$page=input('post.page');
	    		$start=$page*9;
	    		$cval=$member->where($map)->order('pdate desc')->limit($start,9)->select();
	    		if($cval){
	    			foreach($cval as $key=>$val){
	    				$val['pname']=mb_substr(htmlspecialchars_decode($val['pname']),0,100);
	    				$lres=$love->where('mid='.$val['id'])->count();
    			        $cval[$key]['love']=$lres;
    			        $ures=$user->where('id='.$val['uid'])->find();
			    		$cval[$key]['name']=$ures['username'];
						$cval[$key]['userhead']=$ures['userhead'];
						$cval[$key]['zhiwei']=$ures['zhiwei'];
	    			}
	    			return json($cval);
	    		}else{
	    			return json(array('code' => 0, 'msg' => '没有数据了'));
	    		}
//	    	}
	    }
    }
    
    /*文章列表*/
    public function content(){ 
    	$this->assign('nav','account');  
    	$this->assign('type','content');
    	$member = new ForumModel();
    	$user = new UserModel();
    	$uid=input('get.uid','','intval');
    	if(session('?userid')){
    		$map['uid']=session('userid');
    		$map['ftype']=1;
    		$cval=$member->where($map)->order('time desc')->limit(3)->select();
    	}else{
    		if($uid){
    			$map['uid']=$uid;
	    		$map['ftype']=1;
	    		$cval=$member->where($map)->order('time desc')->limit(3)->select();	
    		}else{
    			exit;
    		}
    	}
    	foreach($cval as $key=>$val){
    		$ures=$user->where('id='.$val['uid'])->find();
    		$cval[$key]['name']=$ures['username'];
			$cval[$key]['userhead']=$ures['userhead'];
			$cval[$key]['zhiwei']=$ures['zhiwei'];
    	}
//  	dump($cval);exit;
    	$this->assign('cval',$cval);
        return view();
    }
    /*更多文章列表*/
    public function conlist(){     	
    	$member = new ForumModel();
    	$user = new UserModel();
    	if (request()->isPost()) { 
    		$uid=input('uid','','intval');
	    	if(session('?userid')){
	    		$uid=session('userid');
	    	}
	    	if(!$uid){
	    		exit;
	    	}
	    		$map['uid']=$uid;
	    		$map['ftype']=1;
	    		$page=input('post.page');
	    		$start=$page*3;
	    		$cval=$member->where($map)->order('time desc')->limit($start,3)->select();
	    		if($cval){
	    			foreach($cval as $key=>$val){
	    				$val['content']=mb_substr(htmlspecialchars_decode($val['content']),0,200);
	    			    $ures=$user->where('id='.$val['uid'])->find();
			    		$cval[$key]['name']=$ures['username'];
						$cval[$key]['userhead']=$ures['userhead'];
						$cval[$key]['zhiwei']=$ures['zhiwei'];
	    			}
	    			return json($cval);
	    		}else{
	    			return json(array('code' => 0, 'msg' => '没有数据了'));
	    		}
	    	
	    }
    }
    //私信
    public function setmsg(){
    	$this->assign('nav','account'); 
    	$this->assign('msg','msg');
    	
    	return view();
    }
    /*文章详情*/
   function detail(){
   	  $this->assign('nav','lists'); 
   	  $id=input('get.id','','intval');
   	  $forum = new ForumModel();
   	  $user=new UserModel();
   	  $cone=$forum->find($id);
   	  $this->assign('cone',$cone);
   	  /*用户信息*/
   	  $userone=$user->find($cone['uid']);
   	  $this->assign('userone',$userone);
   	  
   	  $member = new CommentModel();
   	  if (request()->isPost()) { 
		   $data = input('post.');
		   if($data['tid']){
		   	  $tid=$data['tid'];
		   }else{
		   	   $tid=0;
		   }
		   if(intval($data['mid'])){
		   	  $mid=$data['mid'];
		   }else{
		   	   $mid=0;
		   }
		   if($data['cid']){
		   	  $cid=$data['cid'];
		   }else{
		   	   $cid=0;
		   }
		   
		   if(!$data['comments']){
		   	return json(array('code' => 0, 'msg' => '内容不能为空'));
		   }
		   $member->uid=session('userid');
		   $member->tid=$tid;
		   $member->fid=$cid;
		   $member->mid=$mid;
		   $member->content=$data['comments'];
		   $member->reply=0;
		   $member->praise=0;
		    $member->time=time();	  
		   $member->save();
		   if($member->id){
		   	  return json(array('code' => 200, 'msg' => '评论成功'));
		   }else{
		   	  return json(array('code' => 0, 'msg' => '评论失败'));
		   }
		}
		
		$page=input('get.page','');
        if($page=='all'){
        	$commentval=$member->where('fid='.$id.' and tid=0')->order('time desc')->select();
        }else{
        	$commentval=$member->where('fid='.$id.' and tid=0')->order('time desc')->limit(6)->select();
        }
		
		if($commentval){
			foreach($commentval as $key=>$val){
				$userval=$user->find($val['uid']);
				$val['uname']=$userval['username'];
				$val['uhead']=$userval['userhead'];
				$cnums=$member->where('tid='.$val['id'])->count();
				$val['cnums']=$cnums;
			}
		}
		
		$this->assign('commentval',$commentval);
		/*评论的数量*/
		$count=$member->where('fid='.$id)->count();
		$num=$member->where('fid='.$id.' and tid=0')->count();
		$this->assign('commentnum',$count);
		$this->assign('connum',$num);
		/*喜欢*/
		$love=new LoveModel();
		$lmap['cid']=$id;
		$lnums=$love->where($lmap)->count();
		$this->assign('lnums',$lnums);
		if(session('?userid')){
			$lmap['uid']=session('userid');
			$lone=$love->where($lmap)->count();
			if($lone){
				$this->assign('lone',$lone);
			}
		}
   	  return view();
   }
   /*文章评论列表页*/
  function comlists(){
  	$id=input('get.id','','intval');
  	$page=input('get.page','');
  	$member = new CommentModel();
  	$user=new UserModel();
  	
  	if($id){
  		if($page=='all'){
  			$comments=$member->where('tid='.$id)->order('time desc')->select();
  		}else{
  			$comments=$member->where('tid='.$id)->limit(6)->order('time desc')->select();
  		}
  		
  		if($comments){
			foreach($comments as $key=>$val){
				$userval=$user->find($val['uid']);
				$val['uname']=$userval['username'];
				$val['uhead']=$userval['userhead'];
				$cnums=$member->where('tid='.$val['id'])->count();
				$val['cnums']=$cnums;
			}
		}
  		$this->assign('comments',$comments);
  		
//		dump($comments);
  	}else{
  		echo '页面有错误';exit;
  	}
  	return view();
  }
  /*作品评论*/
  function filmlists(){
  	$id=input('get.id','','intval');
  	$page=input('get.page','');
  	$member = new CommentModel();
  	$user=new UserModel();
  	$mone=$member->where('id',$id)->find();
  	$this->assign('mid',$mone['mid']);
  	if($id){
  		if($page=='all'){
  			$comments=$member->where('tid='.$id)->order('time desc')->select();
  		}else{
  			$comments=$member->where('tid='.$id)->limit(6)->order('time desc')->select();
  		}
  		
  		if($comments){
			foreach($comments as $key=>$val){
				$userval=$user->find($val['uid']);
				$val['uname']=$userval['username'];
				$val['uhead']=$userval['userhead'];
				$cnums=$member->where('tid='.$val['id'])->count();
				$val['cnums']=$cnums;
			}
		}
  		$this->assign('comments',$comments);
  		
//		dump($comments);
  	}else{
  		echo '页面有错误';exit;
  	}
  	return view();
  }
  
  /*喜欢按钮*/
  function lovefilm(){
  	if (request()->isPost()) { 
	  	$id=input('post.id','','intval');
	  	$member = new LoveModel();
	  	if(session('?userid')){
	  		$map['mid']=$id;
	  		$map['uid']=session('userid');
	  		$con=$member->where($map)->find();
	  		if($con){
		  		return json(array('code' => 0, 'msg' => '你已经点击过了'));
		  	}else{
		  		$map['ltime']=date('Y-m-d H:i:s');
		  		$rel=$member->data($map)->save();
		  		if($rel){
		  			return json(array('code' => 200, 'msg' => '提交成功'));
		  		}else{
		  			return json(array('code' => 0, 'msg' => '点击失败'));
		  		}
		  	}
	  	}      
    }
  }
 
 /*喜欢文章按钮*/
  function lovecontent(){
  	if (request()->isPost()) { 
	  	$id=input('post.id','','intval');
	  	$member = new LoveModel();
	  	if(session('?userid')){
	  		$map['cid']=$id;
	  		$map['uid']=session('userid');
	  		$con=$member->where($map)->find();
	  		if($con){
		  		$rel=$member->where($map)->delete();
		  		if($rel){
		  			return json(array('code' => 200, 'msg' => '取消成功'));
		  		}else{
		  			return json(array('code' => 0, 'msg' => '点击失败'));
		  		}
		  	}else{
		  		$map['ltime']=date('Y-m-d H:i:s');
		  		$rel=$member->data($map)->save();
		  		if($rel){
		  			return json(array('code' => 200, 'msg' => '添加成功'));
		  		}else{
		  			return json(array('code' => 0, 'msg' => '点击失败'));
		  		}
		  	}
	  	}      
	  	
	  	
	  	
    }
  }
  /*点赞*/
  function dopraise(){
  	if (request()->isPost()) { 
	  	$id=input('post.id','','intval');
	  	$request = Request::instance();
        $ip=$request->ip();
        if(cookie('contentip'+$id)==$ip){
        	return json(array('code' => 0, 'msg' => '你已点过，请不要重复点'));
        }
	  	$member = new CommentModel();
	  	$con=$member->find($id);
	  	$praise=intval($con['praise'])+1;
	  	$map['praise']=$praise;
	  	$c['id']=$id;
		$doval=$member->where('id',$id)->update($map);
	 
	   if($doval){
	   	cookie('contentip'+$id, $ip, 360000);
	   	  return json(array('code' => 200, 'msg' => $praise));
	   }else{
	   	  return json(array('code' => 0, 'msg' => '点赞失败'));
	   }
    }
  }
   /*文章列表页*/
  function lists(){
  	$this->assign('nav','lists'); 
  	$forum = new ForumModel();
  	$map['ftype']=1;
  	$fval=$forum->where($map)->order('view desc')->limit(3)->select();
  	$this->assign('fval',$fval);  

	
	$cval=$forum->where($map)->order('time desc')->limit(3)->select();
	$this->assign('cval',$cval);
    
  	return view();
  }
  /*文章列表页加载更多*/
  public function listsmore(){     	
    	$member = new ForumModel();
    	if (request()->isPost()) {    		
    		$map['ftype']=1;
    		$page=input('post.page');
    		$start=$page*3;
    		$cval=$member->where($map)->order('time desc')->limit($start,3)->select();
    		if($cval){
    			foreach($cval as $key=>$val){
    				$val['content']=mb_substr(htmlspecialchars_decode($val['content']),0,200);
    				$val['title']=mb_substr($val['title'],0,100);
    			}
    			return json($cval);
    		}else{
    			return json(array('code' => 0, 'msg' => '没有数据了'));
    		}
	    	
	    }
    }
  /*发布文章*/
 function publish(){
 	$this->assign('nav','lists'); 
 	if (request()->isPost()) { 
	   $data = input('post.');
	   $member = new ForumModel();

	   if(!$data['title']){
	   	return json(array('code' => 0, 'msg' => '标题不能为空'));
	   }
	   if(!$data['content']){
	   	return json(array('code' => 0, 'msg' => '内容不能为空'));
	   }
	   $member->uid=session('userid');
	   $member->title=$data['title'];
	   $member->open=1;
	   $member->content=$data['content'];
	  $member->thumb=$data['path'];
	    $member->time=date('Y-m-d H:i:s');
	  
	    $member->ftype=1;
	  
	   $member->save();
	   if($member->id){
	   	  return json(array('code' => 200, 'msg' => '发布成功'));
	   }else{
	   	  return json(array('code' => 0, 'msg' => '发布失败'));
	   }
	}
 	return view();
 }
 /*上传封面*/
 public function upload(){
 	$file=new UploadModel();
 	return json($file->upfile('images'));
 }
 function upImg(){
 	$file=new UploadModel();
 	$info=$file->upfile('images');
 	if($info['code']==200){
 		$arr['code']=0;
 		$arr['msg']='';
 		$arr['data']['src']=$info['headpath'];
 	}else{
 		$arr['msg']=$info['msg'];
 	}
 	return json($arr);
 }
}