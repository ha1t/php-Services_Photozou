<?php

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
