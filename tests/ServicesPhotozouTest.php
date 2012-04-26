<?php
require_once 'Services/Photozou.php';

class ServicesPhotozouTest extends PHPUnit_Framework_TestCase
{
    public function testPhotoAlbum()
    {
        global $user;
        global $password;
        $photozou = new Services_Photozou($user, $password);
        $this->assertTrue(is_array($photozou->photo_album()));
    }
}

