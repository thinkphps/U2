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
    <li class="active"><strong>标签管理</strong></li>
  </ol>
  <form class="form-horizontal" role="form" action="<?php echo U('Tags/export');?>" method='post' enctype="multipart/form-data">
    <div class="form-group">
      <label for="inputName" class="col-lg-2 control-label">标签批量导入</label>
      <div class="col-lg-3">
        <input type="file" class="form-control" id="inputName" name='Filedata'>
      </div>
      <div class="col-lg-3">
        <button type="submit" class="btn btn-success">提交</button>
      </div>
    </div>
  </form>

  <form class="form-horizontal" role="form" action="<?php echo U('Tags/add');?>" method='post'>
    <div class="form-group">
    <label class="col-lg-2 control-label">添加新标签</label>
      <div class="row tags-select">
        <div class="col-sm-6 col-md-5">
          <div class="thumbnail">
            <div class="caption clearfix">
              <div class="form-group">
                <label class="col-lg-4 control-label">请选择</label>
                <div class="col-lg-4">
                  <select class="form-control" name='ptag'>
                    <option value='0'>请选择</option>
            <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value='<?php echo ($vo["id"]); ?>'><?php echo ($vo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-lg-4 control-label">请输入新标签</label>
                <div class="col-lg-4">
                  <input type="text" class="form-control" name='tagname'>
                </div>
                <div class="col-lg-2">
                  <input type="submit" class="btn btn-success" value="添加" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-lg-4 control-label">适合人群</label>
                <div class="col-lg-4" style="margin-top:7px;">
                  <p><input type="radio"  value='1' name='typename'> WOMEN</p>
                  <p><input type="radio"  value='2' name='typename'> MEN</p>
                  <p><input type="radio"  value='3' name='typename'> KIDS</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
  
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