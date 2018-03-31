<?php

namespace Maxbond\SNMPModel\Receivers;

use Maxbond\SNMPModel\Interfaces\ReceiverInterface;
use Maxbond\SNMPModel\HasSNMPSession;
use Maxbond\SNMPModel\SNMPModelException;

class ReceiveWalk implements ReceiverInterface
{
    use HasSNMPSession;

    /**
     * SNMP result keys delimiter.
     */
    const KEY_DELIMITER = '.';

    /**
     * Walk each OID in array.
     *
     * @param array $oidList
     *
     * @return array
     *
     * @throws \Exception
     */
    public function get(array $oidList): array
    {
        $eachOidResult = [];
        foreach ($oidList as $name => $oid) {
            $resultData = [];
            try {
                $results = $this->snmpSession->walk(trim($oid));
            } catch (\Exception $e) {
                throw new SNMPModelException($e->getMessage());
            }
            if (!empty($results)) {
                foreach ($results as $key => $result) {
                    $key = $this->key($key);
                    if (null === $key) {
                        continue;
                    }
                    $resultData[$key] = $result;
                }
                $eachOidResult[$name] = $resultData;
            }
        }

        return $eachOidResult;
    }

    /**
     * Use last SNMP index as key, .1.3.6.1.2.1.10.127.1.1.5.[0].
     *
     * @param $value
     *
     * @return null|string
     */
    protected function key(string $value): ?string
    {
        if (!strrpos($value, static::KEY_DELIMITER)) {
            return null;
        }

        return substr($value, strrpos($value, static::KEY_DELIMITER) + 1);
    }
}
