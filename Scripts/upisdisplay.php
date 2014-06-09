<?php
/**
 * Created by JetBrains PhpStorm.
 * User: yu
 * Date: 14-6-9
 * Time: 下午1:02
 * To change this template use File | Settings | File Templates.
 */
require_once('init.php');
require_once('TopSdk.php');
set_time_limit(0);
$db = new DB();
$db->mysqlquery("call upgoods()");
exit;