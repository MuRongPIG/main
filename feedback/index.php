<?php
$t1 = microtime(true);
include 'app/class/app.php';
include 'app/class/page.php';
$s = isset($_GET['s']) ? htmlspecialchars($_GET['s']) : '';
?><!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo empty($s)?$config['title']:'搜索：'.$s.'_'.$config['title'];?></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <link rel="stylesheet" href="app/style/css/bootstrap.min.css" >
    <style>.w1000{max-width:1000px;margin: 0 auto;}.card-body p:last-child{margin-bottom:0}.rcontent{margin-bottom:.5rem;margin-left:1rem;padding-top:.5rem;border-top:1px dashed #dee2e6}</style>
</head>
<body>
	<header class="bg-dark">
    <nav class="navbar navbar-expand-lg navbar-dark w1000">
  <a class="navbar-brand" href="index.php"><?php echo $config['title'];?></a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navmenu">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navmenu">
    <ul class="navbar-nav mr-auto nav">	 
      <li class="nav-item">
        <a class="nav-link" href="https://crosst.chat">十字街</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="index.php">首页</a>
      </li>
	  <!--<li class="nav-item">
        <a class="nav-link" href="#message" data-toggle="tab">发布留言</a>
      </li>-->      
	  <?php if(ADMIN){
		  echo '<li class="nav-item">
        <a class="nav-link" href="#config" data-toggle="tab">系统设置</a>
      </li><li class="nav-item">
        <a class="nav-link" id="logout" href="javascript:;">退出</a>
      </li>';
	  }else{
	  echo '<li class="nav-item">
        <a class="nav-link" href="#login" data-toggle="tab">Mod 登录</a>
      </li>';
		  }?>
    </ul>
	   <div class="my-2"></div>
	    <form class="navbar-form navbar-left" role="search">
		       <div class="input-group">
			        <input type="text" class="form-control" name="s" placeholder="Search"><span class="input-group-append"><button type="submit" class="btn btn-info btn-search">搜索</button></span>			        
		       </div>
		       
	       </form>
 
  </div>
</nav> </header>   
    <!--<div class="container-fluid">-->
<div class="col-lg-12 mt-3 tab-content w1000"> 
<form id="login" class="form tab-pane">
 <div class="form-group"><input type="password" id="pass" class="form-control" required placeholder="请输入密码" maxlength="50" /></div> 
 <div class="form-group"><button type="button" id="dologin" class="btn btn-primary btn-block">登录系统</button></div>
</form>

<?php if(ADMIN){?>
   <form id="config" class="tab-pane">
    <div class="form-group">
    <label>留言板名称</label>
    <input type="text" class="form-control" value="<?php echo $config['title'];?>" name="title" required />
    </div>
		 <div class="form-group">
    <label>管理员名称</label>
      <input type="text" class="form-control" value="<?php echo $config['author'];?>" placeholder="管理员昵称" name="author" />
    </div>
	 <div class="form-group">
    <label>管理密码</label>
      <input type="text" class="form-control" value="" placeholder="不修改请留空" name="pass" />
    </div>
    <div class="form-group">
    <label>每页显示留言数量</label>
    <input type="text" class="form-control" value="<?php echo $config['pagesize'];?>" name="pagesize" required />
    </div>
    <div class="form-group">
    <label>是否显示验证码</label>
    <select class="form-control" name="captch"><option value="1" <?php echo $config['captch']==1?'selected':''; ?>>是</option><option value="0" <?php echo $config['captch']==0?'selected':''; ?>>否</option></select>
    </div>
    <div class="form-group">
    <label>是否需要审核</label>
    <select class="form-control" name="lock"><option value="1" <?php echo $config['lock']==1?'selected':''; ?>>是</option><option value="0" <?php echo $config['lock']==0?'selected':''; ?>>否</option></select>
    </div>
	    <div class="form-group">
    <label>邮箱服务器</label>
    <input type="text" class="form-control" value="<?php echo $config['smtp'];?>" name="smtp" />
    </div>
	    <div class="form-group">
    <label>邮箱地址</label>
    <input type="text" class="form-control" value="<?php echo $config['email'];?>" name="email" />
    </div>
	    <div class="form-group">
    <label>邮箱密码</label>
    <input type="text" class="form-control" value="<?php echo $config['epass'];?>" name="epass" />
    </div>
    <button type="button" id="save" class="btn btn-primary btn-block">保存设置</button>    
    <div class="alert alert-success mt-3" style="display:none">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
      <span id="res"></span>
    </div> 
  </form>
  <?php } ?>
 <div id="list" class="tab-pane active">
 <form id="message" class="form">
<div class="form-group">
<textarea class="form-control" rows="5" id="content" placeholder="留言内容*"></textarea>
</div>
<div class="form-group<?php hide();?>">
<input type="text" id="author" class="form-control" value="<?php echo @$_COOKIE['pname'];?>" placeholder="昵称（选填）" maxlength="20" /> 
</div>
<div class="form-group<?php hide();?>">
<input type="text" id="mail" class="form-control" value="<?php echo @$_COOKIE['pmail'];?>" placeholder="邮箱（选填，回复邮件通知）" maxlength="50" /> 
</div>
<?php
if( $config['captch'] == 1){
?>
<div class="form-group row<?php hide();?>">
<div class="col-6"><input type="text" id="vcode" class="form-control" maxlength="4" placeholder="验证码*" /></div>
<div class="col-6"><img src="app/class/ajax.php?act=vcode" alt="点击刷新" title="点击刷新" id="vcode_img" style="vertical-align:middle;cursor:pointer;" /></div>
</div> 
<?php }?>
<div class="form-group"><button type="button" id="add" class="btn btn-primary btn-block">提交留言</button></div>
</form> 
 <?php
 $db =new DbHelpClass();
 $p = isset($_GET['p']) ? intval($_GET['p']) : 1;
 $per_page = $config['pagesize'];
 $start = $per_page * ($p - 1);
 if (empty($s)) {
	  $csql = 'SELECT count(id) as c FROM `content` where `parent`=0';
	  $where = 'where `parent`=0';
	  $_arr = array();
 }else{
      $csql = 'SELECT count(id) as c FROM `content`  where `parent`=0 and `content` like :s';
	  $where = 'where `parent`=0 and `content` like :s';
	  $_arr = array("s" => $s);	  
 }
 $count = $db->total($csql,$_arr);
 $list = $db->getdata("select * from `content` $where order by top desc,lastime desc limit $start,$per_page", $_arr);
 //$ids = join(',',array_column($list,'id'));
 $ids = join(',', array_map('array_shift',$list));
 $clist = $db->getdata("select * from `content` where parent in ($ids)");
 foreach($clist as $vv){
    $children[$vv['parent']][] = $vv; 
 }
foreach($list as $v){
?>	
<div class="card mb-3" id="card-<?php echo $v['id'];?>" data-id="<?php echo $v['id']?>" style="font-size:14px;">
  <div class="card-header<?php echo $v['aid']==1?' text-danger':''?>"><?php echo view_ip($v['author'],$v['mail']);?> <?php echo timeago($v['addtime']);?> <?php if($v['top']==1) echo '<span style="color:#F00;">[置顶]</span>';?><?php if($v['lock']==1) echo '<span style="color:#F00;">[未审核]</span>';?><span class="p-1"><a data-act="reply" class="click float-right" href="javascript:;">回复</a></span></div>
  <div class="card-body">
   <p class="content"><?php echo  view_content($v['content'],$v['lock']);?></p>
   <?php foreach($children[$v['id']] as $vv){?>          
		  <p id="r<?php echo $vv['id']?>" class="rcontent"><strong<?php echo $vv['aid']==1?' class="text-danger"':''?>><?php echo $vv['author'];?></strong><span class="text-secondary" style="font-size:12px">(<?php echo timeago($vv['addtime']);?>)</span>：<span class="reply"><?php echo view_content($vv['content'],$vv['lock']);?>  <?php if(ADMIN){?><a data-id="<?php echo $vv['id']?>" data-act="lockr" class="click" href="javascript:;"><?php echo $vv['lock']==1?"公开":"隐藏" ?></a> <a data-id="<?php echo $vv['id']?>" data-act="delr" class="click" href="javascript:;">删除</a><?php }?></span></p>		   
	<?php }?>
  </div>     
  <?php if(ADMIN){?>
 <div class="card-footer">
                        <a data-act="top" class="click" href="javascript:;"><?php echo $v['top']==1?"取消":"置顶" ?></a>  
                        <a data-act="lock" class="click" href="javascript:;"><?php echo $v['lock']==1?"公开":"隐藏" ?></a>                    
                        <a data-act="edit" class="click" href="javascript:;">编辑</a>                          
                        <a data-act="del" class="click" href="javascript:;">删除</a>
</div>
<?php
}
?>
</div>              
<?php
}
?>      
            
            <nav aria-label="Page navigation ">
                <ul class="pagination" id="pager">
                       <li class="page-item disabled d-none d-sm-block"><span class="page-link"> 共计：<?php echo $count; ?> 条记录 每页:<?php echo $per_page; ?>条</span></li>
  <?php

        $page_config['base_url'] =   "?p="; //当前的url，如果有参数需要拼接一下url       
        $page_config['total_rows'] = $count; //传递总数
        $page_config['per_page'] = $per_page; //传递每页的数量
        $page_config['cur_page'] = $p; //传递当前页码
        $pageStr = new Page($page_config);
        $pagelist = $pageStr->create_links(); //创建新页码
		echo $pagelist;
  ?>
                </ul>
            </nav>			
            </div><!--list end-->             
        </div>		
   <!-- </div>-->  
<footer class="p-5 text-center"><?php echo $config['title'];?> - <?php echo $config['ver'];?> - <br> <a href="https://crosst.chat"> 十字街 </a> <br>「加载用时 <?php $t2 = microtime(true); echo round($t2-$t1,3); ?>s」</footer>   
<!--对话框-->
<!-- 模态框 -->
<div class="modal fade" id="myModal">
  <div class="modal-dialog">
    <div class="modal-content"> 
      <!-- 模态框头部 -->
      <div class="modal-header">
        <h4 class="modal-title">系统提示</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div> 
      <!-- 模态框主体 -->
      <div class="modal-body" id="msg">
        模态框内容..
      </div> 
      <!-- 模态框底部 -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">关闭</button>
      </div> 
        </div><!-- /.modal-content -->  
      </div><!-- /.modal-dialog -->  
    </div><!-- /.modal -->  
     <!-- 信息删除确认 -->  
    <div class="modal fade" id="delcfmModel">  
      <div class="modal-dialog">  
        <div class="modal-content">  
          <div class="modal-header">  
		    <h4 class="modal-title">提示信息</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>  
              
          </div>  
          <div class="modal-body">  
            <p>您确认要删除吗？</p>  
          </div>  
          <div class="modal-footer">              
             <button type="button" class="btn btn-default" data-dismiss="modal">取消</button> 
			 <button type="button" class="btn btn-primary" data-dismiss="modal">确定</button>              
          </div>  
        </div><!-- /.modal-content -->  
      </div><!-- /.modal-dialog -->  
    </div><!-- /.modal -->  

<script src="app/style/js/jquery.min.js"></script>
<script src="app/style/js/bootstrap.min.js"></script>
<script>
 let captch = <?php echo ADMIN?0:$config['captch'];?>,
 lock = <?php echo $config['lock'];?>,
 delid = 0,
 old = '',reid=0
function msg(txt){
    $('#msg').html(txt)
	$('#myModal').modal('show')
}
function capl(id){
     $('#card-'+id+' .card-body').children().show()
	 $('#card-'+id+' .rebox').hide()

}
function md(id){
   $('html,body').animate({scrollTop:$('#'+id).offset().top-20},300)
}
$(function(){
    $('#vcode_img').click(function(){this.src='app/class/ajax.php?act=vcode&t='+Math.random()});
    $('#add').click(function(){
		let content= $('#content').val().trim()
		let author= $('#author').val().trim()
		let mail= $('#mail').val().trim()
		let vcode =  $('#vcode').val()
		let did = reid
		if(content ==''){
			msg('请填写留言内容')		
			return false
		}
		if(vcode =='' && captch==1){
			msg('请填写验证码')			 
			return false;
		}		 
		$.post('app/class/ajax.php?act=add&id='+reid,{'content':content,'vcode':vcode,'author':author,'mail':mail},function(res){			
			if(res.result==200){
				$('#content').val('')
				$('#vcode').val('')
				$('#vcode_img').attr('src','app/class/ajax.php?act=vcode&t='+Math.random())
			    if(lock==1){
					//msg('留言已经提交请等待审核!')				 
				}else{
					//msg('留言成功!')
					//location.reload()
				}
				 did++
				 if(reid>0){				   
			           oid.text('回复')
				       $('#message').prependTo('#list')
					   $('#card-'+reid+' .card-body').append('<p id="n'+did+'" class="rcontent border-success"><strong>'+res.message.author+'</strong><span class="text-secondary" style="font-size:12px">(刚刚)</span>：<span class="reply">'+res.message.content+'</span></p>')
					   $('#card-'+reid).insertAfter('#message')
					   let email =   $('#card-'+reid+' .mail').text()
		    //doajax(that,act,id,data)
			          if(email !='' && res.message.aid==1){			  
			              doajax('','mail','',{'mail':email})
			           }					  
					   reid = 0
				 }else{
				   $('#message').after('<div id="n'+did+'" class="card mb-3 border-success" style="font-size:14px;"><div class="card-header">'+res.message.author+' <span class="mail">'+res.message.mail+'</span> 刚刚</div><div class="card-body"><p class="content">'+res.message.content+'</p></div></div>')
				   //doajax('','mail','','')
				  }
				   md('n'+did)
			}else{
				msg(res.message)			    		
			}
			 
		}, 'json')		 
	})

   $('#save').click(function(){
	   let data = $("#config").serialize();
       $.post("./app/class/ajax.php?act=sconfig",data , function(res) {
		 $('#res').text(res.message)
		 $('.alert').show().fadeOut(2000)
	   }, 'json')
       }   
   )


	 $('#dologin').click(function(){
	   let pass =  $('#pass').val()
       $.post("./app/class/ajax.php?act=login",{pass:pass} , function(res) {
		   if(res.result==200){
		      location.reload();
		   }else{
			 msg('密码错误，登陆失败!')
		     return false
		   }
	   }, 'json')
       }   
   ) 

   $('#navmenu').on('click','.nav-link',function(e){
	   if(e.target.id=='logout'){
	       $.get("./app/class/ajax.php?act=logout" , function(res) {
		    location.reload()
	      })
	   }
       $('#navmenu').removeClass('show')
   })

    $('#delcfmModel').on('click', '.btn-primary',function (e) {
	    doajax(e,'del',delid,'')
	})
   let oid = '';
   $('.card').on('click','.click',function(e){
	    let that = $(this)
		let act  = that.data("act")
        let id   = that.offsetParent().data("id")
		let data = {}
		if(act=='edit'){
		   //msg('修改中!')
		   //return false
		   $('.rebox').hide()
           $('#card-'+id+' .card-body').children().hide()
		  if($('#card-'+id+' .rebox').length>= 1){
		     $('#card-'+id+' .rebox').show()
		  }else{
		   data.content = $('#card-'+id+' .content').text()
           //data.reply = $('#card-'+id+' .reply').text()
		   old = data.reply
           let rebox = '<div class="rebox"><div class="form-group"><textarea required="required" placeholder="请输入留言内容..." rows="3" class="tcontent form-control">'+data.content+'</textarea> </div><div class="form-group"><button data-act="save" name="re" id="re" class="btn btn-primary click"> 保 存 </button> <button onclick="capl('+id+')" class="btn btn-danger"> 取 消 </button></div></div>'
		   $('#card-'+id+' .card-body').append(rebox)
		  }
		}else if(act=='reply'){
			 //console.log(oid)
			// console.log(typeof(oid))			
			let txt = that.text()
			let msghf = $('#message')
			if(txt=='取消'){				 
				reid = 0
			    that.text('回复')
				msghf.prependTo('#list')
			}else{
			  if(reid>0) oid.text('回复')
              oid = that;
              that.text('取消')
			  reid = id			
			  msghf.insertAfter($('#card-'+id+' .content'))
			  md('message')
              $('#content').focus()
			}
			 

			//msg('功能构建中...')
		}else if(act=='save'){
		    data.content = $('#card-'+id+' .tcontent').val()
            //data.reply =   $('#card-'+id+' .treply').val()
		    //data.mail =   $('#card-'+id+' .mail').text()
		    doajax(that,act,id,data)
			//if(data.mail !='' && old!=data.reply && data.reply!=''){			  
			   //doajax(that,'mail',id,data)
			//}
		}else{	
		   if(act == 'del'){
			  $('#delcfmModel').modal('show')
			  delid = id
			  return false
		   }else if(act=='delr'){
		         id = that.data("id")				  
		   }else if(act=='lockr'){
		         id = that.data("id")				  
		   }
		  data.text =  $(this).text()
          doajax(that,act,id,data)
		}		
  })

   function doajax(that,act,id,data){   
      $.post("./app/class/ajax.php?act="+act+"&id="+id, data , function(res) {	
			 if(res.result==200){
				 if(act == 'del'){
				   $('#card-'+id).remove()
				 }else if(act == 'mail'){
				   }else if(act == 'delr'){ $('#r'+id).remove()}else if(act == 'save'){
				   $('#card-'+id+' .content').text(data.content);
                    //$('#card-'+id+' .reply').text(data.reply);
		          capl(id)	
				   }else{
			        that.text(res.message)
				 }
			 }else{
			   msg(res.message)
			 }
	     }, 'json')   
   }
})
</script>
</body>
</html>