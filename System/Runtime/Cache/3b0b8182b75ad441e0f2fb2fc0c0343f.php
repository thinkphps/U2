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
    <li><a href="<?php echo U('Products/index');?>">商品管理</a></li>
    <li class="active"><strong>编辑商品</strong></li>
  </ol>
  <form class="form-horizontal products-edit" role="form" action="<?php echo U('Productsedit/doedit');?>" method='post'>
  <input type='hidden' name='id' value='<?php echo ($result["id"]); ?>'>
  <input type='hidden' name='num_iid' value='<?php echo ($result["num_iid"]); ?>'>
  <input type='hidden' name='widf' value="<?php echo ($gtaglist[0]['wid']); ?>">
  <input type='hidden' name='wids' value="<?php echo ($gtaglist[1]['wid']); ?>">
    <div class="form-group">
      <label class="col-lg-1 control-label">性别：</label>
      <div class="row col-lg-12">
        <div class="col-lg-1">
          <select class="form-control" name='gtype' id='gtypeid'>
            <option value="">请选择</option>
            <option value="0" <?php if($gtaglist[0]['gtype'] == '0'): ?>selected='selected'<?php endif; ?>>ALL</option>
            <option value="1" <?php if($gtaglist[0]['gtype'] == '1'): ?>selected='selected'<?php endif; ?>>WOMEN</option>
            <option value="2" <?php if($gtaglist[0]['gtype'] == '2'): ?>selected='selected'<?php endif; ?>>MEN</option>
            <option value="3" <?php if($gtaglist[0]['gtype'] == '3'): ?>selected='selected'<?php endif; ?>>KIDS</option>
          </select>
        </div>
        <label class="pull-left control-label">部位：</label>
        <div class="col-lg-1">
          <select class="form-control" name='isud' id='isudid'>
            <option value="">请选择</option>
            <option value="1" <?php if($gtaglist[0]['isud'] == '1'): ?>selected='selected'<?php endif; ?>>上装</option>
            <option value="2" <?php if($gtaglist[0]['isud'] == '2'): ?>selected='selected'<?php endif; ?>>下装</option>
            <option value="4" <?php if($gtaglist[0]['isud'] == '4'): ?>selected='selected'<?php endif; ?>>套装</option>
            <option value="3" <?php if($gtaglist[0]['isud'] == '3'): ?>selected='selected'<?php endif; ?>>配饰</option>
            <option value="5" <?php if($gtaglist[0]['isud'] == '5'): ?>selected='selected'<?php endif; ?>>内衣</option>
          </select>
        </div>
        <label class="pull-left control-label">指数1：</label>
        <div class="col-lg-1">
          <select class="form-control" name='wid[]' id='zhis1' onchange="changezhishu('1')">
		    <?php if(is_array($windex)): $i = 0; $__LIST__ = $windex;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$wvo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($wvo["id"]); ?>" <?php if($gtaglist[0]['wid'] == $wvo['id']): ?>selected='selected' <?php elseif($wvo['id'] == 8): ?>selected='selected'<?php endif; ?>><?php echo ($wvo["wname"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
          </select>
        </div>
        <label class="pull-left control-label">气温：</label>
        <div class="col-lg-1">
          <select class="form-control" name='stp[]' id='stm1'>
            <option value="">请选择</option>
		  <?php if(is_array($temarr)): $i = 0; $__LIST__ = $temarr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$tvo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($tvo); ?>" <?php if($gtaglist[0]['stm'] == $tvo): ?>selected='selected'<?php endif; ?>><?php echo ($tvo); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
          </select>
        </div>
        <label class="pull-left control-label">&deg;C —</label>
        <div class="col-lg-1">
          <select class="form-control" name='etp[]' id='etm1'>
            <option value="">请选择</option>
			<?php if(is_array($temarr)): $i = 0; $__LIST__ = $temarr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$tvo2): $mod = ($i % 2 );++$i;?><option value="<?php echo ($tvo2); ?>" <?php if($gtaglist[0]['etm'] == $tvo2): ?>selected='selected'<?php endif; ?>><?php echo ($tvo2); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
          </select>
        </div>
        <label class="pull-left control-label">&deg;C</label>
        <label class="pull-left control-label" id='clickaddstm'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;添加</label>
      </div>
    </div>
    <!--新添加的指数-->

     <div class="form-group addstm" id="delz2" <?php if($gtaglist[1] != ''): ?>style="display:block;"<?php else: ?>style="display:none;"<?php endif; ?>
      <label class="col-lg-1 control-label"></label>
      <div class="row col-lg-12">
        <label class="pull-left control-label">指数2：</label>
        <div class="col-lg-1">
          <select class="form-control" name='wid[]' id='zhis2' onchange="changezhishu('2')">
		    <option value=''>请选择</option>
		    <?php if(is_array($windex)): $i = 0; $__LIST__ = $windex;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$wvo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($wvo["id"]); ?>" <?php if($gtaglist[1]['wid'] == $wvo['id']): ?>selected='selected'<?php endif; ?>><?php echo ($wvo["wname"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
          </select>
        </div>
        <label class="pull-left control-label">气温：</label>
        <div class="col-lg-1">
          <select class="form-control" name='stp[]' id='stm2'>
            <option value="">请选择</option>
		  <?php if(is_array($temarr)): $i = 0; $__LIST__ = $temarr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$tvo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($tvo); ?>" <?php if($gtaglist[1]['stm'] == $tvo): ?>selected='selected'<?php endif; ?>><?php echo ($tvo); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
          </select>
        </div>
        <label class="pull-left control-label">&deg;C —</label>
        <div class="col-lg-1">
          <select class="form-control" name='etp[]' id='etm2'>
            <option value="">请选择</option>
			<?php if(is_array($temarr)): $i = 0; $__LIST__ = $temarr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$tvo2): $mod = ($i % 2 );++$i;?><option value="<?php echo ($tvo2); ?>" <?php if($gtaglist[1]['etm'] == $tvo2): ?>selected='selected'<?php endif; ?>><?php echo ($tvo2); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
          </select>
        </div>
        <label class="pull-left control-label">&deg;C</label>
		<label class="pull-left control-label" onclick="delzhishu('2')">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;删除</label>
      </div>
    </div>

    <div class="form-group addstm" id="delz3" <?php if($gtaglist[2] != ''): ?>style="display:block;"<?php else: ?>style="display:none;"<?php endif; ?>
      <label class="col-lg-1 control-label"></label>
      <div class="row col-lg-12">
        <label class="pull-left control-label">指数3：</label>
        <div class="col-lg-1">
          <select class="form-control" name='wid[]' id='zhis3' onchange="changezhishu('3')">
		    <option value=''>请选择</option>
		    <?php if(is_array($windex)): $i = 0; $__LIST__ = $windex;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$wvo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($wvo["id"]); ?>" <?php if($gtaglist[2]['wid'] == $wvo['id']): ?>selected='selected'<?php endif; ?>><?php echo ($wvo["wname"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
          </select>
        </div>
        <label class="pull-left control-label">气温：</label>
        <div class="col-lg-1">
          <select class="form-control" name='stp[]' id='stm3'>
            <option value="">请选择</option>
		  <?php if(is_array($temarr)): $i = 0; $__LIST__ = $temarr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$tvo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($tvo); ?>" <?php if($gtaglist[2]['stm'] == $tvo): ?>selected='selected'<?php endif; ?>><?php echo ($tvo); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
          </select>
        </div>
        <label class="pull-left control-label">&deg;C —</label>
        <div class="col-lg-1">
          <select class="form-control" name='etp[]' id='etm3'>
            <option value="">请选择</option>
			<?php if(is_array($temarr)): $i = 0; $__LIST__ = $temarr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$tvo2): $mod = ($i % 2 );++$i;?><option value="<?php echo ($tvo2); ?>" <?php if($gtaglist[2]['etm'] == $tvo2): ?>selected='selected'<?php endif; ?>><?php echo ($tvo2); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
          </select>
        </div>
        <label class="pull-left control-label">&deg;C</label>
		<label class="pull-left control-label" onclick="delzhishu('3')">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;删除</label>
      </div>
    </div>

    <div class="form-group addstm" id="delz4" <?php if($gtaglist[3] != ''): ?>style="display:block;"<?php else: ?>style="display:none;"<?php endif; ?>
      <label class="col-lg-1 control-label"></label>
      <div class="row col-lg-12">
        <label class="pull-left control-label">指数4：</label>
        <div class="col-lg-1">
          <select class="form-control" name='wid[]' id='zhis4' onchange="changezhishu('4')">
		    <option value=''>请选择</option>
		    <?php if(is_array($windex)): $i = 0; $__LIST__ = $windex;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$wvo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($wvo["id"]); ?>" <?php if($gtaglist[3]['wid'] == $wvo['id']): ?>selected='selected'<?php endif; ?>><?php echo ($wvo["wname"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
          </select>
        </div>
        <label class="pull-left control-label">气温：</label>
        <div class="col-lg-1">
          <select class="form-control" name='stp[]' id='stm4'>
            <option value="">请选择</option>
		  <?php if(is_array($temarr)): $i = 0; $__LIST__ = $temarr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$tvo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($tvo); ?>" <?php if($gtaglist[3]['stm'] == $tvo): ?>selected='selected'<?php endif; ?>><?php echo ($tvo); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
          </select>
        </div>
        <label class="pull-left control-label">&deg;C —</label>
        <div class="col-lg-1">
          <select class="form-control" name='etp[]' id='etm4'>
            <option value="">请选择</option>
			<?php if(is_array($temarr)): $i = 0; $__LIST__ = $temarr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$tvo2): $mod = ($i % 2 );++$i;?><option value="<?php echo ($tvo2); ?>" <?php if($gtaglist[3]['etm'] == $tvo2): ?>selected='selected'<?php endif; ?>><?php echo ($tvo2); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
          </select>
        </div>
        <label class="pull-left control-label">&deg;C</label>
		<label class="pull-left control-label" onclick="delzhishu('4')">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;删除</label>
      </div>
    </div>

    <div class="form-group addstm" id="delz5" <?php if($gtaglist[4] != ''): ?>style="display:block;"<?php else: ?>style="display:none;"<?php endif; ?>
      <label class="col-lg-1 control-label"></label>
      <div class="row col-lg-12">
        <label class="pull-left control-label">指数5：</label>
        <div class="col-lg-1">
          <select class="form-control" name='wid[]' id='zhis5' onchange="changezhishu('5')">
		    <option value=''>请选择</option>
		    <?php if(is_array($windex)): $i = 0; $__LIST__ = $windex;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$wvo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($wvo["id"]); ?>" <?php if($gtaglist[4]['wid'] == $wvo['id']): ?>selected='selected'<?php endif; ?>><?php echo ($wvo["wname"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
          </select>
        </div>
        <label class="pull-left control-label">气温：</label>
        <div class="col-lg-1">
          <select class="form-control" name='stp[]' id='stm5'>
            <option value="">请选择</option>
		  <?php if(is_array($temarr)): $i = 0; $__LIST__ = $temarr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$tvo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($tvo); ?>" <?php if($gtaglist[4]['stm'] == $tvo): ?>selected='selected'<?php endif; ?>><?php echo ($tvo); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
          </select>
        </div>
        <label class="pull-left control-label">&deg;C —</label>
        <div class="col-lg-1">
          <select class="form-control" name='etp[]' id='etm5'>
            <option value="">请选择</option>
			<?php if(is_array($temarr)): $i = 0; $__LIST__ = $temarr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$tvo2): $mod = ($i % 2 );++$i;?><option value="<?php echo ($tvo2); ?>" <?php if($gtaglist[4]['etm'] == $tvo2): ?>selected='selected'<?php endif; ?>><?php echo ($tvo2); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
          </select>
        </div>
        <label class="pull-left control-label">&deg;C</label>
		<label class="pull-left control-label" onclick="delzhishu('5')">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;删除</label>
      </div>
    </div>

    <div class="form-group addstm" id="delz6" <?php if($gtaglist[5] != ''): ?>style="display:block;"<?php else: ?>style="display:none;"<?php endif; ?>
      <label class="col-lg-1 control-label"></label>
      <div class="row col-lg-12">
        <label class="pull-left control-label">指数6：</label>
        <div class="col-lg-1">
          <select class="form-control" name='wid[]' id='zhis6' onchange="changezhishu('6')">
		    <option value=''>请选择</option>
		    <?php if(is_array($windex)): $i = 0; $__LIST__ = $windex;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$wvo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($wvo["id"]); ?>" <?php if($gtaglist[5]['wid'] == $wvo['id']): ?>selected='selected'<?php endif; ?>><?php echo ($wvo["wname"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
          </select>
        </div>
        <label class="pull-left control-label">气温：</label>
        <div class="col-lg-1">
          <select class="form-control" name='stp[]' id='stm6'>
            <option value="">请选择</option>
		  <?php if(is_array($temarr)): $i = 0; $__LIST__ = $temarr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$tvo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($tvo); ?>" <?php if($gtaglist[5]['stm'] == $tvo): ?>selected='selected'<?php endif; ?>><?php echo ($tvo); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
          </select>
        </div>
        <label class="pull-left control-label">&deg;C —</label>
        <div class="col-lg-1">
          <select class="form-control" name='etp[]' id='etm6'>
            <option value="">请选择</option>
			<?php if(is_array($temarr)): $i = 0; $__LIST__ = $temarr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$tvo2): $mod = ($i % 2 );++$i;?><option value="<?php echo ($tvo2); ?>" <?php if($gtaglist[5]['etm'] == $tvo2): ?>selected='selected'<?php endif; ?>><?php echo ($tvo2); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
          </select>
        </div>
        <label class="pull-left control-label">&deg;C</label>
		<label class="pull-left control-label" onclick="delzhishu('6')">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;删除</label>
      </div>
    </div>

    <div class="form-group addstm" id="delz7" <?php if($gtaglist[6] != ''): ?>style="display:block;"<?php else: ?>style="display:none;"<?php endif; ?>
      <label class="col-lg-1 control-label"></label>
      <div class="row col-lg-12">
        <label class="pull-left control-label">指数7：</label>
        <div class="col-lg-1">
          <select class="form-control" name='wid[]' id='zhis7' onchange="changezhishu('7')">
		    <option value=''>请选择</option>
		    <?php if(is_array($windex)): $i = 0; $__LIST__ = $windex;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$wvo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($wvo["id"]); ?>" <?php if($gtaglist[6]['wid'] == $wvo['id']): ?>selected='selected'<?php endif; ?>><?php echo ($wvo["wname"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
          </select>
        </div>
        <label class="pull-left control-label">气温：</label>
        <div class="col-lg-1">
          <select class="form-control" name='stp[]' id='stm7'>
            <option value="">请选择</option>
		  <?php if(is_array($temarr)): $i = 0; $__LIST__ = $temarr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$tvo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($tvo); ?>" <?php if($gtaglist[6]['stm'] == $tvo): ?>selected='selected'<?php endif; ?>><?php echo ($tvo); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
          </select>
        </div>
        <label class="pull-left control-label">&deg;C —</label>
        <div class="col-lg-1">
          <select class="form-control" name='etp[]' id='etm7'>
            <option value="">请选择</option>
			<?php if(is_array($temarr)): $i = 0; $__LIST__ = $temarr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$tvo2): $mod = ($i % 2 );++$i;?><option value="<?php echo ($tvo2); ?>" <?php if($gtaglist[6]['etm'] == $tvo2): ?>selected='selected'<?php endif; ?>><?php echo ($tvo2); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
          </select>
        </div>
        <label class="pull-left control-label">&deg;C</label>
		<label class="pull-left control-label" onclick="delzhishu('7')">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;删除</label>
      </div>
    </div>
	<!--新添加的指数-->
<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$lvo): $mod = ($i % 2 );++$i;?><div class="form-group">
      <label class="col-lg-1 control-label"><?php echo ($lvo["name"]); ?>：</label>
      <div class="col-lg-10">
        <!--<label class="checkbox-inline">
          <input type="checkbox" class="allselect" value="option1"> 全选
        </label>-->
		<?php if(is_array($lvo['ctag'])): $i = 0; $__LIST__ = $lvo['ctag'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$tvo): $mod = ($i % 2 );++$i; if($gtaglist[0]['gtype'] >= '0'): ?><div class="clearfix tsex<?php echo ($tvo["type"]); ?>" <?php if($gtaglist[0]['gtype'] != $tvo['type']): ?>style="display:none;"<?php endif; ?>>
		<?php if($tvo['type'] == 1): ?><label class="checkbox-inline nopdl tags-1">WOMEN</label><?php elseif($tvo['type'] == 2): ?><label class="checkbox-inline nopdl tags-2">MEN&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label><?php elseif($tvo['type'] == 3): ?><label class="checkbox-inline nopdl tags-3">KIDS&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label><?php endif; ?>
		<?php if(is_array($tvo['type2'])): $i = 0; $__LIST__ = $tvo['type2'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$cvo): $mod = ($i % 2 );++$i;?><label class="checkbox-inline">
          <input type="checkbox" value="<?php echo ($cvo["id"]); ?>" class='delch' name="tag<?php echo ($lvo["id"]); ?>[]" <?php if($cvo['sel'] == 1): ?>checked='checked'<?php endif; ?>> <?php echo ($cvo["name"]); ?>
        </label><?php endforeach; endif; else: echo "" ;endif; ?>
		 </div>
		 <?php else: ?>
		<div class="clearfix tsex<?php echo ($tvo["type"]); ?>">
		<?php if($tvo['type'] == 1): ?><label class="checkbox-inline nopdl tags-1">WOMEN</label><?php elseif($tvo['type'] == 2): ?><label class="checkbox-inline nopdl tags-2">MEN&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label><?php elseif($tvo['type'] == 3): ?><label class="checkbox-inline nopdl tags-3">KIDS&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label><?php endif; ?>
		<?php if(is_array($tvo['type2'])): $i = 0; $__LIST__ = $tvo['type2'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$cvo): $mod = ($i % 2 );++$i;?><label class="checkbox-inline">
          <input type="checkbox" value="<?php echo ($cvo["id"]); ?>" class='delch' name="tag<?php echo ($lvo["id"]); ?>[]" <?php if($cvo['sel'] == 1): ?>checked='checked'<?php endif; ?>> <?php echo ($cvo["name"]); ?>
        </label><?php endforeach; endif; else: echo "" ;endif; ?>
		 </div><?php endif; endforeach; endif; else: echo "" ;endif; ?>
      </div>
    </div><?php endforeach; endif; else: echo "" ;endif; ?>
      <div class="form-group">
      <label class="col-lg-1 control-label">选择自定义分类：</label>
      <div class="col-lg-10" id='zidy'>
      	<?php echo ($str); ?>
	  <?php if($ccatelist != ''): if(is_array($ccatelist)): $i = 0; $__LIST__ = $ccatelist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$cvo): $mod = ($i % 2 );++$i;?><input type="radio" name="ccate" value="<?php echo ($cvo["id"]); ?>" <?php if($cvo['id'] == $gtaglist[0]['ccateid']): ?>checked='checked'<?php endif; ?>><?php echo ($cvo["name"]); ?>&nbsp;&nbsp;<?php endforeach; endif; else: echo "" ;endif; endif; ?>
      </div>
    </div>
      <div class="form-group">
      <label class="col-lg-1 control-label">复制ID：</label>
      <div class="col-lg-10">
        <label class="radio-inline">
          <input type="text" name="originalid" value='' >
        </label>
      </div>
    </div>
    <input type="hidden" name='p' value='<?php echo ($p); ?>'>
    <input type="hidden" name='keyword' value='<?php echo ($keyword); ?>'>
    <input type="hidden" name='ist' value='<?php echo ($istag); ?>'>
    <input type="hidden" name='cate1' value='<?php echo ($cate1); ?>'>
    <input type="hidden" name='cate2' value='<?php echo ($cate2); ?>'>
    <input type="hidden" name='isdoubt' value='<?php echo ($isdoubt); ?>'>
<div class="text-center">
        <button type="submit" class="btn btn-success">保存</button>
	  <label class="checkbox-inline">
        <input type="checkbox" name='cy' value='2' <?php if($result['isdoubt'] == '2'): ?>checked="checked"<?php endif; ?>/>存疑
      </label>
	  <label class="checkbox-inline">
               <a href="<?php echo U('Products/index',array('p'=>$p,'keyword'=>$keyword,'ist'=>$istag,'cate1'=>$cate1,'cate2'=>$cate2,'isdoubt'=>$isdoubt));?>">返回</a>
      </label>
    </div>
  </form>
  <hr />
<form class="form-horizontal" role="form">
    <div class="form-group">
      <label class="col-lg-1 control-label">后台ID</label>
      <div class="col-lg-10">
        <span class="help-block"><?php echo ($result["id"]); ?></span>
        <img src="__ROOT__/<?php echo ($result["pic_url"]); ?>" alt="">
      </div>
    </div>
    <div class="form-group">
      <label class="col-lg-1 control-label">商品名称</label>
      <div class="col-lg-10">
        <span class="help-block"><?php echo ($result["title"]); ?></span>
        <img src="__ROOT__/<?php echo ($result["pic_url"]); ?>" alt="">
      </div>
    </div>
    <div class="form-group">
      <label class="col-lg-1 control-label">商品数字ID</label>
      <div class="col-lg-10">
        <span class="help-block"><?php echo ($result["num_iid"]); ?></span>
      </div>
    </div>
    <div class="form-group">
      <label class="col-lg-1 control-label">分类</label>
      <div class="col-lg-10">
        <span class="help-block"><?php echo ($result["cname"]); ?></span>
      </div>
    </div>
    <div class="form-group">
      <label class="col-lg-1 control-label">价格</label>
      <div class="col-lg-10">
        <span class="help-block">￥<?php echo ($result["price"]); ?></span>
      </div>
    </div>
    <div class="form-group">
      <label class="col-lg-1 control-label">淘宝ID</label>
      <div class="col-lg-10">
        <span class="help-block"><?php echo ($result["num_iid"]); ?></span>
      </div>
    </div>
    <div class="form-group">
      <label class="col-lg-1 control-label">商品编码</label>
      <div class="col-lg-10">
        <span class="help-block"><?php echo ($result["outer_id"]); ?></span>
      </div>
    </div>
    <div class="form-group">
      <label class="col-lg-1 control-label">商品属性</label>
      <div class="col-lg-10">
        <span class="help-block"><?php echo ($result["props_name"]); ?></span>
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
<script type='text/javascript'>
var ur = '<?php echo U('Productsedit/ajaxt');?>';
var ur2 = '<?php echo U('Productsedit/ajaxcate');?>';
var ur3 = '<?php echo U('Productsedit/ajaxt2');?>';
$(function(){	
 $('#isudid').change(function(){
	 var gtypevalue = $('#gtypeid option:selected').val();
	 if(gtypevalue<=0){
       alert('请选择性别');
	  }else{
      var isuevalue = $('#isudid option:selected').val();
	  if(isuevalue){
       $.post(ur2,{gtype:gtypevalue,isud :isuevalue},function(data,status){
		   var da = eval("("+data+")");
		   if(da.flag){
              $('#zidy').html(da.str);
			}else{
             alert(da.msg);
             $('#zidy').html('');
				}
		   
		   });
	  }
	  }
	 });

$('#gtypeid').change(function(){
	$('.delch').removeAttr("checked");
	var gtypevalue = $('#gtypeid option:selected').val();
	if(gtypevalue>0){
		$('.clearfix').css('display','none');
        $('.tsex'+gtypevalue).css('display','block');
	}else{
        $('.clearfix').css('display','block');
		}
	});
//点击添加显示指数
$('#clickaddstm').click(function(){
    $(".addstm:hidden").each(function(i,val){
		if(i==0){
         $(this).css('display','block');
		}
		});
});
})
//删除指数标签
function delzhishu(id){
$('#zhis'+id).find(':selected').prop('selected',false);
$('#stm'+id).find(':selected').prop('selected',false);
$('#etm'+id).find(':selected').prop('selected',false);
$('#delz'+id).css('display','none');
}

function changezhishu(id){
var selvalue = $('#zhis'+id+' option:selected').val();
$.post(ur,{
	selid : selvalue
	},function(data,status){
	var da = eval("("+data+")");
        $('#stm'+id).html(da.stm1);
		$('#etm'+id).html(da.stm2);
		});
}
</script>