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
class SNMPModel
{
    const DEFAULT_RECEIVERS_CLASS_MAP = [
        'get' => ReceiveGet::class,
        'walk' => ReceiveWalk::class,
    ];

    const DEFAULT_MODIFIERS_CLASS_MAP = [
        'numeric' => ModifierNumeric::class,
    ];

    const ONE_SECOND = 1000000;

    /**
     * SNMP version.
     *
     * @var int
     */
    protected $snmpVersion = \SNMP::VERSION_2c;

    /**
     * Connection timeout.
     *
     * @var int
     */
    protected $snmpTimeout = self::ONE_SECOND;

    /**
     * SNMP community.
     *
     * @var string
     */
    protected $snmpCommunity;

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
     * Enable quick print ?
     *
     * @var bool
     */
    protected $quickPrint = true;

    /**
     * Enable enum print ?
     *
     * @var bool
     */
    protected $enumPrint = true;

    /**
     * SNMP decice IP address.
     *
     * @var string
     */
    protected $ip;

    /**
     * @var \SNMP
     */
    protected $snmpSession;

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
     * Initialize SNMP session.
     *
     * @throws \Maxbond\SNMPModel\SNMPModelException
     */
    public function initSNMPSession(): void
    {
        try {
            $this->snmpSession = new \SNMP($this->snmpVersion, $this->ip, $this->snmpCommunity, $this->snmpTimeout);
            $this->snmpSession->exceptions_enabled = true;
            $this->snmpSession->oid_output_format = SNMP_OID_OUTPUT_NUMERIC;

            if (true === $this->quickPrint) {
                $this->snmpSession->quick_print = true;
            }
            if (true === $this->enumPrint) {
                $this->snmpSession->enum_print = true;
            }
        } catch (\Exception $e) {
            throw new SNMPModelException($e->getMessage());
        }
    }

    /**
     * Get SNMP session
     *
     * @return null|\SNMP
     */
    public function getSNMPSession(): ?\SNMP
    {
        return $this->snmpSession;
    }

    /**
     * Set SNMP session
     *
     * @param \SNMP $snmpSession
     */
    public function setSNMPSession(\SNMP $snmpSession): void
    {
        $this->snmpSession = $snmpSession;
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
