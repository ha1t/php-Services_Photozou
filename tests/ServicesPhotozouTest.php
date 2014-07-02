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

    public function testGetMime()
    {
        $ref = new ReflectionMethod('Services_Photozou', 'getMime');
        $ref->setAccessible(true);
        $photozou = new Services_Photozou($this->user, $this->password);

        $this->assertEquals('image/gif', $ref->invoke($photozou, 'hoge'));
        $this->assertEquals('image/gif', $ref->invoke($photozou, 'hoge.gif'));
        $this->assertEquals('image/jpeg', $ref->invoke($photozou, 'hoge.jpg'));
        $this->assertEquals('image/jpeg', $ref->invoke($photozou, 'hoge.jpeg'));
        $this->assertEquals('image/pjpeg', $ref->invoke($photozou, 'hoge.pjpeg'));
        $this->assertEquals('image/png', $ref->invoke($photozou, 'hoge.png'));
        $this->assertEquals('image/x-png', $ref->invoke($photozou, 'hoge.x-png'));
    }

    public function testNop()
    {
        $photozou = new Services_Photozou($this->user, $this->password);
        $this->assertTrue($photozou->nop());

        //$photozou = new Services_Photozou('invalid_user', 'invalid_pass');
        //$this->assertFalse($photozou->nop());
    }

    public function testPhotoAdd()
    {
        $this->markTestIncomplete();
    }

    public function testPhotoAddAlbum()
    {
        $this->markTestIncomplete();
    }

    public function testPhotoAddTag()
    {
        $this->markTestIncomplete();
    }

    public function testPhotoAlbum()
    {
        $photozou = new Services_Photozou($this->user, $this->password);
        $this->assertTrue(is_array($photozou->photo_album()));
    }

    public function testPhotoComment()
    {
        $this->markTestIncomplete();
    }

    public function testPhotoEditAlbum()
    {
        $this->markTestIncomplete();
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

    public function testUserInfo()
    {
        $photozou = new Services_Photozou($this->user, $this->password);

        $result = $photozou->user_info(array('user_id' => '2'));
        $this->assertEquals($result['profile_url'], 'http://photozou.jp/user/top/2');

        // ユーザーidを直接指定する事もできる
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

        $this->assertTrue(is_array($result));

        $photo = current($result);
        $photo2 = $photozou->photo_info($photo['photo_id']);

        unset($photo['view_num']);
        foreach ($photo as $key => $value) {
            $this->assertEquals($value, $photo2[$key]);
        }
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testPhotoInfoFail()
    {
        $fail_id = 'fail_id';
        $photozou = new Services_Photozou($this->user, $this->password);
        $photozou->photo_info($fail_id);
    }

    public function testUserGroup()
    {
        $photozou = new Services_Photozou($this->user, $this->password);
        $user_groups = $photozou->user_group();

        foreach ($user_groups as $user_group) {
            $this->assertTrue(is_array($user_group));
            $this->assertTrue(isset($user_group['group_id']));
        }
    }

    public function testSearchPublic()
    {
        $photozou = new Services_Photozou($this->user, $this->password);
        $params = array(
            'keyword' => 'ご飯',
            'limit' => 5,
        );
        $result = $photozou->search_public($params);
        $this->assertEquals(5, count($result));
    }

}
