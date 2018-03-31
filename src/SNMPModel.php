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

    /**
     * @var int
     */
    protected $snmpVersion = \SNMP::VERSION_2c;

    /**
     * @var int
     */
    protected $snmpTimeout = 1000000;

    /**
     * @var string
     */
    protected $snmpCommunity;

    /**
     * @var array
     */
    protected $model;

    /**
     * @var string
     */
    protected $fileName = '';

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
     * @var string
     */
    private $ip;

    /**
     * @var \SNMP
     */
    private $snmpSession;

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
     * @throws \Exception
     */
    public function get()
    {
        if (null === $this->model) {
            throw new SNMPModelException('Model scheme is empty');
        }

        if (null === $this->snmpSession) {
            try {
                $this->initSNMP();
            } catch (\Exception $e) {
                throw new SNMPModelException($e->getMessage());
            }
        }

        $modelBuilder = new ModelBuilder($this->model, $this->receiversClassMap, $this->modifiersClassMap);

        try {
            $result = $modelBuilder->get($this->snmpSession);
        } catch (\Exception $e) {
            throw new SNMPModelException($e->getMessage());
        }

        return $result;
    }

    /**
     *  Boot model.
     */
    protected function boot()
    {
        if (!empty($this->fileName)) {
            $this->model = $this->loadModelFromYAMLFile($this->fileName);
        }
    }

    /**
     * Register all class maps.
     */
    protected function initClassMaps()
    {
        $modifiersClasses = static::DEFAULT_MODIFIERS_CLASS_MAP;
        $receiversClasses = static::DEFAULT_RECEIVERS_CLASS_MAP;
        if (null !== $this->modifiersClasses) {
            $modifiersClasses = array_merge($modifiersClasses, $this->modifiersClasses);
        }
        if (null !== $this->receiversClasses) {
            $receiversClasses = array_merge($receiversClasses, $this->receiversClasses);
        }
        $this->receiversClassMap = new ReceiversClassMap($receiversClasses);
        $this->modifiersClassMap = new ModifiersClassMap($modifiersClasses);
    }

    /**
     * Load model from YAML file.
     *
     * @param $fileName
     *
     * @return mixed
     */
    protected function loadModelFromYAMLFile($fileName)
    {
        return Yaml::parseFile($fileName);
    }

    /**
     * Initialize SNMP session.
     *
     * @throws \Exception
     */
    protected function initSNMP()
    {
        try {
            $this->snmpSession = new \SNMP($this->snmpVersion, $this->ip, $this->snmpCommunity, $this->snmpTimeout);
            $this->snmpSession->exceptions_enabled = true;
            $this->snmpSession->oid_output_format = SNMP_OID_OUTPUT_NUMERIC;
            if ($this->quickPrint) {
                $this->snmpSession->quick_print = true;
            }
            if ($this->enumPrint) {
                $this->snmpSession->enum_print = true;
            }
        } catch (\Exception $e) {
            throw new SNMPModelException($e->getMessage());
        }
    }
}
