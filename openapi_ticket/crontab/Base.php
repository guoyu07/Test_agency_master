<?php
/**
 * CRONTAB基类
 * @author  mosen
 */

error_reporting(E_ALL ^ (E_NOTICE | E_DEPRECATED));
set_time_limit(0);
ini_set('memory_limit', '256M');
define('BACKEND', 1);
define('APPLICATION_PATH', realpath(dirname(__FILE__).'/../'));

require (APPLICATION_PATH . '/../setting/openapi_ticket.php');
$app = new Yaf_Application( APPLICATION_PATH . "/conf/base.ini");
