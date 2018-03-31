<?php

namespace Maxbond\SNMPModel;

class PackModelByType
{
    /**
     * Aggregate model items by type.
     *
     * @param array                                $model
     * @param \Maxbond\SNMPModel\ReceiversClassMap $receiverClassMap
     *
     * @return array
     */
    public static function pack(array $model, ReceiversClassMap $receiverClassMap): array
    {
        $packModel = [];
        $types = $receiverClassMap->getTypes();
        foreach ($model as $name => $item) {
            if (in_array($item['type'], $types)) {
                $packModel[$item['type']][$name] = $item['oid'];
            }
        }

        return $packModel;
    }
}
