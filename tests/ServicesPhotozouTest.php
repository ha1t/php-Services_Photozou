<?php
require_once 'Services/Photozou.php';

class ServicesPhotozouTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        global $user;
        global $password;
        $this->user = $user;
        $this->password = $password;
    }

    public function testNop()
    {
        $photozou = new Services_Photozou($this->user, $this->password);
        $this->assertTrue($photozou->nop());
    }

    public function testPhotoAlbum()
    {
        $photozou = new Services_Photozou($this->user, $this->password);
        $this->assertTrue(is_array($photozou->photo_album()));
    }

    public function testPhotoListPublic()
    {
        $photozou = new Services_Photozou($this->user, $this->password);

        $result = $photozou->photo_list_public(
            array(
                'type' => 'public',
                'user_id' => '44520',
                'limit' => '2'
            )
        );
        $this->assertTrue(is_array($result));
    }

    public function testGetFileExt()
    {
        $ref = new ReflectionMethod('Services_Photozou', 'getFileExt');
        $ref->setAccessible(true);
        $photozou = new Services_Photozou($this->user, $this->password);
        $test_words = array(
            'test.jpg',
            'test.jpg.jpg',
            'a/b/c.jpg',
            '__r__.jpg',
        );

        foreach ($test_words as $word) {
            $this->assertEquals('jpg', $ref->invoke($photozou, $word));
        }
    }

    public function testUserInfo()
    {
        $photozou = new Services_Photozou($this->user, $this->password);

        $result = $photozou->user_info(array('user_id' => '2'));
        $this->assertEquals($result['profile_url'], 'http://photozou.jp/user/top/2');

        $result = $photozou->user_info(2);
        $this->assertEquals($result['profile_url'], 'http://photozou.jp/user/top/2');
    }

    public function testPhotoInfo()
    {
        $photozou = new Services_Photozou($this->user, $this->password);
        $result = $photozou->photo_list_public(
            array(
                'type' => 'cc',
                'license' => 'by',
                //'user_id' => '44520',
                'limit' => '1'
            )
        );

        $this->assertFalse(PEAR::isError($result));

        $photo = current($result);
        $photo2 = $photozou->photo_info($photo['photo_id']);

        unset($photo['view_num']);
        foreach ($photo as $key => $value) {
            $this->assertEquals($value, $photo2[$key]);
        }
    }

}
