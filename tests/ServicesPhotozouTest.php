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
}

