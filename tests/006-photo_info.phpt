--TEST--
Services_Photozou::photo_info test
--FILE--
<?php
require_once 'config.php';
require_once 'Services/Photozou.php';

$photozou = new Services_Photozou($user, $password);

$result = $photozou->photo_list_public(array(
    'type' => 'cc',
    'license' => 'by',
    //'user_id' => '44520',
    'limit' => '1'
    )
);

if (PEAR::isError($result)) {
  echo $result->getMessage();
  exit;
}

$photo = current($result);

$photo2 = $photozou->photo_info($photo['photo_id']);

unset($photo['view_num']);
foreach ($photo as $key => $value) {
  if ($value !== $photo2[$key]) {
    echo "not equal : {$key}" . PHP_EOL;
  }
}

var_dump(true);
?>
--EXPECT--
bool(true)
