--TEST--
Services_Photozou::photo_album test
--FILE--
<?php
require_once 'config.php';
require_once 'Services/Photozou.php';

$photozou = new Services_Photozou($user, $password);

$test_words = array(
    'test.jpg',
    'test.jpg.jpg',
    'a/b/c.jpg',
    '__r__.jpg',
);

$result = array();
foreach ($test_words as $word) {
    $ext = $photozou->getFileExt($word);
    if ($ext != 'jpg') {
        exit('ERROR:' . $word);
    }
}
var_dump(true)
?>
--EXPECT--
bool(true)
