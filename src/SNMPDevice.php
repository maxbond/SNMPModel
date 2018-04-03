<?php

namespace Maxbond\SNMPModel;

abstract class SNMPDevice
{
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
}