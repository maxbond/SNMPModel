<?php

namespace Maxbond\SNMPModel\Interfaces;

interface ReceiverInterface
{
    public function get(array $oidList): array;
}
