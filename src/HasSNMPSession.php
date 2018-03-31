<?php

namespace Maxbond\SNMPModel;

trait HasSNMPSession
{
    /**
     * @var \SNMP
     */
    protected $snmpSession;

    public function __construct(\SNMP $snmpSession)
    {
        $this->snmpSession = $snmpSession;
    }
}
