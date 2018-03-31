<?php

namespace Maxbond\SNMPModel;

/**
 * Class ReceiveByType
 * Receive SNMP oid by given receiver class map.
 */
class ReceiveByType
{
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

    public function __construct(array $model, ReceiversClassMap $receiverClassMap, \SNMP $snmpSession)
    {
        $this->receiverClassMap = $receiverClassMap;
        $this->snmpSession = $snmpSession;
        $this->packedModel = PackModelByType::pack($model, $this->receiverClassMap);
    }

    /**
     * Get receiver and execute get method.
     *
     * @return array
     *
     * @throws \Exception
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
}
