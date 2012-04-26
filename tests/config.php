<?php
/**
 * Services_Photozou config file
 *
 */

ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . '../');

$user = "your username(mailaddress)";
$password = "your password";

if (file_exists(dirname(__FILE__) . '/config_my.php')) {
    require 'config_my.php';
}

