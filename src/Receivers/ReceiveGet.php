<?php

namespace Maxbond\SNMPModel\Receivers;

use Maxbond\SNMPModel\HasSNMPSession;
use Maxbond\SNMPModel\Interfaces\ReceiverInterface;
use Maxbond\SNMPModel\SNMPModelException;

class ReceiveGet implements ReceiverInterface
{
    use HasSNMPSession;

    /**
     * Fetch snmp values.
     *
     * @param array $oidList
     *
     * @return array
     *
     * @throws \Exception
     */
    public function get(array $oidList): array
    {
        $responseData = [];
        try {
            $bulkData = $this->snmpSession->get(array_values($oidList));
        } catch (\Exception $e) {
            throw new SNMPModelException($e->getMessage());
        }
        if (!empty($bulkData)) {
            foreach ($oidList as $key => $value) {
                $responseData[$key] = $bulkData[$value];
            }
        }

        return $responseData;
    }
}
