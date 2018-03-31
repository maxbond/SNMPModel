<?php

use PHPUnit\Framework\TestCase;
use Maxbond\SNMPModel\Receivers\ReceiveGet;
use Maxbond\SNMPModel\Receivers\ReceiveWalk;
use Maxbond\SNMPModel\ReceiversClassMap;

class ReceiverClassMapTest extends TestCase
{
    public function testReceiverShouldAddNewMap()
    {
        $classMap = [
          'get' => ReceiveGet::class,
        ];

        $receiverMap = new ReceiversClassMap($classMap);
        $receiverMap->register('walk', ReceiveWalk::class);
        $types = $receiverMap->getTypes();

        $this->assertContains('get', $types);
        $this->assertContains('walk', $types);
        $this->assertEquals(ReceiveWalk::class, $receiverMap->getClass('walk'));
    }
}
