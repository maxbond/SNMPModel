<?php

namespace Maxbond\SNMPModel\Modifiers;

use Maxbond\SNMPModel\Interfaces\ModifiersInterface;

class ModifierNumeric implements ModifiersInterface
{
    const NUMBER_REGEXP = '/[-+]?[0-9]*\.?[0-9]+/';

    const SUFFIX_REGEXP = '/\w+:\s/';

    /**
     * Modify raw snmp value to float.
     *
     * @param $value
     *
     * @return float|null|string
     */
    public function modify($value)
    {
        $value = preg_replace(static::SUFFIX_REGEXP, '', $value);
        preg_match(static::NUMBER_REGEXP, $value, $match);

        return isset($match[0]) ? (float) $match[0] : 0;
    }
}
