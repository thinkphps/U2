<?php
/**
 * Created by JetBrains PhpStorm.
 * User: yu
 * Date: 14-7-28
 * Time: 下午7:11
 * 每周星期三凌晨1点40更新beubeu_goods
 */
require_once('init.php');
set_time_limit(0);
$db = new DB();
$db->mysqlquery("call update_beubeu_goods()");
exit;