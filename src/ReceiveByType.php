<?php

namespace Maxbond\SNMPModel;

/**
 * Class ReceiveByType
 * Receive SNMP oid by given receiver class map.
 */
class ReceiveByType
{
    const DEFAULT_RECEIVER_TYPE = 'get';

    const RECEIVER_FIELD = 'type';

    const OID_FIELD = 'oid';

    /**
     * @var \Maxbond\SNMPModel\ReceiversClassMap
     */
    protected $receiverClassMap;

    /**
     * @var \SNMP
     */
    protected $snmpSession;

    /**
     * @var array
     */
    protected $packedModel;

    /**
     * ReceiveByType constructor.
     *
     * @param array $model
     * @param \Maxbond\SNMPModel\ReceiversClassMap $receiverClassMap
     * @param \SNMP $snmpSession
     */
    public function __construct(array $model, ReceiversClassMap $receiverClassMap, \SNMP $snmpSession)
    {
        $this->receiverClassMap = $receiverClassMap;
        $this->snmpSession = $snmpSession;
        $this->packedModel = $this->aggregateTypes($model);
    }

    /**
     * Get receiver and execute get method.
     *
     * @return array
     *
     * @throws \Maxbond\SNMPModel\SNMPModelException
     */
    public function receive(): array
    {
        $result = [];
        foreach ($this->packedModel as $type => $item) {
            try {
                $receiverName = $this->receiverClassMap->getClass($type);
            } catch (\Exception $e) {
                throw new SNMPModelException($e->getMessage());
            }
            $receiver = new $receiverName($this->snmpSession);
            $result = array_merge($receiver->get($item), $result);
        }

        return $result;
    }

    /**
     * Aggregate model types to one entity
     *
     * @param array $model
     * @return array
     */
    protected function aggregateTypes($model): array
    {
        $aggregatedTypes = [];
        foreach ($model as $name => $item) {
            if (! array_key_exists(static::OID_FIELD, $item)) {
                continue;
            }
            if (array_key_exists(static::RECEIVER_FIELD, $item)
                && $this->receiverClassMap->typeExist($item[static::RECEIVER_FIELD])) {
                    $aggregatedTypes[$item[static::RECEIVER_FIELD]][$name] = $item[static::OID_FIELD];
            } else {
                $aggregatedTypes[static::DEFAULT_RECEIVER_TYPE][$name] = $item[static::OID_FIELD];
            }
        }

        return $aggregatedTypes;
    }
}
