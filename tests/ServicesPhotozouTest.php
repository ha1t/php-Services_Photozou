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

    // ダミーリクエストを差し込む
    public function injectMock(Services_Photozou $photozou, $xml)
    {
        if (!(isset($_SERVER['CI']) && $_SERVER['CI'] == true)) {
            return;
        }

        if (!is_array($xml)) {
            $xml = [$xml];

        }

        $response = [];
        foreach ($xml as $item) {
            $response[] = new \GuzzleHttp\Message\Response(
                200,
                [],
                \GuzzleHttp\Stream\Stream::factory($item)
            );

        }

        $mock = new \GuzzleHttp\Subscriber\Mock($response);

        $class = new ReflectionClass('Services_Photozou');
        $property = $class->getProperty('client');
        $property->setAccessible(true);

        $client = $property->getValue($photozou);
        $client->getEmitter()->attach($mock);
    }

    public function testNop()
    {

        $response = <<<EOD
<?xml version="1.0" encoding="UTF-8" ?>
<rsp stat="ok">
<info>
<user_id>1111</user_id>
</info>
</rsp>
EOD;
        $photozou = new Services_Photozou($this->user, $this->password);
        $this->injectMock($photozou, $response);


        $this->assertTrue($photozou->nop());

        //$photozou = new Services_Photozou('invalid_user', 'invalid_pass');
        //$this->assertFalse($photozou->nop());
    }

    /*
    public function testPhotoAdd()
    {
        $this->markTestIncomplete();
    }
     */

    /*
    public function testPhotoAddAlbum()
    {
        $this->markTestIncomplete();
    }
     */

    /*
    public function testPhotoAddTag()
    {
        $this->markTestIncomplete();
    }
     */

    public function testPhotoAlbum()
    {
        $xml = <<<EOD
<?xml version="1.0" encoding="UTF-8" ?>
<rsp stat="ok">
<info>
  <album>
    <album_id>919191</album_id>
    <user_id>939393</user_id>
    <name><![CDATA[TEST1]]></name>
    <description>TEST1!!!!!!!!!</description>
    <perm_type>deny</perm_type>
    <perm_type2>all</perm_type2>
    <order_type>upload</order_type>
    <copyright_type>normal</copyright_type>
    <copyright_commercial>yes</copyright_commercial>
    <copyright_modifications>yes</copyright_modifications>
    <photo_num>769</photo_num>
    <perm_msg>誰にも公開しない</perm_msg>
    <cover_photo_id>12345</cover_photo_id>
    <cover_image_url>http://api.photozou.jp/rest/bin_album_cover?album_id=919191</cover_image_url>
    <cover_original_image_url>http://api.photozou.jp/rest/bin_album_cover?album_id=919191&amp;mode=org</cover_original_image_url>
    <cover_thumbnail_image_url>http://api.photozou.jp/rest/bin_album_cover?album_id=919191&amp;mode=thumbnail</cover_thumbnail_image_url>
    <upload_email>u939393-09813@photozou.jp</upload_email>
    <created_time>2007-01-08T17:25:59+09:00</created_time>
    <updated_time>2007-01-19T11:15:11+09:00</updated_time>
  </album>
  <album>
    <album_id>929292</album_id>
    <user_id>939393</user_id>
    <name><![CDATA[TEST2]]></name>
    <description></description>
    <perm_type>deny</perm_type>
    <perm_type2>all</perm_type2>
    <order_type>upload</order_type>
    <copyright_type>normal</copyright_type>
    <copyright_commercial>yes</copyright_commercial>
    <copyright_modifications>yes</copyright_modifications>
    <photo_num>275</photo_num>
    <perm_msg>誰にも公開しない</perm_msg>
    <cover_photo_id>12345</cover_photo_id>
    <cover_image_url>http://api.photozou.jp/rest/bin_album_cover?album_id=929292</cover_image_url>
    <cover_original_image_url>http://api.photozou.jp/rest/bin_album_cover?album_id=929292&amp;mode=org</cover_original_image_url>
    <cover_thumbnail_image_url>http://api.photozou.jp/rest/bin_album_cover?album_id=929292&amp;mode=thumbnail</cover_thumbnail_image_url>
    <upload_email>u939393-2c419@photozou.jp</upload_email>
    <created_time>2007-09-28T00:01:35+09:00</created_time>
    <updated_time>2008-02-07T11:32:34+09:00</updated_time>
  </album>
</info>
</rsp>
EOD;
        $photozou = new Services_Photozou($this->user, $this->password);
        $this->injectMock($photozou, $xml);
        $albums = $photozou->photo_album();
        $this->assertTrue(is_array($albums));

        foreach ($albums as $album) {
            $this->assertTrue(isset($album['user_id']));
        }
    }

    /*
    public function testPhotoComment()
    {
        $this->markTestIncomplete();
    }
     */

    /*
    public function testPhotoEditAlbum()
    {
        $this->markTestIncomplete();
    }
     */

    public function testPhotoListPublic()
    {
        $xml = <<<EOD
<rsp stat="ok">
<info>
<photo_num>2</photo_num><photo><photo_id>206591045</photo_id>
<user_id>2930585</user_id>
<album_id>7465637</album_id>
<photo_title>我が心の新宿</photo_title>
<favorite_num>0</favorite_num>
<comment_num>0</comment_num>
<view_num>9</view_num>
<copyright>normal</copyright>
<original_height>1365</original_height>
<original_width>2048</original_width>
<date>2014-06-29</date>
<regist_time>2014-07-03T19:26:14+09:00</regist_time>
<url>http://photozou.jp/photo/show/2930585/206591045</url>
<image_url>http://art6.photozou.jp/pub/585/2930585/photo/206591045.jpg</image_url>
<original_image_url>http://art6.photozou.jp/pub/585/2930585/photo/206591045_org.jpg</original_image_url>
<thumbnail_image_url>http://art6.photozou.jp/pub/585/2930585/photo/206591045_thumbnail.jpg</thumbnail_image_url>
<tags></tags><large_tag><![CDATA[<a href="http://photozou.jp/photo/show/2930585/206591045"><img src="http://art6.photozou.jp/pub/585/2930585/photo/206591045.v1404387182.jpg" alt="我が心の新宿" width="450" height="300"></a><br><a href="http://photozou.jp/photo/show/2930585/206591045">我が心の新宿</a> posted by <a href="http://photozou.jp/user/top/2930585">(C)tuti</a>]]></large_tag>
<medium_tag><![CDATA[<a href="http://photozou.jp/photo/show/2930585/206591045"><img src="http://art6.photozou.jp/pub/585/2930585/photo/206591045.v1404387182.jpg" alt="我が心の新宿" width="240" height="160"></a><br><a href="http://photozou.jp/photo/show/2930585/206591045">我が心の新宿</a> posted by <a href="http://photozou.jp/user/top/2930585">(C)tuti</a>]]></medium_tag>
</photo><photo><photo_id>206541556</photo_id>
<user_id>2930585</user_id>
<album_id>7465637</album_id>
<photo_title>御苑の職員さん</photo_title>
<favorite_num>9</favorite_num>
<comment_num>3</comment_num>
<view_num>29</view_num>
<copyright>normal</copyright>
<original_height>1365</original_height>
<original_width>2048</original_width>
<date>2014-06-29</date>
<regist_time>2014-07-02T19:26:48+09:00</regist_time>
<url>http://photozou.jp/photo/show/2930585/206541556</url>
<image_url>http://art3.photozou.jp/pub/585/2930585/photo/206541556.jpg</image_url>
<original_image_url>http://art3.photozou.jp/pub/585/2930585/photo/206541556_org.jpg</original_image_url>
<thumbnail_image_url>http://art3.photozou.jp/pub/585/2930585/photo/206541556_thumbnail.jpg</thumbnail_image_url>
<tags></tags><large_tag><![CDATA[<a href="http://photozou.jp/photo/show/2930585/206541556"><img src="http://art3.photozou.jp/pub/585/2930585/photo/206541556.v1404387317.jpg" alt="御苑の職員さん" width="450" height="300"></a><br><a href="http://photozou.jp/photo/show/2930585/206541556">御苑の職員さん</a> posted by <a href="http://photozou.jp/user/top/2930585">(C)tuti</a>]]></large_tag>
<medium_tag><![CDATA[<a href="http://photozou.jp/photo/show/2930585/206541556"><img src="http://art3.photozou.jp/pub/585/2930585/photo/206541556.v1404387317.jpg" alt="御苑の職員さん" width="240" height="160"></a><br><a href="http://photozou.jp/photo/show/2930585/206541556">御苑の職員さん</a> posted by <a href="http://photozou.jp/user/top/2930585">(C)tuti</a>]]></medium_tag>
</photo>
</info>
</rsp>"
EOD;
        $photozou = new Services_Photozou($this->user, $this->password);
        $this->injectMock($photozou, $xml);

        $result = $photozou->photo_list_public(
            [
                'type' => 'public',
                'user_id' => '2930585',
                'limit' => '2'
            ]
        );

        $this->assertTrue(is_array($result));
    }

    public function testUserInfo()
    {
        $xml_list = [];
        $xml_list[] = <<<EOD
<?xml version="1.0" encoding="UTF-8" ?>
<rsp stat="ok">
<info>
<user>
<user_id>2</user_id>
<profile_url>http://photozou.jp/user/top/2</profile_url>
<nick_name>suadd</nick_name>
<my_pic>http://art6.photozou.jp/pub/2/2/my_pic/main.v44807</my_pic>
<photo_num>7787</photo_num>
<friends_num>97</friends_num>
</user>
</info>
</rsp>
EOD;
        $xml_list[] = <<<EOD
<?xml version="1.0" encoding="UTF-8" ?>
<rsp stat="ok">
<info>
<user>
<user_id>2</user_id>
<profile_url>http://photozou.jp/user/top/2</profile_url>
<nick_name>suadd</nick_name>
<my_pic>http://art6.photozou.jp/pub/2/2/my_pic/main.v44807</my_pic>
<photo_num>7787</photo_num>
<friends_num>97</friends_num>
</user>
</info>
</rsp>
EOD;

        $photozou = new Services_Photozou($this->user, $this->password);
        $this->injectMock($photozou, $xml_list);

        $result = $photozou->user_info(array('user_id' => '2'));
        $this->assertEquals($result['profile_url'], 'http://photozou.jp/user/top/2');

        // ユーザーidを直接指定する事もできる
        $result = $photozou->user_info(2);
        $this->assertEquals($result['profile_url'], 'http://photozou.jp/user/top/2');
    }

    public function testPhotoInfo()
    {
        $xml_list = [];
        $xml_list[] = <<<EOD
<?xml version="1.0" encoding="UTF-8" ?>
<rsp stat="ok">
<info>
<photo><photo_id>206588772</photo_id>
<user_id>141383</user_id>
<album_id>8199002</album_id>
<photo_title>ちょうだーーい</photo_title>
<description>７月３日</description>
<favorite_num>0</favorite_num>
<comment_num>0</comment_num>
<view_num>6</view_num>
<copyright>creativecommons</copyright>
<original_height>1125</original_height>
<original_width>1500</original_width>
<copyright_commercial>yes</copyright_commercial>
<copyright_modifications>yes</copyright_modifications>
<date>2014-07-03</date>
<regist_time>2014-07-03T18:26:37+09:00</regist_time>
<url>http://photozou.jp/photo/show/141383/206588772</url>
<image_url>http://art37.photozou.jp/pub/383/141383/photo/206588772.jpg</image_url>
<original_image_url>http://art37.photozou.jp/pub/383/141383/photo/206588772_org.jpg</original_image_url>
<thumbnail_image_url>http://art37.photozou.jp/pub/383/141383/photo/206588772_thumbnail.jpg</thumbnail_image_url>
<tags><tag>ツバメ</tag>
<tag>鳥</tag>
</tags><large_tag><![CDATA[<a href="http://photozou.jp/photo/show/141383/206588772"><img src="http://art37.photozou.jp/pub/383/141383/photo/206588772.v1404384589.jpg" alt="ちょうだーーい" width="450" height="338"></a><br><a href="http://photozou.jp/photo/show/141383/206588772">ちょうだーーい</a> posted by <a href="http://photozou.jp/user/top/141383">(C)緋佳</a>]]></large_tag>
<medium_tag><![CDATA[<a href="http://photozou.jp/photo/show/141383/206588772"><img src="http://art37.photozou.jp/pub/383/141383/photo/206588772.v1404384589.jpg" alt="ちょうだーーい" width="240" height="180"></a><br><a href="http://photozou.jp/photo/show/141383/206588772">ちょうだーーい</a> posted by <a href="http://photozou.jp/user/top/141383">(C)緋佳</a>]]></medium_tag>
</photo>
</info>
</rsp>
EOD;
        $xml_list[] = <<<EOD
<?xml version="1.0" encoding="UTF-8" ?>
<rsp stat="ok">
<info>
<photo><photo_id>206588772</photo_id>
<user_id>141383</user_id>
<album_id>8199002</album_id>
<photo_title>ちょうだーーい</photo_title>
<description>７月３日</description>
<favorite_num>0</favorite_num>
<comment_num>0</comment_num>
<view_num>6</view_num>
<copyright>creativecommons</copyright>
<original_height>1125</original_height>
<original_width>1500</original_width>
<copyright_commercial>yes</copyright_commercial>
<copyright_modifications>yes</copyright_modifications>
<date>2014-07-03</date>
<regist_time>2014-07-03T18:26:37+09:00</regist_time>
<url>http://photozou.jp/photo/show/141383/206588772</url>
<image_url>http://art37.photozou.jp/pub/383/141383/photo/206588772.jpg</image_url>
<original_image_url>http://art37.photozou.jp/pub/383/141383/photo/206588772_org.jpg</original_image_url>
<thumbnail_image_url>http://art37.photozou.jp/pub/383/141383/photo/206588772_thumbnail.jpg</thumbnail_image_url>
<tags><tag>ツバメ</tag>
<tag>鳥</tag>
</tags><large_tag><![CDATA[<a href="http://photozou.jp/photo/show/141383/206588772"><img src="http://art37.photozou.jp/pub/383/141383/photo/206588772.v1404384589.jpg" alt="ちょうだーーい" width="450" height="338"></a><br><a href="http://photozou.jp/photo/show/141383/206588772">ちょうだーーい</a> posted by <a href="http://photozou.jp/user/top/141383">(C)緋佳</a>]]></large_tag>
<medium_tag><![CDATA[<a href="http://photozou.jp/photo/show/141383/206588772"><img src="http://art37.photozou.jp/pub/383/141383/photo/206588772.v1404384589.jpg" alt="ちょうだーーい" width="240" height="180"></a><br><a href="http://photozou.jp/photo/show/141383/206588772">ちょうだーーい</a> posted by <a href="http://photozou.jp/user/top/141383">(C)緋佳</a>]]></medium_tag>
</photo>
</info>
</rsp>
EOD;
        $photozou = new Services_Photozou($this->user, $this->password);
        $this->injectMock($photozou, $xml_list);

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
        $xml = <<<EOD
<rsp stat="ok">
<info>
  <user_group>
    <group_id>5</group_id>
    <name>users</name>
    <user_num>3</user_num>
  </user_group>
  <user_group>
    <group_id>0</group_id>
    <name>未指定</name>
    <user_num>4</user_num>
  </user_group>
</info>
</rsp>
EOD;
        $photozou = new Services_Photozou($this->user, $this->password);
        $this->injectMock($photozou, $xml);

        $user_groups = $photozou->user_group();

        foreach ($user_groups as $user_group) {
            $this->assertTrue(is_array($user_group));
            $this->assertTrue(isset($user_group['group_id']));
        }
    }

    public function testSearchPublic()
    {
        $xml = <<<EOD
<rsp stat="ok">
<info>
<photo_num>5</photo_num><photo><photo_id>206593811</photo_id>
<user_id>367766</user_id>
<album_id>2912852</album_id>
<photo_title>今晩は、茄子と万願寺唐辛子...</photo_title>
<favorite_num>0</favorite_num>
<comment_num>0</comment_num>
<view_num>0</view_num>
<copyright>creativecommons</copyright>
<original_height>960</original_height>
<original_width>720</original_width>
<copyright_commercial>yes</copyright_commercial>
<copyright_modifications>no</copyright_modifications>
<date>2014-07-03</date>
<regist_time>2014-07-03T20:32:35+09:00</regist_time>
<url>http://photozou.jp/photo/show/367766/206593811</url>
<image_url>http://art1.photozou.jp/pub/766/367766/photo/206593811.jpg</image_url>
<original_image_url>http://art1.photozou.jp/pub/766/367766/photo/206593811_org.jpg</original_image_url>
<thumbnail_image_url>http://art1.photozou.jp/pub/766/367766/photo/206593811_thumbnail.jpg</thumbnail_image_url>
<large_tag><![CDATA[<a href="http://photozou.jp/photo/show/367766/206593811"><img src="http://art1.photozou.jp/pub/766/367766/photo/206593811.v1404387156.jpg" alt="今晩は、茄子と万願寺唐辛子..." width="338" height="450"></a><br><a href="http://photozou.jp/photo/show/367766/206593811">今晩は、茄子と万願寺唐辛子...</a> posted by <a href="http://photozou.jp/user/top/367766">(C)akazawa3</a>]]></large_tag>
<medium_tag><![CDATA[<a href="http://photozou.jp/photo/show/367766/206593811"><img src="http://art1.photozou.jp/pub/766/367766/photo/206593811.v1404387156.jpg" alt="今晩は、茄子と万願寺唐辛子..." width="180" height="240"></a><br><a href="http://photozou.jp/photo/show/367766/206593811">今晩は、茄子と万願寺唐辛子...</a> posted by <a href="http://photozou.jp/user/top/367766">(C)akazawa3</a>]]></medium_tag>
</photo>
<photo><photo_id>206593504</photo_id>
<user_id>1058696</user_id>
<album_id>2839379</album_id>
<photo_title>7月3日のお弁当 鶏の梅紫...</photo_title>
<favorite_num>0</favorite_num>
<comment_num>0</comment_num>
<view_num>0</view_num>
<copyright>normal</copyright>
<original_height>1053</original_height>
<original_width>1192</original_width>
<date>2014-07-03</date>
<regist_time>2014-07-03T20:24:42+09:00</regist_time>
<url>http://photozou.jp/photo/show/1058696/206593504</url>
<image_url>http://art6.photozou.jp/pub/696/1058696/photo/206593504.jpg</image_url>
<original_image_url>http://art6.photozou.jp/pub/696/1058696/photo/206593504_org.jpg</original_image_url>
<thumbnail_image_url>http://art6.photozou.jp/pub/696/1058696/photo/206593504_thumbnail.jpg</thumbnail_image_url>
<large_tag><![CDATA[<a href="http://photozou.jp/photo/show/1058696/206593504"><img src="http://art6.photozou.jp/pub/696/1058696/photo/206593504.v1404386683.jpg" alt="7月3日のお弁当 鶏の梅紫..." width="450" height="398"></a><br><a href="http://photozou.jp/photo/show/1058696/206593504">7月3日のお弁当 鶏の梅紫...</a> posted by <a href="http://photozou.jp/user/top/1058696">(C)graystarling691</a>]]></large_tag>
<medium_tag><![CDATA[<a href="http://photozou.jp/photo/show/1058696/206593504"><img src="http://art6.photozou.jp/pub/696/1058696/photo/206593504.v1404386683.jpg" alt="7月3日のお弁当 鶏の梅紫..." width="240" height="212"></a><br><a href="http://photozou.jp/photo/show/1058696/206593504">7月3日のお弁当 鶏の梅紫...</a> posted by <a href="http://photozou.jp/user/top/1058696">(C)graystarling691</a>]]></medium_tag>
</photo>
</info>
</rsp>"
EOD;
        $photozou = new Services_Photozou($this->user, $this->password);
        $this->injectMock($photozou, $xml);

        $params = array(
            'keyword' => 'ご飯',
            'limit' => 2,
        );
        $result = $photozou->search_public($params);
        $this->assertEquals(2, count($result));
    }

}
