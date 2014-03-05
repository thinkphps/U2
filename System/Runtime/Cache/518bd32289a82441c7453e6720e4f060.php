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
<div class="jumbotron text-center">
  <h2>优衣库后台</h2>
  <a href="<?php echo U('Products/index');?>" class="btn btn-success btn-lg">商品管理</a>
  <a href="<?php echo U('Shop/index');?>" class="btn btn-success btn-lg">门店管理</a>
  <a href="<?php echo U('Tags/index');?>" class="btn btn-success btn-lg">标签管理</a>
  <a href="<?php echo U('Official/index');?>" class="btn btn-success btn-lg">官方推送</a>
  <a href="<?php echo U('Feedback/index');?>" class="btn btn-success btn-lg">客户反馈</a>
  <a href="<?php echo U('User/index');?>" class="btn btn-success btn-lg">注册用户统计</a>
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