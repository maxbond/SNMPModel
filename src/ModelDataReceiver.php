<?php

namespace Maxbond\SNMPModel;

/**
 * Class ModelDataReceiver
 *
 * @package Maxbond\SNMPModel
 */
class ModelDataReceiver
{
    /**
     * @var array
     */
    protected $model;

    /**
     * @var \Maxbond\SNMPModel\ReceiversClassMap
     */
    protected $receiverClassMap;

    protected $modifiersClassMap;

    /**
     * ModelBuilder constructor.
     *
     * @param array                                     $model
     * @param \Maxbond\SNMPModel\ReceiversClassMap      $receiversClassMap
     * @param \Maxbond\SNMPModel\ModifiersClassMap|null $modifiersClassMap
     */
    public function __construct(
        array $model,
        ReceiversClassMap $receiversClassMap,
        ModifiersClassMap $modifiersClassMap
    ) {
        $this->model = $model;
        $this->receiverClassMap = $receiversClassMap;
        $this->modifiersClassMap = $modifiersClassMap;
    }

    /**
     * Get model data from session.
     *
     * @param \SNMP $snmpSession
     *
     * @return array
     *
     * @throws \Maxbond\SNMPModel\SNMPModelException
     */
    public function getData(\SNMP $snmpSession): array
    {
        $byTypeReceiver = new ReceiveByType($this->model, $this->receiverClassMap, $snmpSession);

        try {
            $result = $byTypeReceiver->receive();
        } catch (\Exception $e) {
            throw new SNMPModelException($e->getMessage());
        }

        $modifierByType = new ModifyByType($this->model, $this->modifiersClassMap);
        $result = $modifierByType->modify($result);

        return $result;
    }
}
