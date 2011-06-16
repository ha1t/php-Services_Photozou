--TEST--
Services_Photozou::user_info test
--FILE--
<?php
require_once 'config.php';
require_once 'Services/Photozou.php';

$photozou = new Services_Photozou($user, $password);

$result = $photozou->user_info(2);

if (isset($result['profile_url']) && $result['profile_url'] === 'http://photozou.jp/user/top/2') {
var_dump(true);
}
?>
--EXPECT--
bool(true)
