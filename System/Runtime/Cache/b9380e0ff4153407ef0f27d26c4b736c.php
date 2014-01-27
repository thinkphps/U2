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
    <li class="active"><strong>商品管理</strong></li>
  </ol>

  <form class="form-inline form-group" role="form" action="<?php echo U('Products/index');?>" method='post'>
    <div class="form-group">
      <label class="sr-only" for="exampleInputEmail2">请输入关键字</label>
      <input type="text" class="form-control" id="exampleInputEmail2" placeholder="请输入关键字" name='keyword' value='<?php echo ($keyword); ?>'> 
    </div>
    <div class="form-group">
      <label>一级分类</label>
      <select name='cate1' id='cate1id'>
	  <option value=''>请选择</option>
      <?php if(is_array($onecate)): $i = 0; $__LIST__ = $onecate;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo1): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo1["cid"]); ?>" <?php if($vo1['cid'] == $cate1): ?>selected='selected'<?php endif; ?>><?php echo ($vo1["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
	  </select>
	  <?php if($twocate != ''): ?><label class='twocate'>二级分类</label>
      <select name='cate2' class='twocate' id='cate2id'>
	  <option value=''>请选择</option>
	  <?php if(is_array($twocate)): $i = 0; $__LIST__ = $twocate;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$svo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($svo["cid"]); ?>" <?php if($svo['cid'] == $cate2): ?>selected='selected'<?php endif; ?> ><?php echo ($svo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
	  </select>
	  <?php else: ?>
	   <label class='twocate' style="display:none;">二级分类</label>
      <select name='cate2' class='twocate' style="display:none;" id='cate2id'>
	  <option value=''>请选择</option>
	  </select><?php endif; ?>
    </div>
	<div class="form-group">
	<label>是否打标签</label>
      <select name='ist'>
        <option value=''>请选择</option>
        <option value='1' <?php if($istag == 1): ?>selected='selected'<?php endif; ?>>未打</option>
        <option value='2' <?php if($istag == 2): ?>selected='selected'<?php endif; ?>>已打</option>
	  </select>
	 </div>
	<div class="form-group">
	<label>是否存疑</label>
      <select name='isdoubt'>
        <option value=''>请选择</option>
        <option value='1' <?php if($isdoubt == 1): ?>selected='selected'<?php endif; ?>>无疑</option>
        <option value='2' <?php if($isdoubt == 2): ?>selected='selected'<?php endif; ?>>存疑</option>
	  </select>
	 </div>
    <button type="submit" class="btn btn-success">搜索</button>
  </form>

  <table class="table table-striped table-hover text-center table-responsive table-condensed">
    <thead>
      <tr>
        <th>后台ID</th>
        <th>商品数字ID</th>
        <th>商品分类</th>
        <th>缩略图</th>
        <th>商品名称</th>
        <th>价格</th>
        <th>库存</th>
		<th>是否打标签</th>
        <th>操作</th>
      </tr>
    </thead>
    <tbody>
    <?php if(is_array($goods)): $i = 0; $__LIST__ = $goods;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
        <td><?php echo ($vo["id"]); ?></td>
        <td><a href="<?php echo ($vo["detail_url"]); ?>" target='__blank'><?php echo ($vo["num_iid"]); ?></a></td>
        <td><?php echo ($vo["cname"]); ?></td>
        <td><img src="__ROOT__/<?php echo ($vo["pic_url"]); ?>" alt="" width='100px' height='100px'></td>
        <td><?php echo ($vo["title"]); ?></td>
        <td>￥<?php echo ($vo["price"]); ?></td>
        <td><?php echo ($vo["num"]); ?></td>
		<td><?php if($vo['istag'] == 2): ?>已打<?php endif; ?></td>
        <td><a class="btn btn-primary" href="<?php echo U('Productsedit/index',array('id'=>$vo['id'],'p'=>$p,'keyword'=>$keyword,'ist'=>$istag,'cate1'=>$cate1,'cate2'=>$cate2,'isdoubt'=>$isdoubt));?>">编辑</a></td>
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
<script type="text/javascript">
var ur = '<?php echo U('Products/addcate');?>';
$(function(){
	$('#cate1id').change(function(){
		var selvalue = $('#cate1id option:selected').val();
		$.post(ur,{cateid : selvalue},function(data,status){
			var da = eval("("+data+")");
			$('.twocate').css('display','inline');
            $('#cate2id').html(da.str);
			});
		});
	})
</script>