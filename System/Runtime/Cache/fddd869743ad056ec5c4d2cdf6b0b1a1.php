<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php if($result['id'] != ''): echo ($result["id"]); else: ?>Uniqlo<?php endif; ?></title>
<link rel="stylesheet" type="text/css" href="__TMPL__Public/css/bootstrap.css?575" />
<link rel="stylesheet" type="text/css" href="__TMPL__Public/css/bootstrap-theme.css?575" />
<link rel="stylesheet" type="text/css" href="__TMPL__Public/css/uniqlo.css?575" />
</head>
<body>
<nav class="navbar navbar-default" role="navigation">
  <!-- Brand and toggle get grouped for better mobile display -->
  <div class="navbar-header">
    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
      <span class="sr-only">Toggle navigation</span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </button>
    <a class="navbar-brand" href="<?php echo U('Index/index');?>">优衣库后台</a>
  </div>
  
  <!-- Collect the nav links, forms, and other content for toggling -->
  <div class="collapse navbar-collapse navbar-ex1-collapse">
    <ul class="nav navbar-nav navbar-right">
      <li><a href="#"><?php if($aid != ''): echo ($nick); else: ?>请登录<?php endif; ?></a></li>
      <li><button type="button" class="btn btn-warning" id='loginout'>退出</button></li>
    </ul>
  </div><!-- /.navbar-collapse -->
</nav>
<div class="container">
  <ol class="breadcrumb">
    <li><a href="<?php echo U('Index/index');?>">首页</a></li>
    <li class="active"><strong>门店管理</strong></li>
  </ol>
  <form class="form-inline form-group" role="form" action="<?php echo U('Shop/index');?>" method='post'>
    <div class="form-group">
      <label class="sr-only" for="exampleInputEmail2">请输入关键字</label>
      <input type="text" class="form-control" id="exampleInputEmail2" placeholder="请输入关键字" name='keyword'>
    </div>
    <button type="submit" class="btn btn-success">搜索</button>
    <a href="<?php echo U('Shop/create');?>" class="btn btn-success pull-right">新建门店</a>
  </form>
  <table class="table table-striped table-hover text-center table-responsive table-condensed">
    <thead>
      <tr>
        <th>后台ID</th>
        <th>门店名称</th>
        <th>门店地址</th>
        <th>操作</th>
      </tr>
    </thead>
    <tbody>
    <?php if(is_array($shop)): $i = 0; $__LIST__ = $shop;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
        <td><?php echo ($vo["id"]); ?></td>
        <td><?php echo ($vo["sname"]); ?></td>
        <td><?php echo ($vo["saddress"]); ?></td>
        <td><a href="<?php echo U('Shop/shopedit',array('id'=>$vo['id']));?>" class="btn btn-primary">编辑</a> <a href="<?php echo U('Shop/del',array('id'=>$vo['id']));?>" class="btn btn-danger">删除</a></td>
      </tr><?php endforeach; endif; else: echo "" ;endif; ?>
    </tbody>
  </table>
  <div class="text-center">
    <ul class="pagination">
         <?php echo ($page); ?>
    </ul>
  </div>
</div>
<script type="text/javascript" src="__TMPL__Public/js/jquery.js"></script>
<script type="text/javascript" src="__TMPL__Public/js/bootstrap.js"></script>
<script type="text/javascript" src="__TMPL__Public/js/main.js"></script>
</body>
</html>
<script type='text/javascript'>
var outurl = "<?php echo U('Index/loginout');?>";
$(function(){
	$('#loginout').click(function(){
	window.location.href=outurl;
	});
});	
</script>