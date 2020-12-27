<?php
require_once 'app.php';
$c = isset($_GET['act'])?$_GET['act']:'';
$t = isset($_POST['text'])?$_POST['text']:'';
$p = isset($_GET['p'])?intval($_GET['p']):1;
$id = isset($_GET['id'])?intval($_GET['id']):0;
$db = new DbHelpClass();
switch ($c) {
	 case "login":
		 $pass = $_POST['pass'];
	     if($config['pass'] == md5(KEY.$pass)){
			$_SESSION[KEY.'admin'] = 1; 
		    logmsg(1,'登录成功');
		 }else{
		    logmsg(0,'登录失败');
		 }
		 break;
	 case "logout":
         $_SESSION[KEY.'admin'] = 0; 
		 break;
	 case "vcode":
         get_captch(); 
		 break;
     case "new":
        $list = $db->getdata("select * from `content` order by top desc,lastime desc limit 0,$id");
	    if(!empty($list)){
		  $tmp = '';
		  $r = isset($_GET['r'])?$_GET['r']:'';
		  foreach($list as $v){
			   $tit = $v['lock'] == 1 ? '留言审核中' :mb_substr(str_replace(PHP_EOL,'',$v['content']),0,$p,'utf-8');
			   $did = $v['parent'] == 0 ?'#card-'.$v['id']:'#r'.$v['id'];
	           $tmp .= '<li><strong>'.$v['author'].'：</strong><a href="'.$r.$did.'">'.$tit.'</a></li>';
	      }
		  echo 'document.write(\''.addslashes($tmp).'\');';
		}
	   break;
     case "add":
         $arr['content'] = strip_tags(trim($_POST['content']));
         $vcode = strtolower(trim($_POST['vcode']));		 
		 if($config['captch'] == 1 and $vcode != $_SESSION[KEY . 'captch'] and ADMIN==0){
		   logmsg(0,'验证码错误！');
		 }
		 $arr['author'] = ADMIN?$config['author']:mb_substr(strip_tags(trim($_POST['author'])),0,20,'utf-8');
         if(empty($arr['author'])){$arr['author'] ='匿名网友';}
         $arr['mail'] = strip_tags(trim($_POST['mail']));
		 if(!empty($arr['mail']) && filter_var($arr['mail'], FILTER_VALIDATE_EMAIL)==false){
		   logmsg(0,'Email格式错误！');
		 }
         $arr['ip'] = get_ip();
		 $arr['lock'] = ADMIN?0:$config['lock'];
		 $arr['parent'] = $id;
		 $arr['aid'] = ADMIN;
		 $sql = arr_sql('content','insert',$arr);
		 setcookie('pname',$arr['author'],time()+3600*24*30,'/');
	     setcookie('pmail',$arr['mail'],time()+3600*24*30,'/');
		 $b =  $db->runsql($sql,$arr);
		 $_SESSION[KEY.'add'] = $arr;
		 //$_SESSION[KEY.'mail'] = $arr['mail'];
		 if($arr['parent']>0){$db->runsql("update `content` set lastime=:lastime where id=:id",array('lastime'=>date('Y-m-d H:i:s'),'id'=>$id));}
		 logmsg($b,$arr);
		 break;
     case "lock":
	 case "lockr":
	  ckadm();
      $lock = $t=='隐藏'?1:0;
	  $ltxt = $t=='隐藏'?'公开':'隐藏';
	  $b = $db->runsql("update `content` set lock={$lock} where id=:id",array("id"=>$id));
	  logmsg($b,$ltxt);
	  break;
  case "top":
	  ckadm();
      $top = $t=='置顶'?1:0;
	  $ttxt = $t=='置顶'?'取消':'置顶';
	  $b = $db->runsql("update `content` set top={$top} where id=:id",array("id"=>$id));
	  logmsg($b,$ttxt);
	  break;
     case "del":
     case "delr":
		 ckadm();
         $b =  $db->runsql("delete from `content` where id=:id or parent=:id",array("id"=>$id));
	  logmsg($b);
	  break;
   case "mail":
	   ckadm();
	   if(!empty($_SESSION[KEY.'add'])){		    
            require_once 'mail.php';
			if($_SESSION[KEY.'add']['parent']>0){			 
				  $mail = $_POST['mail'];
				  $sub = '留言被回复';
		          $text = '<p>您在['.$config['title'].']的留言被'.$config['author'].'回复：'.$_SESSION[KEY.'add']['content'].'</p>';				 
			}		 
			$_SESSION[KEY.'add']='';		 
            echo send_mail($mail,$text,$sub);
       }else{
          logmsg(0,'非法请求！'); 
       } 	   
	   break;
   case "save":
	   ckadm();       
   $arr['content'] = strip_tags(trim($_POST['content'])); 
   $arr['lock'] = 0; 
   $sql = arr_sql('content','update',$arr);   
   $arr['id'] = $id; 
   $b =  $db->runsql($sql,$arr); 
	  logmsg($b);
	  break;
  case "sconfig":
   ckadm();
   $arr = $_POST; 
   $_SESSION[KEY.'config'] = '';   
   if(empty($arr['pass'])){
	   unset($arr['pass']);
   }else{	 
      $arr['pass'] = md5(KEY.$arr['pass']);
   }
   $sql = arr_sql('config','update',$arr);
   $arr['id'] = 1;
   $b =  $db->runsql($sql,$arr);   
   logmsg($b);		   
	  break;		 
default:
   logmsg(0,'非法请求！');

}