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
<div class="container official">
  <ol class="breadcrumb">
    <li><a href="<?php echo U('Index/index');?>">首页</a></li>
    <li class="active"><strong>官方推送</strong></li>
  </ol>
 <?php if($list1 != ''): ?><p class="well-sm">
    <span class="pull-left">方案一：上海市、江苏省、浙江省、福建省、台湾省、湖北省、湖南省、江西省、安徽省、广东省、广西、海南省、重庆市、贵州省、四川省、云南省、西藏、香港、澳门的用户看到以下8件官方推荐衣物
  </p>
<div class="row text-center">
	<?php if(is_array($list1)): $key = 0; $__LIST__ = $list1;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($key % 2 );++$key;?><div class="col-sm-6 col-md-3">
      <div class="thumbnail">
        <div class="caption">
          <p>商品数字ID：</p>
          <p class="js-official-id"><?php echo ($vo["num_iid"]); ?></p>
          <p><input type="text" class="form-control hide" id='spid1<?php echo ($key); ?>' value='<?php echo ($vo["num_iid"]); ?>'></p>
		  <span id='rid1<?php echo ($key); ?>' style='display:none;'><?php echo ($vo["id"]); ?></span>
          <button type="button" class="btn btn-info js-official-alter">替换</button>
		  <?php if($key <= 4): ?><button type="button" class="btn btn-success js-official-save hide" onclick="reco(<?php echo ($key); ?>,1,1);">保存</button>
		  <?php else: ?>
          <button type="button" class="btn btn-success js-official-save hide" onclick="reco(<?php echo ($key); ?>,1,2);">保存</button><?php endif; ?>
        </div>
      </div>
    </div><?php endforeach; endif; else: echo "" ;endif; ?>
</div>
 <?php else: ?>
 <p class="well-sm">
    <span class="pull-left">方案一：上海市、江苏省、浙江省、福建省、台湾省、湖北省、湖南省、江西省、安徽省、广东省、广西、海南省、重庆市、贵州省、四川省、云南省、西藏、香港、澳门的用户看到以下8件官方推荐衣物℃时，用户看到以下推荐商品（商品数字ID为空时，按标签推荐）
  </p>
  <div class="row text-center">
    <div class="col-sm-6 col-md-3">
      <div class="thumbnail">
        <div class="caption">
          <p>商品数字ID：</p>
          <p class="js-official-id">18632267046</p>
          <p><input type="text" class="form-control hide" id='spid10'></p>
		  <span id='rid10' style='display:none;'></span>
          <button type="button" class="btn btn-info js-official-alter">替换</button>
          <button type="button" class="btn btn-success js-official-save hide" onclick="reco(0,1,1);">保存</button>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-3">
      <div class="thumbnail">
        <div class="caption">
          <p>商品数字ID：</p>
          <p class="js-official-id">18632267046</p>
          <p><input type="text" class="form-control hide" id='spid11'></p>
		  <span id='rid11' style='display:none;'></span>
          <button type="button" class="btn btn-info js-official-alter">替换</button>
          <button type="button" class="btn btn-success js-official-save hide" onclick="reco(1,1,1);">保存</button>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-3">
      <div class="thumbnail">
        <div class="caption">
          <p>商品数字ID：</p>
          <p class="js-official-id">18632267046</p>
          <p><input type="text" class="form-control hide" id='spid12'></p>
		  <span id='rid12' style='display:none;'></span>
          <button type="button" class="btn btn-info js-official-alter">替换</button>
          <button type="button" class="btn btn-success js-official-save hide" onclick="reco(2,1,1);">保存</button>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-3">
      <div class="thumbnail">
        <div class="caption">
          <p>商品数字ID：</p>
          <p class="js-official-id">18632267046</p>
          <p><input type="text" class="form-control hide" id='spid13'></p>
		  <span id='rid13' style='display:none;'></span>
          <button type="button" class="btn btn-info js-official-alter">替换</button>
          <button type="button" class="btn btn-success js-official-save hide" onclick="reco(3,1,1);">保存</button>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-3">
      <div class="thumbnail">
        <div class="caption">
          <p>商品数字ID：</p>
          <p class="js-official-id">18632267046</p>
          <p><input type="text" class="form-control hide" id='spid14'></p>
		  <span id='rid14' style='display:none;'></span>
          <button type="button" class="btn btn-info js-official-alter">替换</button>
          <button type="button" class="btn btn-success js-official-save hide" onclick="reco(4,1,2);">保存</button>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-3">
      <div class="thumbnail">
        <div class="caption">
          <p>商品数字ID：</p>
          <p class="js-official-id">18632267046</p>
          <p><input type="text" class="form-control hide" id='spid15'></p>
		  <span id='rid15' style='display:none;'></span>
          <button type="button" class="btn btn-info js-official-alter">替换</button>
          <button type="button" class="btn btn-success js-official-save hide" onclick="reco(5,1,2);">保存</button>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-3">
      <div class="thumbnail">
        <div class="caption">
          <p>商品数字ID：</p>
          <p class="js-official-id">18632267046</p>
          <p><input type="text" class="form-control hide" id='spid16'></p>
		  <span id='rid16' style='display:none;'></span>
          <button type="button" class="btn btn-info js-official-alter">替换</button>
          <button type="button" class="btn btn-success js-official-save hide" onclick="reco(6,1,2);">保存</button>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-3">
      <div class="thumbnail">
        <div class="caption">
          <p>商品数字ID：</p>
          <p class="js-official-id">18632267046</p>
          <p><input type="text" class="form-control hide" id='spid17'></p>
		  <span id='rid17' style='display:none;'></span>
          <button type="button" class="btn btn-info js-official-alter">替换</button>
          <button type="button" class="btn btn-success js-official-save hide" onclick="reco(7,1,2);">保存</button>
        </div>
      </div>
    </div>
  </div><?php endif; ?>
 
  <?php if($list2 != ''): ?><p class="well-sm">方案二：辽宁省、吉林省、黑龙江省、内蒙古、北京市、天津市、河北省、山西省、 山东省、河南省、陕西省、宁夏、甘肃省、青海省、新疆的用户看到以下8件官方推荐衣物
</p>
  <div class="row text-center">
  	<?php if(is_array($list2)): $key = 0; $__LIST__ = $list2;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($key % 2 );++$key;?><div class="col-sm-6 col-md-3">
      <div class="thumbnail">
        <div class="caption">
          <p>商品数字ID：</p>
          <p class="js-official-id"><?php echo ($vo["num_iid"]); ?></p>
          <p><input type="text" class="form-control hide" id='spid2<?php echo ($key); ?>' value='<?php echo ($vo["num_iid"]); ?>'></p>
		  <span id='rid2<?php echo ($key); ?>' style='display:none;'><?php echo ($vo["id"]); ?></span>
          <button type="button" class="btn btn-info js-official-alter">替换</button>
		  <?php if($key <= 4): ?><button type="button" class="btn btn-success js-official-save hide" onclick="reco(<?php echo ($key); ?>,2,1);">保存</button>
		  <?php else: ?>
          <button type="button" class="btn btn-success js-official-save hide" onclick="reco(<?php echo ($key); ?>,2,2);">保存</button><?php endif; ?>
        </div>
      </div>
    </div><?php endforeach; endif; else: echo "" ;endif; ?>
</div> 
 <?php else: ?>
  <p class="well-sm">方案二：辽宁省、吉林省、黑龙江省、内蒙古、北京市、天津市、河北省、山西省、 山东省、河南省、陕西省、宁夏、甘肃省、青海省、新疆的用户看到以下8件官方推荐衣物
</p>
  <div class="row text-center">
    <div class="col-sm-6 col-md-3">
      <div class="thumbnail">
        <div class="caption">
          <p>商品数字ID：</p>
          <p class="js-official-id">18632267046</p>
          <p><input type="text" class="form-control hide" id='spid20'></p>
          <span id='rid20' style='display:none;'></span>
          <button type="button" class="btn btn-info js-official-alter">替换</button>
          <button type="button" class="btn btn-success js-official-save hide" onclick="reco(0,2,1);">保存</button>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-3">
      <div class="thumbnail">
        <div class="caption">
          <p>商品数字ID：</p>
          <p class="js-official-id">18632267046</p>
          <p><input type="text" class="form-control hide" id='spid21'></p>
		  <span id='rid21' style='display:none;'></span>
          <button type="button" class="btn btn-info js-official-alter">替换</button>
          <button type="button" class="btn btn-success js-official-save hide" onclick="reco(1,2,1);">保存</button>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-3">
      <div class="thumbnail">
        <div class="caption">
          <p>商品数字ID：</p>
          <p class="js-official-id">18632267046</p>
          <p><input type="text" class="form-control hide" id='spid22'></p>
		  <span id='rid22' style='display:none;'></span>
          <button type="button" class="btn btn-info js-official-alter">替换</button>
          <button type="button" class="btn btn-success js-official-save hide" onclick="reco(2,2,1);">保存</button>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-3">
      <div class="thumbnail">
        <div class="caption">
          <p>商品数字ID：</p>
          <p class="js-official-id">18632267046</p>
          <p><input type="text" class="form-control hide" id='spid23'></p>
		  <span id='rid23' style='display:none;'></span>
          <button type="button" class="btn btn-info js-official-alter">替换</button>
          <button type="button" class="btn btn-success js-official-save hide" onclick="reco(3,2,1);">保存</button>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-3">
      <div class="thumbnail">
        <div class="caption">
          <p>商品数字ID：</p>
          <p class="js-official-id">18632267046</p>
          <p><input type="text" class="form-control hide" id='spid24'></p>
		  <span id='rid24' style='display:none;'></span>
          <button type="button" class="btn btn-info js-official-alter">替换</button>
          <button type="button" class="btn btn-success js-official-save hide" onclick="reco(4,2,2);">保存</button>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-3">
      <div class="thumbnail">
        <div class="caption">
          <p>商品数字ID：</p>
          <p class="js-official-id">18632267046</p>
          <p><input type="text" class="form-control hide" id='spid25'></p>
		  <span id='rid25' style='display:none;'></span>
          <button type="button" class="btn btn-info js-official-alter">替换</button>
          <button type="button" class="btn btn-success js-official-save hide" onclick="reco(5,2,2);">保存</button>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-3">
      <div class="thumbnail">
        <div class="caption">
          <p>商品数字ID：</p>
          <p class="js-official-id">18632267046</p>
          <p><input type="text" class="form-control hide" id='spid26'></p>
		  <span id='rid26' style='display:none;'></span>
          <button type="button" class="btn btn-info js-official-alter">替换</button>
          <button type="button" class="btn btn-success js-official-save hide" onclick="reco(6,2,2);">保存</button>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-3">
      <div class="thumbnail">
        <div class="caption">
          <p>商品数字ID：</p>
          <p class="js-official-id">18632267046</p>
          <p><input type="text" class="form-control hide" id='spid27'></p>
		  <span id='rid27' style='display:none;'></span>
          <button type="button" class="btn btn-info js-official-alter">替换</button>
          <button type="button" class="btn btn-success js-official-save hide" onclick="reco(7,2,2);">保存</button>
        </div>
      </div>
    </div>
  </div><?php endif; ?>
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
<script type='text/javascript'>
var url = "<?php echo U('Official/add');?>";
	/*
    type:1为方案一2为方案2
	issue:1为上装2为下装
	*/
function reco(id,type,issue){
var kuid = $('#spid'+type+id).val();
//var lttem = $('#lttem').val();
var rid = $('#rid'+type+id).text();

  $.post(url,{kuid:kuid,type:type,rid:rid,issue:issue},function(data,status){
	  var da = eval("("+data+")");
	  if(da.flag){
	  $('#spid'+type+id).val(da.num_iid);
	  //$('#lttem').val(da.stm);
      $('#rid'+type+id).text(da.rid);
      //$('#twotm').text(lttem);
	  }else{
       //alert(da.msg);
	  }
	  });
}
</script>