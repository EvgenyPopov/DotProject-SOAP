<?php


function filter_field_list(&$field_list, &$select_fields, $module_name){
	for($sug = 0; $sug < sizeof($field_list) ; $sug++){

		if(!empty($select_fields) && is_array($select_fields)){
			foreach($field_list as $name=>$value)
					if(!in_array($value['name'], $select_fields)){
						unset($field_list[$name]);
					}
		}
	}
	return $field_list;
}


function filter_return_list(&$output_list, $select_fields, $module_name){

	for($sug = 0; $sug < sizeof($output_list) ; $sug++){

		if( !empty($output_list[$sug]['name_value_list']) && is_array($output_list[$sug]['name_value_list']) && !empty($select_fields) && is_array($select_fields)){
			foreach($output_list[$sug]['name_value_list'] as $name=>$value)
					if(!in_array($value['name'], $select_fields)){
						unset($output_list[$sug]['name_value_list'][$name]);
						//unset($output_list[$sug]['field_list'][$name]);
					}
		}
	}
	return $output_list;
}





function get_return_value(&$value, $module){
	$object_id = rtrim($module,'s').'_id';	
	return Array('id'=>$value->$object_id,
				'module_name'=> $module,
				'name_value_list'=>get_name_value_list($value)
				);
}

function get_return_value2(&$value, $module){
	$object_id = rtrim($module,'s').'_id';	
	return Array('id'=>$value->$object_id,
				'module_name'=> $module,
				'name_value_list'=>get_name_value_list2($value)
				);
}







function get_name_value_list2($value){
	$list = array();
	$field_defs =$_SESSION['field_defs'];
	if(!empty($field_defs)){
		
		foreach($field_defs as $var){
			$tmp=$var->name;
			if(isset($value[$tmp])){
				$val = $value[$tmp];
				$list[] = get_name_value($tmp, $val);
			}
		}
	}
	return $list;

}
function get_name_value_list($value){
	$list = array();
	$field_defs =$_SESSION['field_defs'];
	if(!empty($field_defs)){
		
		foreach($field_defs as $var){
			$tmp=$var->name;
			if(isset($value->$tmp)){
				$val = $value->$tmp;
				$list[] = get_name_value($tmp, $val);
			}
		}
	}
	return $list;

}


function get_name_value3($field,$value,$email){
	return array('name'=>$field, 'value'=>$value, 'email'=>$email);
}

function get_name_value($field,$value){
	return array('name'=>$field, 'value'=>$value);
}


function get_field_list(&$value){
	$list = array();
	$field_defs =$value;
	
	if(!empty($field_defs)){

		foreach($field_defs as $var){
			
            $entry = array();
            $entry['name'] = $var->name;
            $entry['type'] = $var->type;
            
            if ($var->not_null)$required=1;
            else $required=0;
            
            $entry['required'] = $required;
            
			if($var->has_default) {
			   $entry['default_value'] = $var->default_value;
			}else $entry['default_value']=-1;
			$list[] = $entry;
		} 
	}
	return $list;
}


?>
