<?php

use PHPUnit\Framework\TestCase;
use Maxbond\SNMPModel\ReceiversClassMap;
use Maxbond\SNMPModel\PackModelByType;
use Maxbond\SNMPModel\Receivers\ReceiveGet;
use Maxbond\SNMPModel\Receivers\ReceiveWalk;

class PackModelByTypeTest extends TestCase
{
    protected $receiverClassMap;

    protected $model;

    public function testPackShouldAggregateArray()
    {
        $receiverClass = new ReceiversClassMap($this->receiverClassMap);
        $packed = PackModelByType::pack($this->model, $receiverClass);

        $this->assertArrayHasKey('get', $packed);
        $this->assertArrayHasKey('walk', $packed);
        $this->assertArrayNotHasKey('t1', $packed);
        $this->assertArrayNotHasKey('t3', $packed);
        $this->assertArrayHasKey('t1', $packed['get']);
        $this->assertArrayHasKey('t3', $packed['walk']);
    }

    public function setUp(): void
    {
        $this->receiverClassMap = [
            'get' => ReceiveGet::class,
            'walk' => ReceiveWalk::class,
        ];

        $this->model = [
            't1' => ['type' => 'get', 'oid' => '1.3.6.1.2.1.1.1.0'],
            't2' => ['type' => 'get', 'oid' => '1.3.6.1.2.1.1.1.0'],
            't3' => ['type' => 'walk', 'oid' => '1.3.6.1.2.1.1.1'],
            't4' => ['type' => 'walk', 'oid' => '1.3.6.1.2.1.1.1'],
        ];
    }
}
