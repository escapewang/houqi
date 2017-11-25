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
use app\common\model\Forum as ForumModel;
use app\common\model\Comment as CommentModel;
use app\common\model\User as UserModel;
use app\common\model\Love as LoveModel;
use app\common\model\Connect as ConnectModel;
use app\common\model\Makecon as MakeconModel;
class Forum extends Controller{
    public function _initialize(){
    	parent::_initialize();
    	$this->assign('nav','forum'); 
    	 $user=new UserModel();
    	if(session('?userid')){
    		$userone=$user->find(session('userid'));
    	    $this->assign('userone',$userone);
    	}
    }    
    /*详情*/
    public function detail(){
    	$this->assign('type','index');       	
        return view();
    }
    public function lists(){
    	return view();
    }
    /*评论*/
   public function comment(){
   	  return view();
   }
   /*首页*/
    public function index(){
    	$makecon=New MakeconModel();
    	$user=New UserModel();
    	$love=New LoveModel();
    	$mval=$makecon->where('pstatus=2')->order('count desc')->limit(7)->select();
    	if($mval){
    		foreach($mval as $key=>$val){
    			$ures=$user->where('id='.$val['uid'])->find();
    			$mval[$key]['name']=$ures['username'];
    			$mval[$key]['userhead']=$ures['userhead'];
    			$mval[$key]['zhiwei']=$ures['zhiwei'];
    			$lres=$love->where('mid='.$val['id'])->count();
    		    $mval[$key]['love']=$lres;
    		}
	    	$this->assign('mval',$mval);
	    	$this->assign('mone',$mval[0]);
    	}
    	
    	/*其他影片*/
    	
    	return view();
    }
    public function publish(){
    	return view();
    }
    /*视频作品详情*/
   function film(){
   	  $film=New MakeconModel();
   	  $user=New UserModel();
   	  $love=New LoveModel();
   	  $id=input('get.id','','intval');
   	  if(!$id){
   	  	echo 'error';exit;
   	  }
   	  $filmval=$film->where('id='.$id)->find();
   	  $count['count']=$filmval['count']+1;
   	  
   	  $film->where('id='.$id)->update($count);
   	  $filmval['pdate']=strtotime($filmval['pdate']);
   	  $filmnums=$love->where('mid='.$filmval['id'])->count();
   	  $this->assign('filmval',$filmval);
   	  $this->assign('filmcount',$count['count']);
   	  $this->assign('filmnums',$filmnums);
   	  
   	  /*评论的数量*/
   	  $comment=New CommentModel();
   	  $comdata['mid']=$id;
// 	  $comdata['tid']=0;
   	  $commentnums=$comment->where($comdata)->count();
   	  $this->assign('commentnums',$commentnums);
   	  
   	  /*评论*/
   	  $page=input('get.page','');
        if($page=='all'){
        	$commentval=$comment->where('mid='.$id.' and tid=0')->order('time desc')->select();
        }else{
        	$commentval=$comment->where('mid='.$id.' and tid=0')->order('time desc')->limit(4)->select();
        }
        if($commentval){
			foreach($commentval as $key=>$val){
				$userval=$user->find($val['uid']);
				$val['uname']=$userval['username'];
				$val['uhead']=$userval['userhead'];
				$cnums=$comment->where('tid='.$val['id'])->count();
				$val['cnums']=$cnums;
			}
		}
   	 $this->assign('commentval',$commentval);
   	 /*发布人信息*/
   	 $f_one=$user->where('id',$filmval['uid'])->field('username,tech,userhead,id')->find();
   	  $this->assign('f_one',$f_one);
   	  
   	  /*作品*/
   	  $filmlists=$film->where('uid='.$filmval['uid'])->order('count desc')->limit(2)->select();
   	  $this->assign('filmlists',$filmlists);
   	  return view();
   }
   /*作品评论*/
   function filmcomment(){
   	  $member = new CommentModel();
   	  if(!session('userid')){
   	  	echo 'error';exit;
   	  }
   	  if (request()->isPost()) { 
		   $data = input('post.');
		   if($data['tid']){
		   	  $tid=$data['tid'];
		   }else{
		   	   $tid=0;
		   }
		   if(!$data['commentfilm']){
		   	return json(array('code' => 0, 'msg' => '内容不能为空'));
		   }
		   $member->uid=session('userid');
		   $member->tid=$tid;
		   $member->mid=$data['mid'];
		   $member->content=$data['commentfilm'];
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
   	  
   }
}