<?php

namespace Maxbond\SNMPModel;

use Maxbond\SNMPModel\Receivers\ReceiveGet;
use Maxbond\SNMPModel\Receivers\ReceiveWalk;
use Maxbond\SNMPModel\Modifiers\ModifierNumeric;
use Symfony\Component\Yaml\Yaml;

/**
 * Class SNMPModel
 * Base SNMP model class.
 */
class SNMPModel extends SNMPDevice
{
    const DEFAULT_RECEIVERS_CLASS_MAP = [
        'get' => ReceiveGet::class,
        'walk' => ReceiveWalk::class,
    ];

    const DEFAULT_MODIFIERS_CLASS_MAP = [
        'numeric' => ModifierNumeric::class,
    ];

    /**
     * Model scheme.
     *
     * @var array
     */
    protected $model;

    /**
     * YAML file.
     *
     * @var string
     */
    protected $file = '';

    /**
     * Child class receivers classes.
     *
     * @var array
     */
    protected $receiversClasses;

    /**
     * Child class receivers classes.
     *
     * @var array
     */
    protected $modifiersClasses;

    /**
     * @var \Maxbond\SNMPModel\ReceiversClassMap
     */
    private $receiversClassMap;

    /**
     * @var \Maxbond\SNMPModel\ModifiersClassMap
     */
    private $modifiersClassMap;

    /**
     * SNMPModel constructor.
     *
     * @param string $ip
     * @param string $snmpCommunity
     *
     * @throws \Maxbond\SNMPModel\SNMPModelException
     */
    public function __construct(
        string $ip,
        string $snmpCommunity
    ) {
        $this->ip = $ip;
        $this->snmpCommunity = $snmpCommunity;
        $this->snmpSession = null;
        $this->initClassMaps();
        $this->boot();
    }

    /**
     * Get data from SNMP device.
     *
     * @return array
     *
     * @throws \Maxbond\SNMPModel\SNMPModelException
     */
    public function get(): array
    {
        if (null === $this->model) {
            throw new SNMPModelException('Model scheme is empty');
        }

        if (null === $this->snmpSession) {
            try {
                $this->initSNMPSession();
            } catch (\Exception $e) {
                throw new SNMPModelException($e->getMessage());
            }
        }

        $modelDataReceiver = new ModelDataReceiver($this->model, $this->receiversClassMap, $this->modifiersClassMap);

        try {
            $result = $modelDataReceiver->getData($this->snmpSession);
        } catch (\Exception $e) {
            throw new SNMPModelException($e->getMessage());
        }

        return $result;
    }

    /**
     * Set model to given scheme.
     *
     * @param array $model
     */
    public function setModel(array $model): void
    {
        $this->model = $model;
    }

    /**
     *  Boot model.
     *
     * @throws \Maxbond\SNMPModel\SNMPModelException
     */
    protected function boot(): void
    {
        if (! empty($this->file)) {
            if (! file_exists($this->file)) {
                throw new SNMPModelException('File '.$this->file.' does not exists');
            }
            $this->model = Yaml::parseFile($this->file);
        }
    }

    /**
     * Register all class maps.
     */
    protected function initClassMaps(): void
    {
        /**
         * Register default class maps.
         */
        $modifiersClasses = static::DEFAULT_MODIFIERS_CLASS_MAP;
        $receiversClasses = static::DEFAULT_RECEIVERS_CLASS_MAP;

        /*
         * Merge with child class maps
         */
        if (null !== $this->modifiersClasses) {
            $modifiersClasses = array_merge($modifiersClasses, $this->modifiersClasses);
        }
        if (null !== $this->receiversClasses) {
            $receiversClasses = array_merge($receiversClasses, $this->receiversClasses);
        }

        $this->receiversClassMap = new ReceiversClassMap($receiversClasses);
        $this->modifiersClassMap = new ModifiersClassMap($modifiersClasses);
    }
}
