<?php
return array(
	//'配置项'=>'配置值'
    'DB_TYPE'=>'mysql',
    'DB_HOST'=>'localhost',
    'DB_NAME'=>'uniqlo',
    'DB_USER'=>'root',
    'DB_PWD'=>'root',
    'DB_PORT'=>'3306',
    /*URL配置*/
    'DB_PREFIX'=>'u_', 
    'URL_MODEL'=>1,
    'TOKEN_ON'=>true,  // 是否开启令牌验证
    'TOKEN_NAME'=>'__hash__',    // 令牌验证的表单隐藏字段名称
    'TOKEN_TYPE'=>'md5',  //令牌哈希验证规则 默认为MD5'TOKEN_RESET'=>true, 
);
?>
