<?php

use PHPUnit\Framework\TestCase;
use Maxbond\SNMPModel\Modifiers\ModifierNumeric;
use Maxbond\SNMPModel\ModifiersClassMap;
use Maxbond\SNMPModel\ModifyByType;

class ModifyByTypeTest extends TestCase
{
    public function testModifierShouldAddNewMap()
    {
        $classMap = [
            'numeric' => ModifierNumeric::class,
        ];
        $model = [
            't1' => ['type' => 'get', 'oid' => '1.3.6.1.2.1.1.1.0', 'modifier' => 'numeric'],
        ];
        $modifierMap = new ModifiersClassMap($classMap);

        $result = ['t1' => 'INTEGER: 20 d'];

        $modifer = new ModifyByType($model, $modifierMap);

        $modifed = $modifer->modify($result);

        $this->assertArrayHasKey('t1', $modifed);
        $this->assertEquals(20, $modifed['t1']);
    }
}
