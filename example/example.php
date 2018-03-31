<?php

require '../vendor/autoload.php';

use Maxbond\SNMPModel\SNMPModel;
use Maxbond\SNMPModel\Interfaces\ModifiersInterface;

/**
 * Custom value modifier class
 */
class PortModifier implements ModifiersInterface
{
    public function modify($value)
    {
        $value = (int) $value;
        switch ($value) {
            case 1:
                return 'up';
            case 3:
                return 'dormant';
            default:
                return 'down';
        }
    }
}

class ExampleModel extends SNMPModel
{
    protected $fileName = 'demo.yaml';

    /**
     * Add custom modifier to model
     * @var array
     */
    protected $modifiersClasses = [
        'port' => PortModifier::class,
    ];
}

$model = new ExampleModel('192.168.0.10', 'public');

try {
    print_r($model->get());
} catch (\Exception $e) {
    echo $e->getMessage();
}
