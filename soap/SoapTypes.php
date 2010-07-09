<?php
$server->wsdl->addComplexType(
    'file_attachment',
    'complexType',
    'struct',
    'all',
    '',
    array(
    	"task_id" => array('name'=>"id",'type'=>'xsd:string'),
	"filename" => array('name'=>"filename",'type'=>'xsd:string'),
	"location" => array('name'=>"location",'type'=>'xsd:string'),
     )
);

$server->wsdl->addComplexType(
    'name_value',
	'complexType',
   	 'struct',
   	 'all',
  	  '',
		array(
        	'name'=>array('name'=>'name', 'type'=>'xsd:string'),
			'value'=>array('name'=>'value', 'type'=>'xsd:string'),
		)
);

$server->wsdl->addComplexType(
    'name_value_users',
	'complexType',
   	 'struct',
   	 'all',
  	  '',
		array(
        	'name'=>array('name'=>'name', 'type'=>'xsd:string'),
			'value'=>array('name'=>'value', 'type'=>'xsd:string'),
			'email'=>array('name'=>'email','type'=>'xsd:string'),
		)
);

$server->wsdl->addComplexType(
    'name_value_list',
	'complexType',
   	'array',
   	'',
  	'SOAP-ENC:Array',
	array(),
    array(
    array('ref'=>'SOAP-ENC:arrayType', 'wsdl:arrayType'=>'tns:name_value[]')
    ),
	'tns:name_value'
);

$server->wsdl->addComplexType(
    'name_value_list_users',
	'complexType',
   	'array',
   	'',
  	'SOAP-ENC:Array',
	array(),
    array(
    array('ref'=>'SOAP-ENC:arrayType', 'wsdl:arrayType'=>'tns:name_value_users[]')
    ),
	'tns:name_value_users'
);

$server->wsdl->addComplexType(
   	 'set_entry_result',
   	 'complexType',
   	 'struct',
   	 'all',
  	  '',
	 array(
		'id' => array('name'=>'id', 'type'=>'xsd:string'),
		'error' => array('name' =>'error', 'type'=>'tns:error_value'),
	 )
);

$server->wsdl->addComplexType(
    'error_value',
	'complexType',
   	'struct',
   	'all',
  	'',
	array(
        	'number'=>array('name'=>'number', 'type'=>'xsd:string'),
			'name'=>array('name'=>'name', 'type'=>'xsd:string'),
			'description'=>array('name'=>'description', 'type'=>'xsd:string'),
		)
);

$server->wsdl->addComplexType(
   	 'get_entry_result',
   	 'complexType',
   	 'struct',
   	 'all',
  	  '',
	array(
		'field_list'=>array('name'=>'field_list', 'type'=>'tns:field_list'),
		'entry_list' => array('name' =>'entry_list', 'type'=>'tns:entry_list'),
		'error' => array('name' =>'error', 'type'=>'tns:error_value'),
	)
);

$server->wsdl->addComplexType(
    'field_list',
	'complexType',
   	 'array',
   	 '',
  	  'SOAP-ENC:Array',
	array(),
    array(
        array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:field[]')
    ),
	'tns:field'
);

$server->wsdl->addComplexType(
    'entry_list',
	'complexType',
   	 'array',
   	 '',
  	  'SOAP-ENC:Array',
	array(),
    array(
        array('ref'=>'SOAP-ENC:arrayType', 'wsdl:arrayType'=>'tns:entry_value[]')
    ),
	'tns:entry_value'
);

$server->wsdl->addComplexType(
    'entry_value',
	'complexType',
   	 'struct',
   	 'all',
  	  '',
		array(
        	'id'=>array('name'=>'id', 'type'=>'xsd:string'),
			'module_name'=>array('name'=>'module_name', 'type'=>'xsd:string'),
			'name_value_list'=>array('name'=>'name_value_list', 'type'=>'tns:name_value_list'),
		)
);

/*$server->wsdl->addComplexType(
    'entry_value',
	'complexType',
   	 'struct',
   	 'all',
  	  '',
		array(
        	'id'=>array('name'=>'id', 'type'=>'xsd:string'),
			'module_name'=>array('name'=>'module_name', 'type'=>'xsd:string'),
			'name_value_list'=>array('name'=>'name_value_list', 'type'=>'tns:name_value_list'),
		)
);*/

$server->wsdl->addComplexType(
    'field',
	'complexType',
   	 'struct',
   	 'all',
  	  '',
		array(
			'name'=>array('name'=>'name', 'type'=>'xsd:string'),
			'type'=>array('name'=>'type', 'type'=>'xsd:string'),
			'required'=>array('name'=>'required', 'type'=>'xsd:int'),
            'default_value'=>array('name'=>'name', 'type'=>'xsd:string'),
		)
);

//these are just a list of fields we want to get
$server->wsdl->addComplexType(
    'select_fields',
	'complexType',
   	 'array',
   	 '',
  	  'SOAP-ENC:Array',
	array(),
    array(
        array('ref'=>'SOAP-ENC:arrayType', 'wsdl:arrayType'=>'xsd:string[]')
    ),
	'xsd:string'
);

$server->wsdl->addComplexType(
   	 'user_auth',
   	 'complexType',
   	 'struct',
   	 'all',
  	  '',
	array(
		'user_name'=>array('name'=>'user_name', 'type'=>'xsd:string'),
		'password' => array('name'=>'password', 'type'=>'xsd:string')
	)

);


//insert report (Evgeny Popov)=====================================================================================
$server->wsdl->addComplexType(
	'insert_log',
	'complexType',
	'struct',
	'all',
	'',
       array(
    	      'id_task'=>array('name'=>'id_task', 'type'=>'xsd:string'),
    	      'task_log_name'=>array('name'=>'task_log_name','type'=>'xsd:string'),
    	      'description'=>array('name'=>'decription', 'type'=>'xsd:string'),
    	      'task_log_creator'=>array('name'=>'task_log_creator', 'type'=>'xsd:string'),
    	      'task_log_date'=>array('name'=>'task_log_date','type'=>'xsd:string')
       )
);

$server->wsdl->addComplexType(
	'insert_log_return',
	'complexType',
	'struct',
	'all',
	'',
	array(
	      'id'=>array('name'=>'id','type'=>'xsd:string'),
	      'result'=>array('name'=>'result','type'=>'xsd:string')
	)
);
//===================================================================================================================

//GetUserList (Evgeny Popov)=====================================================================================
$server->wsdl->addComplexType(
   	 'user_list',
   	 'complexType',
   	 'struct',
   	 'all',
  	  '',
	array(
		'name_value_list'=>array('name'=>'name_value_list', 'type'=>'tns:name_value_list_users'),
		'error' => array('name' =>'error', 'type'=>'xsd:string')
	)
);

//===================================================================================================================

//Get Last Task (Evgeny Popov)=======================================================================================
$server->wsdl->addComplexType(
	'task_ID',
	'complexType',
	'struct',
	'all',
	'',
	array(
		'ID_task'=>array('name'=>'ID_task','type'=>'xsd:string')
	)
);

$server->wsdl->addComplexType(
	'last_log_data',
	'complexType',
	'struct',
	'all',
	'',
	array(
		'task_log_name'=>array('name'=>'task_log_name','type'=>'xsd:string'),
		'description'=>array('name'=>'description','type'=>'xsd:string'),
		'task_log_creator'=>array('name'=>'task_log_creator','type'=>'xsd:string'),
		'task_log_date'=>array('name'=>'task_log_date','type'=>'xsd:string'),
		'error_code'=>array('name'=>'error_code','type'=>'xsd:string')
	)
);
//===================================================================================================================
//Insert People Resources (Evgeny Popov)=============================================================================
//===================================================================================================================
$server->wsdl->addComplexType(
        'insert_People',
        'complexType',
        'struct',
        'all',
        '',
        array(
                'login_name'=>array('name'=>'login_name','type'=>'xsd:string'),
                'task_id'=>array('name'=>'task_id','type'=>'xsd:string')
        )
);

$server->wsdl->addComplexType(
       'result_insert_People',
       'complexType',
       'struct',
       'all',
       '',
       array(
                'result'=>array('name'=>'result','type'=>'xsd:string')
       )
);
//===================================================================================================================


$server->wsdl->addComplexType(
   	 'get_entry_list_result',
   	 'complexType',
   	 'struct',
   	 'all',
  	  '',
	array(
		'result_count' => array('name'=>'result_count', 'type'=>'xsd:int'),
		'next_offset' => array('name'=>'next_offset', 'type'=>'xsd:int'),
		'field_list'=>array('name'=>'field_list', 'type'=>'tns:field_list'),
		'entry_list' => array('name' =>'entry_list', 'type'=>'tns:entry_list'),
		'error' => array('name' =>'error', 'type'=>'tns:error_value'),
	)
);

//these are just a list of fields we want to get
$server->wsdl->addComplexType(
    'module_fields',
	'complexType',
   	 'struct',
   	 'all',
  	  '',
		array(
        	'module_name'=>array('name'=>'module_name', 'type'=>'xsd:string'),
			'module_fields'=>array('name'=>'module_fields', 'type'=>'tns:field_list'),
			'error' => array('name' =>'error', 'type'=>'tns:error_value'),
		)
);

?>
