--TEST--
Services_Photozou::photo_list_public test
--FILE--
<?php
require_once 'config.php';
require_once 'Services/Photozou.php';

$photozou = new Services_Photozou($user, $password);

$result = $photozou->photo_list_public(array(
    'type' => 'public',
    'user_id' => '44520',
    'limit' => '2'
    )
);
var_dump(is_array($result));
?>
--EXPECT--
bool(true)
