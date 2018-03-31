<?php

use PHPUnit\Framework\TestCase;

class ModifierTest extends TestCase
{
    public function testModifierShouldChangeValue(): void
    {
        $value = 'INTEGER: 20 db';

        $modifier = new Maxbond\SNMPModel\Modifiers\ModifierNumeric();

        $this->assertEquals(20, $modifier->modify($value));
    }
}
