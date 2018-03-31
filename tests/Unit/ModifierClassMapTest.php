<?php

use PHPUnit\Framework\TestCase;
use Maxbond\SNMPModel\Modifiers\ModifierNumeric;
use Maxbond\SNMPModel\ModifiersClassMap;

class ModifierClassMapTest extends TestCase
{
    public function testModifierShouldAddNewMap()
    {
        $classMap = [
            'numeric' => ModifierNumeric::class,
        ];

        $modifierMap = new ModifiersClassMap($classMap);
        $modifierMap->register('float', ModifierNumeric::class);
        $types = $modifierMap->getTypes();

        $this->assertContains('numeric', $types);
        $this->assertContains('float', $types);
        $this->assertEquals(ModifierNumeric::class, $modifierMap->getClass('numeric'));
    }
}
