--TEST--
Services_Photozou::photo_album test
--FILE--
<?php
require_once 'config.php';
require_once 'Services/Photozou.php';

$photozou = new Services_Photozou($user, $password);

var_dump(is_array($photozou->photo_album()));
?>
--EXPECT--
bool(true)
