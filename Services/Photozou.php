<?php
/**
 * Photozou.php
 *
 * @package Services_Photozou
 */

/**
 * Services_Photozou
 *
 * @package Services_Photozou
 */
class Services_Photozou
{
    const API_URL = 'http://api.photozou.jp/rest/';
    private $username;
    private $password;

    /**
     * @see http://photozou.jp/basic/api_error
     * TODO: constにする
     */
    public $error_code = array(
        'INVALID_DATE'          =>  0,
        'INVALID_EMAIL_ADDRESS' =>  1,
        'INVALID_ID'            =>  2,
        'INVALID_IMAGE_TYPE'    =>  3,
        'INVALID_INT'           =>  4,
        'INVALID_PASSWORD'      =>  5,
        'INVALID_UNSIGNED_INT'  =>  6,
        'INVALID_URL'           =>  7,
        'INVALID_USER_ID'       =>  8,
        'IS_EMPTY'              =>  9,
        'IS_REGISTERED'         => 10,
        'MAX_PHOTOS'            => 11,
        'TOO_LONG_STRING'       => 12,
        'UPLOAD_ERR_FORM_SIZE'  => 13,
        'UPLOAD_ERR_NO_FILE'    => 14,
        'UPLOAD_ERR_PARTIAL'    => 15,
        'ERROR_UNKNOWN'         => 16,
    );

    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    private static function getFileExt($filename)
    {
        $ext = "";
        $filename = basename($filename);
        $len = strlen($filename);
        $len--;
        while ($len >= 0) {
            if ($filename{$len} != '.') {
                $ext = $ext . $filename{$len};
            } else {
                break;
            }
            $len--;
        }
        $ext = strtolower(strrev($ext));
        return $ext;
    }

    private static function getMime($filename)
    {
        $ext = self::getFileExt($filename);
        switch ($ext) {
        case 'gif' :
            $mime = 'image/gif';
            break;
        case 'jpg' :
            $mime = 'image/jpeg';
            break;
        case 'jpeg' :
            $mime = 'image/jpeg';
            break;
        case 'pjpeg' :
            $mime = 'image/pjpeg';
            break;
        case 'png' :
            $mime = 'image/png';
            break;
        case 'x-png' :
            $mime = 'image/x-png';
            break;
        default:
            $mime = 'image/gif';
        }

        return $mime;
    }

    /**
     * callMethod
     *
     * @param string $method_name
     * @param array  $send_param
     * @param string $method
     * @return string result XML data
     */
    private function callMethod($method_name, array $send_param = [], $method = 'post')
    {
        $client = new \GuzzleHttp\Client([
            'base_url' => self::API_URL,
            'defaults' => [
                'auth'    => [$this->username, $this->password],
            ]
        ]);

        if ($method == 'get') {
            $response = $client->get($method_name, ['query' => $send_param]);
        } elseif ($method == 'post') {
            if (isset($send_param['photo']) && $method_name == "photo_add") {
                $request->addFile('photo', $send_param['photo'], self::getMime($send_param['photo']));
                $send_param['photo'] = \GuzzleHttp\Post\PostFile('photo', file_get_contents($send_param['photo']), $send_param['photo']);
            }

            $response = $client->post($method_name, $send_param);
        }

        $xml = (string)$response->getBody();

        if (strpos($xml, 'rsp stat="fail"') !== false) {
            $matches = array();
            preg_match('|err code="(.*?)" msg="(.*?)"|s', $xml, $matches);
            $code = 0;
            if (isset($this->error_code[$matches[1]])) {
                $code = $this->error_code[$matches[1]];
            }

            throw new ErrorException($matches[1] . ':' . $matches[2] . $code);
        }

        return $xml;
    }

    public function nop()
    {
        $xml = $this->callMethod("nop");

        if (strpos($xml, 'stat="ok"') !== false) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * photo_add
     *
     * photo(必須)
     * 写真/動画データ
     *
     * album_id(必須)
     * アルバムID
     *
     * photo_title
     * 写真タイトル
     *
     * tag
     * 写真に追加するタグ
     * （※複数のタグを指定する場合は、スペース区切りで指定してください）
     *
     * comment
     * 写真に追加するコメント
     *
     * date_type
     * 'exif'(デフォルト), 'date'の2種類を指定できます。
     * 'exif'を指定すると exif のデータから写真/動画の日付を取得します。
     * 'date'を指定した場合は'year', 'month', 'day'の3つのパラメータに
     * よって日付の指定を行ないます。 
     *
     * year
     * 日付の'年'を指定します。
     *
     * month
     * 日付の'月'を指定します。
     *
     * day
     * 日付の'日'を指定します。 
     */
    public function photo_add(array $params)
    {
        $tags = array(
            'photo_id',
            'large_tag',
            'medium_tag'
        );
        $xml = $this->callMethod("photo_add", $params, 'post');

        return self::parseXML($xml, $tags);
    }

    /**
     * photo_add_album
     *
     * name(必須)
     * アルバム名（最大40文字）
     *
     * description
     * アルバムの説明（最大1000文字）
     *
     * cover
     * アルバムの表紙の写真データ(最大10MB)
     *
     * perm_type
     * 写真の公開権限を指定します。
     * 'allow'(デフォルト), 'deny'の2種類があります。
     * 'allow'の場合はアクセスを許可し、'deny'の場合はアクセスを拒否します。
     *
     * perm_type2
     * 写真の公開権限の範囲を指定します。
     * 'net'(デフォルト), 'everyone', 'all', 'user_group'の4種類あります。
     * それぞれ次を意味します。
     * 'net': インターネット
     * 'everyone': フォト蔵全体
     * 'all': 友達全員
     * 'user_group': ユーザーグループ
     *
     * perm_user_id
     * 'perm_type2' が 'user_group' の場合だけ有効な値です。
     * 公開する友達のユーザーIDを指定します。
     * 複数の友達のユーザーIDを指定する場合は、スペース区切りで指定してください。
     *
     * order_type
     * 写真の並び順です。次の値があります。
     * 'upload'(デフォルト): アップロード順
     * 'date': 日付順
     * 'comment': コメント順
     * 'file_name': ファイル名順
     * 値の後に'2'がつくと(例: upload2)逆順になります。
     *
     * copyright_type
     * 著作権タイプを指定します。 'normal'(デフォルト),
     * 'creativecommons'の2種類があります。
     * クリエイティブコモンズによる著作権を設定したい場合に
     * 'creativecommons'を指定して下さい。
     *
     * copyright_commercial
     * クリエイティブコモンズによる著作権、営利目的での利用を指定します。
     * 'yes'(デフォルト), 'no'の2種類を指定できます。
     * 'yes'の場合は利用を許可、'no'の場合は利用を許可しません。
     *
     * copyright_modification
     * クリエイティブコモンズによる著作権、改変の許可を指定します。
     * 'yes'(デフォルト), 'no', 'share'の3種類を指定できます。
     * 'yes'の場合は改変を許可、'no'の場合は改変を許可しません。
     * 'share'の場合は他の人が同一条件化で配付する場合のみ変更を許可します。
     *
     */
    public function photo_add_album(array $params)
    {
        $tags = array('album_id');

        $xml = $this->callMethod('photo_add_album', $params, 'post');

        return self::parseXML($xml, $tags);
    }

    /**
     * photo_add_tag
     *
     * @TODO implement me
     */
    public function photo_add_tag(array $params)
    {
        $xml = $this->callMethod("photo_add_tag", $params, "post");
    }

    /**
     * photo_album
     */
    public function photo_album()
    {
        $result = array();
        $tags = array(
            'album_id',
            'user_id',
            'name',
            'description',
            'perm_type',
            'perm_type2',
            'perm_id',
            'order_type',
            'photo_num',
        );
        $xml = $this->callMethod('photo_album', array(), 'get');

        $match = self::getBlock($xml, "album");
        foreach ($match as $item) {
            $result[] = self::parseXML($item, $tags);
        }

        return $result;
    }

    /**
     * photo_comment
     *
     */
    public function photo_comment(array $params)
    {
        $result = array();
        $tags = array(
            'photo_comment_id',
            'photo_id',
            'user_id',
            'comment',
        );

        $xml = $this->callMethod("photo_comment", $params, "get");

        $list = self::getBlock($xml, "photo_comment");

        foreach ($list as $block) {
            $result[] = self::parseXML($block, $tags);
        }

        return $result;
    }

    /**
     * photo_edit_album
     *
     * @access public
     *
     *
     * name(必須)
     * アルバム名
     *
     * description
     * アルバムの説明
     *
     * perm_type
     * 写真/動画の公開権限を指定します。
     * 'allow'(デフォルト), 'deny'の2種類があります。
     * 'allow'の場合はアクセスを許可し、'deny'の場合はアクセスを拒否します。 
     *
     * perm_type2
     * 写真/動画の公開権限の範囲を指定します。
     * 'net'(デフォルト), 'everyone', 'all', 'user_group'の4種類あります。
     * それぞれ次を意味します。
     * 'net': インターネット
     * 'everyone': フォト蔵全体
     * 'all': 友達全員
     * 'user_group': ユーザーグループ 
     *
     * perm_id
     * 'perm_type2'が'user_group'の場合にのみ有効な値です。
     * ユーザーグループIDを指定します。 
     *
     * order_type
     * 写真/動画の並び順です。次の値があります。
     * 'upload'(デフォルト): アップロード順
     * 'date': 日付順
     * 'comment': コメント順
     * 'file_name': ファイル名順
     * 値の後に'2'がつくと(例: upload2)逆順になります。 
     *
     * copyright_type
     * 著作権タイプを指定します。 'normal'(デフォルト),
     * 'creativecommons'の2種類があります。
     * クリエイティブコモンズによる著作権を設定したい場合に
     * 'creativecommons'を指定して下さい。 
     *
     * copyright_commercial
     * クリエイティブコモンズによる著作権、営利目的での利用を指定します。
     * 'yes'(デフォルト), 'no'の2種類を指定できます。
     * 'yes'の場合は利用を許可、'no'の場合は利用を許可しません。 
     *
     * copyright_modification
     * クリエイティブコモンズによる著作権、改変の許可を指定します。
     * 'yes'(デフォルト), 'no', 'share'の3種類を指定できます。
     * 'yes'の場合は改変を許可、'no'の場合は改変を許可しません。
     * 'share'の場合は他の人が同一条件化で配付する場合のみ変更を許可します。 
     *
     */
    public function photo_edit_album(array $params)
    {
        $xml = $this->callMethod('photo_edit_album', $params, 'post');
        return self::parseXML($xml, array());
    }

    /**
     * photo_info
     *
     * @access public
     * @param int $photo_id
     * @return array
     */
    public function photo_info($photo_id)
    {
        if (!is_numeric($photo_id)) {
            throw new InvalidArgumentException;
        }

        $tags = array(
            'photo_id',
            'user_id',
            'album_id',
            'photo_title',
            'description',
            'favorite_num',
            'comment_num',
            'view_num',
            'copyright',
            'copyright_commercial',
            'copyright_modifications',
            'regist_time',
            'tags',
            'url',
            'image_url',
            'original_image_url',
            'thumbnail_image_url',
            'large_tag',
            'medium_tag',
        );

        $xml = $this->callMethod("photo_info", array('photo_id' => $photo_id), "get");

        return self::parseXML($xml, $tags);
    }

    /**
     * photo_list_public
     *
     * type(必須)
     * 写真/動画一覧の取得
     * 'public', 'everyone', 'album', 'friend', 'popular', 'cc' の
     * いずれかを指定します。
     * 'public': インターネットに公開されている写真/動画の一覧
     * (ユーザIDの指定が必須となります)
     * 'everyone': インターネットに公開されているすべての人の
     * 写真/動画の一覧
     * 'album': 特定のアルバムの写真/動画の一覧
     * (ユーザIDとアルバムIDの指定が必須となります)
     * 'friend': 指定されたユーザの友達の写真/動画の一覧
     * (ユーザIDの指定が必須となります)
     * 'pupular': 人気のある写真/動画の一覧
     * 'cc': クリエイティブコモンズが設定されている写真/動画の一覧
     *
     * user_id
     * ユーザID
     * type が album あるいは friend のときは必須となります。 
     *
     * album_id
     * アルバムID
     * type が album のときは必須となります。 
     *
     * license
     * クリエイティブコモンズの採否を指定します。
     * type が cc のときのみ有効です。
     * 'by': 帰属
     * 'by_sa': 帰属-同一条件承諾
     * 'by_nd': 帰属-派生禁止
     * 'by_nc': 帰属-非営利
     * 'by_nc_sa': 帰属-非営利-同一条件許諾
     * 'by_nc_nd': 帰属-非営利-派生禁止
     *
     * limit
     * 取得する一覧の上限を指定します。(省略時100件、最大1000件) 
     */
    public function photo_list_public(array $param)
    {
        $results = array();
        $tags = array(
            //'photo_num',
            'photo_id',
            'user_id',
            'album_id',
            'photo_title',
            'favorite_num',
            'comment_num',
            'view_num',
            'copyright',
            'copyright_commercial',
            'copyright_modifications',
            'regist_time',
            //'tags',
            'url',
            'image_url',
            'original_image_url',
            'thumbnail_image_url',
            'large_tag',
            'medium_tag',
        );

        $xml = $this->callMethod("photo_list_public", $param, "get");

        $list = self::getBlock($xml, "photo");
        foreach ($list as $photo) {
            $results[] = self::parseXML($photo, $tags);
        }

        return $results;
    }

    /**
     * user_info
     *
     */
    public function user_info($param)
    {
        $result = array();
        $tags = array(
            'user_id',
            'profile_url',
            'nick_name',
            'my_pic',
            'photo_num',
            'friends_num',
        );

        if (!is_array($param)) {
            $param = array('user_id' => $param);
        }

        $xml = $this->callMethod("user_info", $param, "get");

        $list = self::getBlock($xml, "user");
        foreach ($list as $photo) {
            $result = self::parseXML($photo, $tags);
        }

        return $result;
    }

    /**
     * user_group
     *
     * 友達グループの一覧を取得します。
     */
    public function user_group()
    {
        $tags = array(
            'group_id',
            'name',
            'user_num',
        );

        $xml = $this->callMethod("user_group", array(), "get");

        $result = [];
        $match = self::getBlock($xml, 'user_group');
        foreach ($match as $item) {
            $result[] = self::parseXML($item, $tags);
        }

        return $result;
    }

    /**
     * search_public
     *
     * @access public
     *
     * type
     * コンテントのタイプ。
     * 'photo', 'video', 'all' のいずれかを指定します。
     * 'photo': 写真
     * 'video': 動画
     * 'all': すべて(省略時) 
     *
     * keyword
     * タイトルに含まれるキーワードを指定します。
     * 最大100文字まで指定できます。 
     *
     * copyright
     * 著作権表示の型を指定します。
     * 'normal', 'creativecommons', 'all' のうちから指定します。
     * 'normal': ライセンスなし
     * 'creativecommons': クリエイティブコモンズ
     * 'all': すべて(省略時) 
     *
     * copyright_commercial
     * クリエイティブコモンズによる著作権、営利目的での利用を指定します。
     * ('copyright'パラメータに'creativecommons'を指定したときのみ有効)
     * 'yes'(デフォルト), 'no'の2種類を指定できます。
     * 'yes'の場合は利用を許可、'no'の場合は利用を許可しません。 
     *
     * copyright_modifications
     * クリエイティブコモンズによる著作権、改変の許可を指定します。
     * ('copyright'パラメータに'creativecommons'を指定したときのみ有効)
     * 'yes'(デフォルト), 'no', 'share'の3種類を指定できます。
     * 'yes'の場合は改変を許可、'no'の場合は改変を許可しません。
     * 'share'の場合は他の人が同一条件化で配付する場合のみ変更を許可します。 
     *
     * limit
     * 検索の上限を指定します。(省略時100件、最大1000件)
     *
     * offset
     * 検索のオフセットを指定します。(省略時: 0) 
     */
    public function search_public(array $params)
    {
        $result = array();
        $tags = array(
            'photo_id',
            'user_id',
            'album_id',
            'favorite_num',
            'comment_num',
            'copyright',
            'copyright_commercial',
            'copyright_modifications',
            'regist_time',
            'url',
            'image_url',
            'original_image_url',
            'thumbnail_image_url',
            'large_tag',
            'medium_tag'
        );

        $xml = $this->callMethod("search_public", $params, "get");

        $list = self::getBlock($xml, "photo");

        foreach ($list as $block) {
            $result[] = self::parseXML($block, $tags);
        }

        return $result;

    }

    /**
     * getBlock
     */
    private static function getBlock($xml, $block_name)
    {
        $xml = str_replace("\n", '', $xml);
        $pattern = "|<{$block_name}>(.*?)</{$block_name}>|s";
        preg_match_all($pattern, $xml, $matches);
        unset($matches[0]);
        return current($matches);
    }

    /**
     * parseXML
     */
    private static function parseXML($xml, array $parse_param)
    {
        $result = [];
        $xml = str_replace("\n", '', $xml);
        foreach ($parse_param as $key) {
            $pattern = "|<{$key}>(.*?)</{$key}>|";
            $is_match = preg_match($pattern, $xml, $matches);
            if ($is_match <= 0) {
                //unmatch
            } else {
                // experimental(CDATA tag strip)
                $re_matches = array();
                if (preg_match('|<\!\[CDATA\[(.*?)\]\]>|', $matches[1], $re_matches)) {
                    $result[$key] = $re_matches[1];
                } else {
                    $result[$key] = $matches[1];
                }
            }
        }

        return $result;
    }

}

