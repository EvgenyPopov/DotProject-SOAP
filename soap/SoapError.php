<?php
require_once('soap/SoapErrorDefinitions.php');

class SoapError{
	var $name;
	var $number;
	var $description;
	
	function SoapError(){
		$this->set_error('no_error');
	}

	function set_error($error_name){
		global $error_defs;
		if(!isset($error_defs[$error_name])){
			$this->name = 'An Undefined Error - ' . $error_name . ' occured';
			$this->number = '-1';
			$this->description = 'There is no error definition for ' . 	$error_name;
		}else{
			$this->name = $error_defs[$error_name]['name'];
			$this->number = $error_defs[$error_name]['number'];
			$this->description = $error_defs[$error_name]['description'];
		} 	
	}

	function get_soap_array(){
		return Array('number'=>$this->number,
					 'name'=>$this->name,
					 'description'=>$this->description);
		
	}
}

?>
