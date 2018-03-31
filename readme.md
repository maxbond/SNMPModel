## SNMP model library

Use model pattern for work with SNMP data

composer install: composer require maxbond/snmpmodel

example:

```
class ExampleModel extends SNMPModel
{
    protected $model = [    
    		'ethernetOperStatus' => [    		
    			'oid' => '.1.3.6.1.2.1.2.2.1.8.1',    			
    			'type' => 'get',
    			'modifier' => 'numeric'    			
    		]    		
    ];    
}

$exampleModel = new ExampleModel('127.0.0.1','public');
var_dump($exampleModel->get());

``` 

See example for more detail.


