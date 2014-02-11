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
  <p class="well-sm">
    <span class="pull-left">方案一：当天气温≤</span>
    <span class="col-lg-1" style="margin-top:-6px;">
      <input type="text" class="form-control" <?php if($tm != 0): ?>value="<?php echo ($tm); ?>"<?php else: ?>value="25"<?php endif; ?> id='lttem'/>
    </span>℃时，用户看到以下推荐商品
  </p>
  <div class="row text-center">
  	<?php if($list1 != ''): if(is_array($list1)): $key2 = 0; $__LIST__ = $list1;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($key2 % 2 );++$key2;?><div class="col-sm-6 col-md-3">
      <div class="thumbnail">
        <div class="caption">
          <p>商品数字ID：</p>
          <p class="js-official-id"><?php echo ($vo["num_iid"]); ?></p>
          <p><input type="text" class="form-control hide" id='spid1<?php echo ($key2); ?>' value='<?php echo ($vo["num_iid"]); ?>' onchange="getcolor(<?php echo ($key2); ?>,1,<?php echo ($vo["num_iid"]); ?>);"></p>
          <span id='img1<?php echo ($key2); ?>' style='display:none;'></span>
          <span id='rid1<?php echo ($key2); ?>' style='display:none;'><?php echo ($vo["id"]); ?></span>
          <button type="button" class="btn btn-info js-official-alter">替换</button>
          <?php if($key <= 4): ?><button type="button" class="btn btn-success js-official-save hide" onclick="reco(<?php echo ($key2); ?>,1,1);">保存</button>
          <?php else: ?>
          <button type="button" class="btn btn-success js-official-save hide" onclick="reco(<?php echo ($key2); ?>,1,2);">保存</button><?php endif; ?>
          
        <div id='c<?php echo ($vo["num_iid"]); ?>'>
        <?php if($vo['color'] != ''): if(is_array($vo['color'])): $k2 = 0; $__LIST__ = $vo['color'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$cvo): $mod = ($k2 % 2 );++$k2; if($cvo["cvalue"] != ''): ?><img src='__ROOT__/<?php echo ($cvo["url"]); ?>' title='<?php echo ($cvo["cvalue"]); ?>' width='50px' height='50px' onclick="getcolorUrl(<?php echo ($key2); ?>,1,'<?php echo ($cvo["url"]); ?>');">&nbsp;&nbsp;<?php endif; endforeach; endif; else: echo "" ;endif; endif; ?>
        </div>
        </div>

      </div>
    </div><?php endforeach; endif; else: echo "" ;endif; ?>
   <?php else: ?>
    <div class="col-sm-6 col-md-3">
      <div class="thumbnail">
        <div class="caption">
          <p>商品数字ID：</p>
          <p class="js-official-id">18632267046</p>
          <p><input type="text" class="form-control hide" id='spid10'></p>
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
          <button type="button" class="btn btn-info js-official-alter">替换</button>
          <button type="button" class="btn btn-success js-official-save hide" onclick="reco(7,1,2);">保存</button>
        </div>
      </div>
    </div><?php endif; ?>
  </div>
  <p class="well-sm">方案二：当天气温＞<span id='twotm'><?php if($tm != 0): echo ($tm); else: ?>25<?php endif; ?></span>℃时，用户看到以下推荐商品</p>
  <div class="row text-center">
  	<?php if($list2 != ''): if(is_array($list2)): $key3 = 0; $__LIST__ = $list2;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo2): $mod = ($key3 % 2 );++$key3;?><div class="col-sm-6 col-md-3">
      <div class="thumbnail">
        <div class="caption">
          <p>商品数字ID：</p>
          <p class="js-official-id"><?php echo ($vo2["num_iid"]); ?></p>
          <p><input type="text" class="form-control hide" id='spid2<?php echo ($key3); ?>' value='<?php echo ($vo2["num_iid"]); ?>' onchange="getcolor(<?php echo ($key3); ?>,2,<?php echo ($vo2["num_iid"]); ?>);"></p>
          <span id='img2<?php echo ($key3); ?>' style='display:none;'></span>
          <span id='rid2<?php echo ($key3); ?>' style='display:none;'><?php echo ($vo2["id"]); ?></span>
          <button type="button" class="btn btn-info js-official-alter">替换</button>
          <?php if($key3 <= 4): ?><button type="button" class="btn btn-success js-official-save hide" onclick="reco(<?php echo ($key3); ?>,2,1);">保存</button>
          <?php else: ?>
          <button type="button" class="btn btn-success js-official-save hide" onclick="reco(<?php echo ($key3); ?>,2,2);">保存</button><?php endif; ?>
        </div>
        <div id='c<?php echo ($vo2["num_iid"]); ?>'>
        <?php if($vo2['color'] != ''): if(is_array($vo2['color'])): $i = 0; $__LIST__ = $vo2['color'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$cvo2): $mod = ($i % 2 );++$i; if($cvo2["cvalue"] != ''): ?><img src='__ROOT__/<?php echo ($cvo2["url"]); ?>' title='<?php echo ($cvo2["cvalue"]); ?>' width='50px' height='50px' onclick="getcolorUrl(<?php echo ($key3); ?>,2,'<?php echo ($cvo2["url"]); ?>');">&nbsp;&nbsp;<?php endif; endforeach; endif; else: echo "" ;endif; endif; ?>
        </div>
      </div>
    </div><?php endforeach; endif; else: echo "" ;endif; ?>
   <?php else: ?>
    <div class="col-sm-6 col-md-3">
      <div class="thumbnail">
        <div class="caption">
          <p>商品数字ID：</p>
          <p class="js-official-id">18632267046</p>
          <p><input type="text" class="form-control hide" id='spid20'></p>
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
          <button type="button" class="btn btn-info js-official-alter">替换</button>
          <button type="button" class="btn btn-success js-official-save hide" onclick="reco(7,2,2);">保存</button>
        </div>
      </div>
    </div><?php endif; ?>
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
<script type='text/javascript'>
var url = "<?php echo U('Official/add');?>",colorurl = "<?php echo U('Official/getcolor');?>",indexurl = "<?php echo U('Official/index');?>";
	/*
    type:1为方案一2为方案2
	issue:1为上装2为下装
	*/
function reco(id,type,issue){
var kuid = $('#spid'+type+id).val(),lttem = $('#lttem').val(),rid = $('#rid'+type+id).text(),srcvalue = $('#img'+type+id).text();

  $.post(url,{kuid:kuid,type:type,rid:rid,issue:issue,lttem:lttem,srcvalue:srcvalue},function(data,status){
	  var da = eval("("+data+")");
	  if(da.flag){
	  $('#spid'+type+id).val(da.num_iid);
	  $('#lttem').val(da.stm);
	  if(da.rid){
      $('#rid'+type+id).text(da.rid);
       }
      $('#twotm').text(lttem);
      window.location.href = indexurl;
	  }else{
       //alert(da.msg);
	  }
	  });
}
//获取颜色
function getcolor(id,type,iid){
 var num_iid = $('#spid'+type+id).val();
 if(num_iid>0){
 $.post(colorurl,{num_iid:num_iid,type:type,id:id},function(data,status){
    if(data['code']==1){
 	$('#c'+iid).html(data['msg']);
    }else if(data['code']==-1){
    alert(data['msg']);	
    }	
 });	
 }
}
function getcolorUrl(id,type,src){
	$('#img'+type+id).text(src);
}
</script>