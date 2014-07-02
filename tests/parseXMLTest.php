<?php
class parseXMLTest extends PHPUnit_Framework_TestCase
{
    public function testParseUserGroup()
    {
        $xml = <<<EOD
<rsp stat="ok">
<info>
  <user_group>
    <group_id>2091</group_id>
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

        $photozou = new Services_Photozou('test', 'none');
        $method = self::getPrivateMethod($photozou, 'parseXML');
        $result = $method->invoke($photozou, $xml, ['group_id']);

        $this->assertEquals($result['group_id'], 2091);
    }

    public static function getPrivateMethod($obj, $name) {
        $class = new ReflectionClass($obj);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

}

