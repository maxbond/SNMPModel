## SNMP model library

Use model pattern for work with SNMP data

example:

<code>

class ExampleModel extends SNMPModel

{

    protected $model = [
    
    		'ethernetOperStatus' => [
    		
    			'oid' => '.1.3.6.1.2.1.2.2.1.8.1',
    			
    			'type' => 'get'
    			
    		]
    		
    ];
    
}

$example = new ExampleModel('127.0.0.1','public');

var_dump($example->get());

</code> 

See example for more detail.


