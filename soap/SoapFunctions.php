<?php
require_once('soap/SoapTypes.php');
require_once('soap/SoapHelperFunctions.php');
require_once('soap/SoapError.php');	

require_once ('base.php');
require_once DP_BASE_DIR . '/includes/config.php';

require_once DP_BASE_DIR.'/includes/main_functions.php';
require_once DP_BASE_DIR.'/includes/db_adodb.php';
require_once DP_BASE_DIR.'/includes/db_connect.php';

require_once DP_BASE_DIR.'/classes/ui.class.php';
require_once DP_BASE_DIR.'/classes/permissions.class.php';
require_once DP_BASE_DIR.'/includes/session.php';

//=====================================================================================================
//function GetUserList (insert people in task DotProject - Evgeny Popov)
//=====================================================================================================
$server->register(
	'GetUserList',
	array('session'=>'xsd:string'),
	array('return'=>'tns:user_list'),
	$NAMESPACE);
	
function GetUserList($session)
{
 global $db;
 $count = 1;
 
 if(!validate_authenticated($session))
 {
   return array('name_value_list'=>$output_query,'error'=>'Error: -1: User not found');
 }
 $query = "select contact_last_name,contact_first_name,user_username,contact_email from contacts,users where user_contact=contact_id";
 
 $ret=$db->Execute($query);
 if (empty($ret))
 {
   return array('name_value_list'=>$output_query,'error'=>'Error: -1: User not found'); 
 }
 $output_query = array();
 while ($row = $ret->fetchRow())
 {
   $output_query[]=get_name_value3($row['contact_last_name'].' '.$row['contact_first_name'],$row['user_username'],$row['contact_email']);
 }
 
 return array('name_value_list'=>$output_query,'error'=>'0: Fine');
}	


//=====================================================================================================

//=====================================================================================================
//function insert_PeopleRes (insert people in task DotProject - Evgeny Popov)
//=====================================================================================================
$server->register(
        'insert_PeopleRes',
        array('session'=>'xsd:string','insert_People'=>'tns:insert_People'),
        array('return'=>'tns:result_insert_People'),
        $NAMESPACE);

function insert_PeopleRes($session,$insert_People)
{
 global $db;
 if(!validate_authenticated($session))
 {
  return array('result'=>'-1: Invalid Session');
 }

 $taskID=$insert_People["task_id"];
 if($taskID=="")
 {
  return array('result'=>'-1:ID task is Empty');
 }

 $LoginName=$insert_People["login_name"];
 if($LoginName=="")
 {
  return array('result'=>'-1:LoginName is Empty');
 }

 $query = "SELECT * FROM users where user_username='$LoginName'";
 $ret=$db->Execute($query);

 if (empty($ret))
 {
  return array('result'=>'-1: Error from Query');
 }

 $QueryResult = $ret->fetchRow();
 $user_id = $QueryResult['user_id'];

 if($user_id=="")
 {
  return array('result'=>'-1:User not found');
 }

 $query_insert = "INSERT INTO user_tasks (user_id,user_type,task_id,perc_assignment,user_task_priority) VALUES ('$user_id',0,'$taskID',100,0)";
 $ret_insert=$db->Execute($query_insert);

 if (empty($ret_insert))
 {
  return array('result'=>'-1: Error insert people');
 }

return array('result'=>'0: Sucess');
}



//=====================================================================================================
//function get_last_log (get last report - Evgeny Popov)
//=====================================================================================================
$server->register(
	'get_last_log',
	array('session'=>'xsd:string','task_ID'=>'tns:task_ID'),
	array('return'=>'tns:last_log_data'),
	$NAMESPACE);

function get_last_log($session,$task_ID)
{
 global $db;
 if(!validate_authenticated($session))
 {
   return array('error_code'=>'-1: Error user session');
 }
 $taskID = $task_ID["ID_task"];

 if($taskID == "")
 {
   return array('error_code'=>"-1: ID task is empty");
 }

 $query = "SELECT * FROM task_log WHERE task_log_task='$taskID' ORDER BY task_log_date DESC LIMIT 1";
 $ret =$db->Execute($query);

 if (empty($ret))
 {
  return array('error_code'=>'-1: Error from Query'); 
 }

 $QueryResult = $ret->fetchRow();
 $user_id = $QueryResult['task_log_creator'];
 $GetUserQuery = "SELECT contact_first_name, contact_last_name from users, contacts where users.user_id='$user_id' and users.user_contact=contacts.contact_id";
 $RetUser = $db->Execute($GetUserQuery);
 if (empty($RetUser))
 {
  return array('id'=>'-1: Error from Query'); 
 }

 $QueryResultUser = $RetUser->fetchRow();
 $FirstName = $QueryResultUser['contact_first_name'];
 $LastName = $QueryResultUser['contact_last_name'];
 $UserName = $FirstName.' '.$LastName;
 
 return array('error_code'=>'0','task_log_name'=>$QueryResult['task_log_name'],'description'=>$QueryResult['task_log_description'],'task_log_date'=>$QueryResult['task_log_date'],'task_log_creator'=>$UserName);
}
//=====================================================================================================


//=====================================================================================================
//function set_log (create report - Evgeny Popov)
//=====================================================================================================
$server->register(
	'set_log',
	array('session'=>'xsd:string','insert_log'=>'tns:insert_log'),
	array('return'=>'tns:insert_log_return'),
	$NAMESPACE);
	
function set_log($session,$insert_log)
{
 global $db;
 if(!validate_authenticated($session))
 {
  return array('id'=>'-1','result'=>'Error user session');
 }

 $idTask = $insert_log["id_task"];
 if ($idTask == "")
 {
  return array('id'=>'-1','result'=>'ID Task is Empty');
 } 
 
 $taskLogName = $insert_log["task_log_name"];
 if ($taskLogName == "")
 {
  return array('id'=>'-1','result'=>'Field task_log_name is empty');
 }
 
 $Descr = $insert_log["description"];
 if ($Descr == "")
 {
  return array('id'=>'-1','result'=>'Description is empty');
 }
 
 $TaskLogDate = $insert_log["task_log_date"];
 if($TaskLogDate == "")
 {
  return array('id'=>'-1','result'=>'Date is empty');
 }
 
 $taskLogCreator = $insert_log["task_log_creator"];
 
 $query = "INSERT INTO task_log  (task_log_task,task_log_name,task_log_description,task_log_creator,task_log_date) VALUES ('$idTask','$taskLogName','$Descr','$taskLogCreator','$TaskLogDate')";
 $ret=$db->Execute($query);

 if (empty($ret))
 {
  return array('id'=>'-1','result'=>'Error from Query'); 
 }
 
 return array('id'=>$ret,'result'=>'Report is created');
}	
//======================================================================================================








	

//======================================================================================================
//function login
//======================================================================================================
$server->register(
        'login',
        array('user_auth'=>'tns:user_auth', 'application_name'=>'xsd:string'),
        array('return'=>'tns:set_entry_result'),
        $NAMESPACE);

/**
 * Log the user into the application
 *
 * @param UserAuth array $user_auth -- Set user_name and password (password needs to be
 *      in the right encoding for the type of authentication the user is setup for.  
 * @param String $application -- The name of the application you are logging in from.  (Currently unused).
 * @return Array(session_id, error) -- session_id is the id of the session that was
 *      created.  Error is set if there was any error during creation.
 */
function login($user_auth, $application='test'){
    	
    $error = new SoapError();
	$success = false;
	$user_auth["user_name"]=addslashes($user_auth["user_name"]);
	$user_auth["password"]=addslashes($user_auth["password"]);
	$_POST['login'] = 'login';
	$_REQUEST['login'] = 'login';
	
	if(isset($user_auth["user_name"])||isset($user_auth["password"]))
	{
		dPsessionStart(array('AppUI'));
		$AppUI = new CAppUI;
		$ok = $AppUI->login( $user_auth["user_name"], $user_auth["password"] );
		if (!$ok) {

			$error->set_error('invalid_login');
			return  array('id'=>-1, 'error'=>$error->get_soap_array());

			$AppUI->setMsg( 'Login Failed');
		} else {
			//Register login in user_acces_log
			$AppUI->registerLogin();
			addHistory('login', $AppUI->user_id, 'login', $AppUI->user_first_name . ' ' . $AppUI->user_last_name);
			$_SESSION['AppUI'] = $AppUI;

			$success = true;
		}
	}

	if($success){
		$_SESSION['is_valid_session']= true;
		$_SESSION['type'] = 'user';

		return array('id'=>session_id(), 'error'=>$error);
	}

	$error->set_error('invalid_login');
	return array('id'=>-1, 'error'=>$error);
}


//======================================================================================================






//======================================================================================================
//function get_entry_list
//======================================================================================================

$server->register(
    'get_entry_list',
    array('session'=>'xsd:string', 'module_name'=>'xsd:string', 'query'=>'xsd:string', 'order_by'=>'xsd:string','offset'=>'xsd:int', 'select_fields'=>'tns:select_fields', 'max_results'=>'xsd:int'),
    array('return'=>'tns:get_entry_list_result'),
    $NAMESPACE);

/**
 * Retrieve a list of entry.
 *
 * @param String $session -- Session ID returned by a previous call to login.
 * @param String $module_name -- The name of the module to return records from.  This name should be the name the module was developed under (changing a tab name is studio does not affect the name that should be passed into this method)..
 * @param String $query -- SQL where clause without the word 'where'
 * @param String $order_by -- SQL order by clause without the phrase 'order by'
 * @param String $offset -- The record offset to start from.
 * @param Array  $select_fields -- A list of the fields to be included in the results. This optional parameter allows for only needed fields to be retrieved.
 * @param String $max_results -- The maximum number of records to return.  The default is the sugar configuration value for 'list_max_entries_per_page'
 * @return Array 'result_count' -- The number of records returned
 *               'next_offset' -- The start of the next page (This will always be the previous offset plus the number of rows returned.  It does not indicate if there is additional data unless you calculate that the next_offset happens to be closer than it should be.
 *               'field_list' -- The vardef information on the selected fields.
 *                      Array -- 'field'=>  'name' -- the name of the field
 *                                          'type' -- the data type of the field
 *                                          'required' -- Is the field required?
 *                                          'default' -- default value
 *               'entry_list' -- The records that were retrieved
 *               'error' -- The SOAP error, if any
 */
function get_entry_list($session, $module_name, $query, $order_by,$offset, $select_fields, $max_results){
	global $db;
	require_once DP_BASE_DIR.'/includes/permissions.php';

	$error = new SoapError();
	if(!validate_authenticated($session)){
		$error->set_error('invalid_session');
		return array('result_count'=>-1, 'entry_list'=>array(), 'error'=>$error->get_soap_array());
	}

	$AppUI = & $_SESSION['AppUI'];
	$GLOBALS['AppUI'] = $AppUI;
	
	
	$modclass = $AppUI->getModuleClass($module_name);

	if (file_exists($modclass))
		include_once( $modclass );
	else{
		$error->set_error('no_module');
		return array('result_count'=>-1, 'entry_list'=>array(), 'error'=>$error->get_soap_array());
	}
	
	$perms =& $AppUI->acl();
	$canAccess = $perms->checkModule($module_name, 'access');
	$canRead = $perms->checkModule($module_name, 'view');
	$canEdit = $perms->checkModule($module_name, 'edit');
	$canAuthor = $perms->checkModule($module_name, 'add');
	$canDelete = $perms->checkModule($module_name, 'delete');
	$GLOBALS['perms'] = $perms;

	if(!$canRead){
		$error->set_error('no_access');
		return array('result_count'=>-1, 'entry_list'=>array(), 'error'=>$error->get_soap_array());
	}
	
	$class_name= 'C'.ucfirst(rtrim($module_name,'s'));
	
	$object_name = rtrim($module_name,'s');	
	
	if ($module_name == "companies")
	{
         $class_name = "CCompany";
	}
	
	$seed = new $class_name();
	
	$field_defs = $db->MetaColumns($module_name);
	$_SESSION['field_defs'] = $field_defs;
	
	$seed->_query->clear();
	
	$seed->_query->addTable($seed->_tbl);
	if ($order_by) {
		$seed->_query->addOrder($order_by);
	}
	if ($query) {
		$seed->_query->addWhere($query);
	}
	if (isset($offset)){
		$seed->_query->setLimit($max_results,$offset);
	}
		
	$sql = $seed->_query->prepare();

	$list = $seed->_query->loadHashList($object_name.'_id');
	
	$seed->_query->clear();
	$output_list = array();

	$field_list = array();
	foreach($list as $value)
	{
		
		$output_list[] = get_return_value2($value, $module_name);
		if(empty($field_list)){
			$field_list = get_field_list($field_defs);
		}
	}

	// Filter the search results to only include the requested fields.
	$output_list = filter_return_list($output_list, $select_fields, $module_name);

	// Filter the list of fields to only include information on the requested fields.
	$field_list = filter_field_list($field_list,$select_fields, $module_name);

	// Calculate the offset for the start of the next page
	$next_offset = $offset + sizeof($output_list);

	return array('result_count'=>sizeof($output_list), 'next_offset'=>$next_offset,'field_list'=>$field_list, 'entry_list'=>$output_list, 'error'=>$error->get_soap_array());
}

//======================================================================================================






//======================================================================================================
// function get_entries
//======================================================================================================
$server->register(
    'get_entries',
    array('session'=>'xsd:string', 'module_name'=>'xsd:string', 'ids'=>'tns:select_fields', 'select_fields'=>'tns:select_fields'),
    array('return'=>'tns:get_entry_result'),
    $NAMESPACE);

/**
 * Retrieve a list of object based on provided IDs.
 *
 * @param String $session -- Session ID returned by a previous call to login.
 * @param String $module_name -- The name of the module to return records from.  This name should be the name the module was developed under (changing a tab name is studio does not affect the name that should be passed into this method)..
 * @param Array $ids -- An array of  IDs.
 * @param Array $select_fields -- A list of the fields to be included in the results. This optional parameter allows for only needed fields to be retrieved.
 * @return Array 'field_list' -- Var def information about the returned fields
 *               'entry_list' -- The records that were retrieved
 *               'error' -- The SOAP error, if any
 */
function get_entries($session, $module_name, $ids,$select_fields = null ){
	global $db;
	$error = new SoapError();
	$field_list = array();
	$output_list = array();

	if(!validate_authenticated($session)){
		$error->set_error('invalid_session');
		return array('field_list'=>$field_list, 'entry_list'=>array(), 'error'=>$error->get_soap_array());
	}
    $AppUI = & $_SESSION['AppUI'];
	$GLOBALS['AppUI'] = $AppUI;
	
	$modclass = $AppUI->getModuleClass($module_name);
	if (file_exists($modclass))
		include_once( $modclass );
	else{
		$error->set_error('no_module');
		return array('field_list'=>$field_list, 'entry_list'=>array(), 'error'=>$error->get_soap_array());
	}
	
	$perms =& $AppUI->acl();
	$canAccess = $perms->checkModule($module_name, 'access');
	$canRead = $perms->checkModule($module_name, 'view');
	$canEdit = $perms->checkModule($module_name, 'edit');
	$canAuthor = $perms->checkModule($module_name, 'add');
	$canDelete = $perms->checkModule($module_name, 'delete');
	$GLOBALS['perms'] = $perms;

	if(!$canRead){
		$error->set_error('no_access');
		return array('field_list'=>$field_list, 'entry_list'=>array(), 'error'=>$error->get_soap_array());
	}
	
	$field_defs = $db->MetaColumns($module_name);
	$_SESSION['field_defs'] = $field_defs;
		
	$class_name= 'C'.ucfirst(rtrim($module_name,'s'));	
	foreach($ids as $id){
		$seed = new $class_name();	
		$seed->load($id);
		$output_list[] = get_return_value($seed, $module_name);
	}
		
	if(empty($field_list)){
		$field_list = get_field_list($field_defs);
	}

	$output_list = filter_return_list($output_list, $select_fields, $module_name);
	$field_list = filter_field_list($field_list,$select_fields, $module_name);

	return array( 'field_list'=>$field_list, 'entry_list'=>$output_list, 'error'=>$error->get_soap_array());
}

//======================================================================================================




//======================================================================================================
// function set_entry
//======================================================================================================
$server->register(
    'set_entry',
    array('session'=>'xsd:string', 'module_name'=>'xsd:string',  'name_value_list'=>'tns:name_value_list'),
    array('return'=>'tns:set_entry_result'),
    $NAMESPACE);

/**
 * Update or create entry
 *
 * @param String $session -- Session ID returned by a previous call to login.
 * @param String $module_name -- The name of the module to return records from.  This name should be the name the module was developed under (changing a tab name is studio does not affect the name that should be passed into this method)..
 * @param Array $name_value_list -- The keys of the array are the entry attributes, the values of the array are the values the attributes should have.
 * @return Array    'id' -- the ID of the bean that was written to (-1 on error)
 *                  'error' -- The SOAP error if any.
 */
function set_entry($session,$module_name, $name_value_list){
	$error = new SoapError();


	//require_once ('base.php');
	//require_once DP_BASE_DIR . '/includes/config.php';
	
	//require_once DP_BASE_DIR.'/includes/main_functions.php';
	//require_once DP_BASE_DIR.'/includes/db_adodb.php';
	//require_once DP_BASE_DIR.'/includes/db_connect.php';
	
	//require_once DP_BASE_DIR.'/classes/ui.class.php';
	//require_once DP_BASE_DIR.'/classes/permissions.class.php';
	//require_once DP_BASE_DIR.'/includes/session.php';

	//require_once DP_BASE_DIR.'/includes/permissions.php';
    //$error->description.='DP_BASE_DIR = '.DP_BASE_DIR.'; ';
	
	//return array('id'=>-1, 'error'=>$error->get_soap_array());
	//	return array('id'=>-1, 'error'=>$error->get_soap_array());
	if(!validate_authenticated($session)){
		$error->set_error('invalid_session');
		return array('id'=>-1, 'error'=>$error->get_soap_array());
	}
	//$error->description.= 'and call validate_authenticated; ';
	
	///dpSessionStart(array('AppUI'));

	//$error->description.= 'and  call dpSessionStart; ';	
	
	$AppUI = & $_SESSION['AppUI'];
	//if (empty($AppUI)){
	//	$error->description.= 'AppUI is empty; ';
	//	return array('id'=>-1, 'error'=>$error->get_soap_array());
	//}
	//if (!isset($AppUI)) {
	//	$error->description = '�� ���������� ������� AppUI';
	//	return array('id'=>-1, 'error'=>$error->get_soap_array());
	//	}
	$GLOBALS['AppUI'] = $AppUI;
	
	//$error->description.= 'user_id='.$AppUI->user_id.'; ';	
	$perms =& $AppUI->acl();
	//$error->description.= 'perms  created; ';
	//return array('id'=>-1, 'error'=>$error->get_soap_array());


	$canAccess = $perms->checkModule($module_name, 'access',$AppUI->user_id);
	$canRead = $perms->checkModule($module_name, 'view',$AppUI->user_id);
	$canEdit = $perms->checkModule($module_name, 'edit',$AppUI->user_id);
	$canAuthor = $perms->checkModule($module_name, 'add',$AppUI->user_id);
	$canDelete = $perms->checkModule($module_name, 'delete',$AppUI->user_id);
	$GLOBALS['perms'] = $perms;
	
		
	$modclass = $AppUI->getModuleClass($module_name);
	//$error->description.= 'CALL   getModuleClass; ';
	//$error->description.= 'CALL ModuleClass= '.$modclass.'; ';
	//return array('id'=>-1, 'error'=>$error->get_soap_array());
	if (file_exists($modclass))
		include_once( $modclass );
	else {
		$error->set_error('no_module');
		return array('id'=>-1, 'error'=>$error->get_soap_array());
	}
		
	$class_name= 'C'.ucfirst(rtrim($module_name,'s'));	
	$object_name = rtrim($module_name,'s');	
	$object_id= $object_name."_id";

	//$error->description.= "; \n"."object_name= ".$object_name."; \n";
	//$error->description.= 'class_name= '.$class_name."; \n";
	//$error->description.= 'object_id= '.$object_id."; \n";
	//return array('id'=>-1, 'error'=>$error->get_soap_array());

	$seed = new $class_name();
	//$seed_str=serialize($seed);
	//$error->description.= 'CREATE object '.$seed_str."; \n";
	//return array('id'=>-1, 'error'=>$error->get_soap_array());
	foreach($name_value_list as $value){
		if($value['name'] == $object_id){
			$seed->load($value['value']);
			//$seed_str=serialize($seed);
			//$error->description.= "; \n".'Load '.$seed_str."; \n";
			break;
		}
	}
	foreach($name_value_list as $value){
       
		$seed->$value['name'] = $value['value'];
		//$error->description.= $value['name']." =".$seed->$value['name']."; \n";
	}
	//return array('id'=>-1, 'error'=>$error->get_soap_array());
	if(!$canEdit && !$canDelete &&!$canAuthor)
	{
		$error->set_error('no_access');
		return array('id'=>-1, 'error'=>$error->get_soap_array());
	}

	$seed->store();
	//$msg = implode("\n",$msg_arr);
	//$error->description.= 'Store '.$msg."; \n";
	//$object_id= $object_name."_id";
	return array('id'=>$seed->$object_id, 'error'=>$error->get_soap_array());

}


//======================================================================================================
//======================================================================================================
// set_file_attachment
//======================================================================================================

$server->register(
  'set_file_attachment',
   array('session'=>'xsd:string', 'file'=>'tns:file_attachment'),
   array('return'=>'tns:set_entry_result'),
   $NAMESPACE);


function set_file_attachment($session,$file)
{
  global $db;
  require_once('soap/config_upload.php');
 // require_once ('base.php');
  $error = new SoapError();

  if(!validate_authenticated($session)){
    $error->set_error('invalid_login');
    return array('id'=>-1, 'error'=>$error->get_soap_array());
  }

  $AppUI = & $_SESSION['AppUI'];
  $GLOBALS['AppUI'] = $AppUI;
  $module_name='files';

  $perms =& $AppUI->acl();
  $canAccess = $perms->checkModule($module_name, 'access'); 
  $canAuthor = $perms->checkModule($module_name, 'add');
  $GLOBALS['perms'] = $perms;

  if(!$canAccess || !$canAuthor){
    $error->set_error('no_access');
    return array('id'=>-1, 'error'=>$error->get_soap_array());
  }

  $modclass = $AppUI->getModuleClass($module_name);
  if (file_exists($modclass))
    include_once( $modclass );
  else{
    $error->set_error('no_module');
    return array('id'=>-1, 'error'=>$error->get_soap_array());
  }

  $module_name = 'tasks';
  $modclass = $AppUI->getModuleClass($module_name);
  if (file_exists($modclass))
     include_once( $modclass );
  else{
     $error->set_error('no_module');
     return array('id'=>-1, 'error'=>$error->get_soap_array());
  }

  $focus = new CFile();
  $task = new CTask();
  $task->load($file['task_id']);
/// $error->description.=$file['location'];
  
  //$file['location'] = base64_decode($file['location']);
  //$file['filename'] = base64_decode($file['filename']);
  /*if (filesize($file['location'] > $config_upload['upload_maxsize']){
     $error->set_error('no_file');
     return array('id'=>-1, 'error'=>$error->get_soap_array());
  }*/

  $file_real_filename =uniqid(rand());
  $new_location= DP_BASE_DIR.'/files/'.$task->task_project.'/'.$file_real_filename;
 /// $error->description.=$new_location;
  if(!is_dir(DP_BASE_DIR.'/files/'.$task->task_project))
  {
   mkdir(DP_BASE_DIR.'/files/'.$task->task_project);
  }
  copy($file['location'],$new_location);


  if (file_exists($new_location)){
//  return array('id'=>$new_location, 'error'=>$error->get_soap_array());
  
    if(!empty($file['filename'])){
      $upload_filename=$file['filename'];

      $ext_pos = strrpos($upload_filename, ".");
      $file_ext = substr($upload_filename, $ext_pos + 1);
     // $error->description.="file_ext: ".$file_ext;
     // return array('id'=>-1, 'error'=>$error->get_soap_array());
     /* if (in_array($file_ext, $config_upload['upload_badext'])) {
         $upload_filename .= ".txt";
         $file_ext = "txt";
      }*/


      $focus->file_name = $upload_filename;
      $focus->file_owner = $AppUI->user_id;
      // $error->description.="file_owner_id: ".$AppUI->user_id;
      $focus->file_real_filename = $file_real_filename;
      $focus->file_project = $task->task_project;
      $focus->file_date =str_replace("'", '', $db->DBTimeStamp(time()));
      // $error->description.="file_task_id: ".$task->task_id;
      $focus->file_task =$task->task_id;
      $focus->file_folder =0;
      $focus->file_size =filesize($new_location);
      $focus->file_parent = 0;
      $focus->file_folder = 0;
      $focus->file_version = 1;
      $focus->file_version_id = getNextVersionID();
      $focus->file_category = 1;
      $focus->file_type = ext2mime($file_ext);
      $focus->store();
    //  $error->description.="file_file: ".$focus->file_type;
       

    }else{
      $error->set_error('no_file');
      return array('id'=>-1, 'error'=>$error->get_soap_array());
    }
  }else{
    $error->set_error('no_file');
    return array('id'=>-1, 'error'=>$error->get_soap_array());
  }
  return array('id'=>$focus->file_id, 'error'=>$error->get_soap_array());
}
//======================================================================================================

//======================================================================================================
// function logout
//======================================================================================================
$server->register(
        'logout',
        array('session'=>'xsd:string'),
        array('return'=>'tns:error_value'),
        $NAMESPACE);

/**
 * Log out of the session.  This will destroy the session and prevent other's from using it.
 *
 * @param String $session -- Session ID returned by a previous call to login.
 * @return Empty error on success, Error on failure
 */
function logout($session){
	$error = new SoapError();
	
	if(validate_authenticated($session)){
		session_destroy();
		return $error->get_soap_array();
	}
	$error->set_error('no_session');
	return $error->get_soap_array();
}

//======================================================================================================

//======================================================================================================
// get_module_fields
//======================================================================================================
$server->register(
    'get_module_fields',
    array('session'=>'xsd:string', 'module_name'=>'xsd:string'),
    array('return'=>'tns:module_fields'),
    $NAMESPACE);

/**
 * Retrieve vardef information on the fields of the specified bean.
 *
 * @param String $session -- Session ID returned by a previous call to login.
 * @param String $module_name -- The name of the module to return records from.  This name should be the name the module was developed under (changing a tab name is studio does not affect the name that should be passed into this method)..
 * @return Array    'module_fields' -- The vardef information on the selected fields.
 *                  'error' -- The SOAP error, if any
 */
function get_module_fields($session, $module_name){
	global $db;
	$error = new SoapError();
	$module_fields = array();
	
	if(!validate_authenticated($session)){
		$error->set_error('invalid_session');
		return array('module_name'=>$module_name,'module_fields'=>$module_fields, 'error'=>$error->get_soap_array());
	}
	
	$AppUI = & $_SESSION['AppUI'];
	$GLOBALS['AppUI'] = $AppUI;
	
	$modclass = $AppUI->getModuleClass($module_name);
	if (file_exists($modclass))
		include_once( $modclass );
	else{
		$error->set_error('no_module');
		return array('module_name'=>$module_name,'module_fields'=>$module_fields, 'error'=>$error->get_soap_array());
	}
	
	$perms =& $AppUI->acl();
	$canAccess = $perms->checkModule($module_name, 'access');
	$canRead = $perms->checkModule($module_name, 'view');
	$canEdit = $perms->checkModule($module_name, 'edit');
	$canAuthor = $perms->checkModule($module_name, 'add');
	$canDelete = $perms->checkModule($module_name, 'delete');
	$GLOBALS['perms'] = $perms;

	if(!$canRead){
		$error->set_error('no_access');
		return array('module_name'=>$module_name,'module_fields'=>$module_fields, 'error'=>$error->get_soap_array());
	}
	
	
	$module_fields = $db->MetaColumns($module_name);
	
	if (empty($module_fields)){
		$error->set_error('no_records');
		return array('module_name'=>$module_name,'module_fields'=>$module_fields, 'error'=>$error->get_soap_array());
	}
	
	return Array('module_name'=>$module_name,
				'module_fields'=> get_field_list($module_fields),
				'error'=>$error->get_soap_array());
}

$server->register(
    'test',
    array('string'=>'xsd:string'),
    array('return'=>'xsd:string'),
    $NAMESPACE);

/**
 * A simple test method that returns the string you pass into it.  It is convenient for
 * verifying connectivity and server availability.
 *
 * @param String $string -- An arbirtray string that will be returned
 * @return String -- The string that you sent in.
 */
function test($string){
	require_once ('base.php');
	require_once DP_BASE_DIR . '/includes/config.php';
	global $baseDir;
	//require_once DP_BASE_DIR.'/includes/main_functions.php';
	//require_once DP_BASE_DIR.'/includes/db_adodb.php';
	//require_once DP_BASE_DIR.'/includes/db_connect.php';

	//require_once DP_BASE_DIR.'/classes/ui.class.php';
	//require_once DP_BASE_DIR.'/classes/permissions.class.php';
	require_once DP_BASE_DIR.'/includes/session.php';

    if(!empty($string)){
		//session_id($session_id);
		//$error->description = "dpSessionStart";
		//return array('id'=>-1, 'error'=>$error->get_soap_array());
		//return $string ='call dpSessionStart()';
		dpSessionStart(array('AppUI'));
	
		if(!empty($_SESSION['is_valid_session']) && $_SESSION['type'] == 'user' && isset($_SESSION['AppUI']))  {
			return $string ='true';
		}
	
		session_destroy();
	}
	return $string =$baseDir;




	return $string;
}
//======================================================================================================
function validate_authenticated($session_id){
	require_once ('base.php');
	require_once DP_BASE_DIR . '/includes/config.php';	
	require_once DP_BASE_DIR.'/includes/session.php';

	if(!empty($session_id)){
		session_id($session_id);
		dpSessionStart(array('AppUI'));
	
		if(!empty($_SESSION['is_valid_session']) && $_SESSION['type'] == 'user' && isset($_SESSION['AppUI']))  {
			return true;
		}
	
		session_destroy();
	}
	return false;
}
//======================================================================================================


function ext2mime( $ext = '' ) {
  $file_ext = Array(
    '3dm' => 'x-world/x-3dmf',
    '3dmf' => 'x-world/x-3dmf',
    'a' => 'application/octet-stream',
    'aab' => 'application/x-authorware-bin',
    'aam' => 'application/x-authorware-map',
    'aas' => 'application/x-authorware-seg',
    'abc' => 'text/vnd.abc',
    'acgi' => 'text/html',
    'afl' => 'video/animaflex',
    'ai' => 'application/postscript',
    'aif' => 'audio/aiff',
    'aifc' => 'audio/aiff',
    'aiff' => 'audio/aiff',
    'aim' => 'application/x-aim',
    'aip' => 'text/x-audiosoft-intra',
    'ani' => 'application/x-navi-animation',
    'aos' => 'application/x-nokia-9000-communicator-add-on-software',
    'aps' => 'application/mime',
    'arc' => 'application/octet-stream',
    'arj' => 'application/arj',
    'art' => 'image/x-jg',
    'asf' => 'video/x-ms-asf',
    'asm' => 'text/x-asm',
    'asp' => 'text/asp',
    'asx' => 'application/x-mplayer2',
    'au' => 'audio/basic',
    'avi' => 'video/msvideo',
    'avs' => 'video/avs-video',
    'bcpio' => 'application/x-bcpio',
    'bin' => 'application/octet-stream',
    'bm' => 'image/bmp',
    'bmp' => 'image/bmp',
    'boo' => 'application/book',
    'book' => 'application/book',
    'boz' => 'application/x-bzip2',
    'bsh' => 'application/x-bsh',
    'bz' => 'application/x-bzip',
    'bz2' => 'application/x-bzip2',
    'c' => 'text/plain',
    'c++' => 'text/plain',
    'cat' => 'application/vnd.ms-pki.seccat',	
    'cc' => 'text/plain',
    'ccad' => 'application/clariscad',
    'cco' => 'application/x-cocoa',
    'cdf' => 'application/cdf',
    'cer' => 'application/pkix-cert',
    'cha' => 'application/x-chat',
    'chat' => 'application/x-chat',
    'class' => 'application/java',
    'com' => 'application/octet-stream',
    'conf' => 'text/plain',
    'cpio' => 'application/x-cpio',
    'cpp' => 'text/x-c',
    'cpt' => 'application/mac-compactpro',
    'crl' => 'application/pkcs-crl',
    'crt' => 'application/pkix-cert',
    'csh' => 'application/x-csh',
    'css' => 'text/css',
    'cxx' => 'text/plain',
    'dcr' => 'application/x-director',
    'deepv' => 'application/x-deepv',
    'def' => 'text/plain',
    'der' => 'application/x-x509-ca-cert',
    'dif' => 'video/x-dv',
    'dir' => 'application/x-director',
    'dl' => 'video/dl',
    'doc' => 'application/msword',
    'dot' => 'application/msword',	
    'dp' => 'application/commonground',
    'drw' => 'application/drafting',
    'dump' => 'application/octet-stream',	
    'dv' => 'video/x-dv',
    'dvi' => 'application/x-dvi',
    'dwf' => 'model/vnd.dwf',	
    'dwg' => 'application/acad',
    'dxf' => 'image/x-dwg',
    'dxr' => 'application/x-director',
    'el' => 'text/x-script.elisp',
    'elc' => 'application/x-elc',
    'eml' => 'message/rfc822',
    'env' => 'application/x-envoy',
    'eps' => 'application/postscript',	
    'es' => 'application/x-esrehber',
    'esp' => 'text/html',
    'etx' => 'text/x-setext',
    'evy' => 'application/envoy',
    'exe' => 'application/octet-stream',
    'f' => 'text/plain',
    'f77' => 'text/x-fortran',
    'f90' => 'text/plain',
    'f90' => 'text/x-fortran',
    'fdf' => 'application/vnd.fdf',
    'fif' => 'image/fif',
    'fli' => 'video/fli',
    'flo' => 'image/florian',
    'flx' => 'text/vnd.fmi.flexstor',
    'fmf' => 'video/x-atomic3d-feature',
    'for' => 'text/plain',
    'fpx' => 'image/vnd.fpx',
    'frl' => 'application/freeloader',
    'funk' => 'audio/make',
    'g' => 'text/plain',
    'g3' => 'image/g3fax',
    'gif' => 'image/gif',
    'gl' => 'video/gl',
    'gl' => 'video/x-gl',
    'gsd' => 'audio/x-gsm',
    'gsm' => 'audio/x-gsm',
    'gsp' => 'application/x-gsp',
    'gss' => 'application/x-gss',
    'gtar' => 'application/x-gtar',
    'gz' => 'application/x-gzip',
    'gzip' => 'application/x-gzip',
    'h' => 'text/plain',
    'hdf' => 'application/x-hdf',
    'help' => 'application/x-helpfile',
    'hgl' => 'application/vnd.hp-HPGL',
    'hh' => 'text/plain',
    'hlb' => 'text/x-script',
    'hlp' => 'application/hlp',
    'hpg' => 'application/vnd.hp-HPGL',	
    'hpgl' => 'application/vnd.hp-HPGL',
    'hqx' => 'application/binhex',
    'hta' => 'application/hta',
    'htc' => 'text/x-component',
    'htm' => 'text/html',
    'html' => 'text/html',
    'htmls' => 'text/html',
    'htt' => 'text/webviewhtml',
    'htx' => 'text/html',
    'ice' => 'x-conference/x-cooltalk',
    'ico' => 'image/x-icon',
    'idc' => 'text/plain',
    'ief' => 'image/ief',
    'iefs' => 'image/ief',
    'iges' => 'application/iges',
    'igs' => 'application/iges',
    'ima' => 'application/x-ima',
    'imap' => 'application/x-httpd-imap',
    'inf' => 'application/inf',	
    'ins' => 'application/x-internett-signup',
    'ip' => 'application/x-ip2',
    'isu' => 'video/x-isvideo',
    'it' => 'audio/it',	
    'iv' => 'application/x-inventor',
    'ivr' => 'i-world/i-vrml',
    'ivy' => 'application/x-livescreen',
    'jam' => 'audio/x-jam',	
    'jav' => 'text/plain',
    'java' => 'text/plain',
    'jcm' => 'application/x-java-commerce',
    'jfif' => 'image/jpeg',
    'jfif-tbnl' => 'image/jpeg',
    'jpe' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'jpg' => 'image/jpeg',
    'jps' => 'image/x-jps',
    'js' => 'application/x-javascript',
    'jsp' => 'text/html',
    'jut' => 'image/jutvision',
    'kar' => 'audio/midi',
    'ksh' => 'application/x-ksh',
    'la' => 'audio/nspaudio',
    'lam' => 'audio/x-liveaudio',
    'latex' => 'application/x-latex',
    'lha' => 'application/octet-stream',
    'lhx' => 'application/octet-stream',
    'list' => 'text/plain',
    'lma' => 'audio/nspaudio',
    'log' => 'text/plain',
    'lsp' => 'application/x-lisp',
    'lst' => 'text/plain',
    'lsx' => 'text/x-la-asf',
    'ltx' => 'application/x-latex',
    'lzh' => 'application/octet-stream',
    'lzx' => 'application/octet-stream',
    'm' => 'text/plain',
    'm' => 'text/x-m',
    'm1v' => 'video/mpeg',
    'm2a' => 'audio/mpeg',
    'm2v' => 'video/mpeg',
    'm3u' => 'audio/x-mpequrl',
    'man' => 'application/x-troff-man',
    'map' => 'application/x-navimap',
    'mar' => 'text/plain',
    'mbd' => 'application/mbedlet',
    'mc$' => 'application/x-magic-cap-package-1.0',
    'mcd' => 'application/mcad',
    'mcf' => 'text/mcf',
    'mcp' => 'application/netmc',
    'me' => 'application/x-troff-me',
    'mht' => 'message/rfc822',
    'mhtml' => 'message/rfc822',
    'mid' => 'audio/midi',
    'midi' => 'audio/midi',
    'mif' => 'application/x-frame',
    'mime' => 'message/rfc822',
    'mjf' => 'audio/x-vnd.AudioExplosion.MjuiceMediaFile',
    'mjpg' => 'video/x-motion-jpeg',
    'mm' => 'application/base64',
    'mme' => 'application/base64',
    'mod' => 'audio/mod',
    'moov' => 'video/quicktime',
    'mov' => 'video/quicktime',
    'movie' => 'video/x-sgi-movie',
    'mp2' => 'audio/mpeg',
    'mp3' => 'audio/mpeg3',
    'mpa' => 'audio/mpeg',
    'mpc' => 'application/x-project',
    'mpe' => 'video/mpeg',
    'mpeg' => 'video/mpeg',
    'mpg' => 'audio/mpeg',
    'mpga' => 'audio/mpeg',
    'mpp' => 'application/vnd.ms-project',
    'mpt' => 'application/x-project',
    'mpv' => 'application/x-project',
    'mpx' => 'application/x-project',
    'mrc' => 'application/marc',
    'ms' => 'application/x-troff-ms',
    'mv' => 'video/x-sgi-movie',	
    'my' => 'audio/make',
    'mzz' => 'application/x-vnd.AudioExplosion.mzz',
    'nap' => 'image/naplps',
    'naplps' => 'image/naplps',
    'nc' => 'application/x-netcdf',
    'ncm' => 'application/vnd.nokia.configuration-message',
    'nif' => 'image/x-niff',
    'niff' => 'image/x-niff',
    'nix' => 'application/x-mix-transfer',
    'nsc' => 'application/x-conference',
    'nvd' => 'application/x-navidoc',
    'o' => 'application/octet-stream',
    'oda' => 'application/oda',
    'omc' => 'application/x-omc',
    'omcd' => 'application/x-omcdatamaker',
    'omcr' => 'application/x-omcregerator',
    'p' => 'text/x-pascal',
    'p10' => 'application/pkcs10',
    'p12' => 'application/pkcs-12',
    'p7a' => 'application/x-pkcs7-signature',
    'p7c' => 'application/pkcs7-mime',
    'p7m' => 'application/pkcs7-mime',
    'p7r' => 'application/x-pkcs7-certreqresp',
    'p7s' => 'application/pkcs7-signature',
    'part' => 'application/pro_eng',
    'pas' => 'text/pascal',
    'pbm' => 'image/x-portable-bitmap',
    'pcl' => 'application/x-pcl',
    'pct' => 'image/x-pict',
    'pcx' => 'image/x-pcx',
    'pdb' => 'chemical/x-pdb',
    'pdf' => 'application/pdf',
    'pfunk' => 'audio/make',
    'pgm' => 'image/x-portable-greymap',
    'php' => 'application/x-httpd-php',
    'php3' => 'text/html',
    'php4' => 'text/html',
    'phps' => 'text/html',
    'phtml' => 'text/html',
    'pic' => 'image/pict',
    'pict' => 'image/pict',
    'pkg' => 'application/x-newton-compatible-pkg',
    'pko' => 'application/vnd.ms-pki.pko',
    'pl' => 'text/plain',
    'plx' => 'application/x-PiXCLscript',
    'pm' => 'image/x-xpixmap',
    'pm4 ' => 'application/x-pagemaker',
    'pm5' => 'application/x-pagemaker',
    'png' => 'image/png',
    'pnm' => 'application/x-portable-anymap',
    'pot' => 'application/mspowerpoint',	
    'pov' => 'model/x-pov',
    'ppa' => 'application/vnd.ms-powerpoint',
    'ppm' => 'image/x-portable-pixmap',
    'pps' => 'application/mspowerpoint',
    'ppt' => 'application/mspowerpoint',
    'ppz' => 'application/mspowerpoint',
    'pre' => 'application/x-freelance',
    'prt' => 'application/pro_eng',
    'ps' => 'application/postscript',
    'psd' => 'application/octet-stream',
    'pvu' => 'paleovu/x-pv',
    'pwz' => 'application/vnd.ms-powerpoint',
    'py' => 'text/x-script.phyton',	
    'pyc' => 'applicaiton/x-bytecode.python',		
    'qcp' => 'audio/vnd.qcelp',	
    'qd3' => 'x-world/x-3dmf',
    'qd3d' => 'x-world/x-3dmf',
    'qif' => 'image/x-quicktime',
    'qt' => 'video/quicktime',	
    'qtc' => 'video/x-qtc',
    'qti' => 'image/x-quicktime',
    'qtif' => 'image/x-quicktime',
    'ra' => 'audio/x-realaudio',	
    'ram' => 'audio/x-pn-realaudio',	
    'ras' => 'image/cmu-raster',
    'rast' => 'image/cmu-raster',
    'rex' => 'text/x-script.rexx',
    'rexx' => 'text/x-script.rexx',
    'rf' => 'image/vnd.rn-realflash',
    'rgb' => 'image/x-rgb',
    'rm' => 'audio/x-pn-realaudio',
    'rmi' => 'audio/mid',
    'rmm' => 'audio/x-pn-realaudio',
    'rmp' => 'audio/x-pn-realaudio',
    'rng' => 'application/ringing-tones',
    'rng' => 'application/vnd.nokia.ringing-tone',
    'rnx' => 'application/vnd.rn-realplayer',
    'roff' => 'application/x-troff',
    'rp' => 'image/vnd.rn-realpix',
    'rpm' => 'audio/x-pn-realaudio-plugin',
    'rt' => 'text/richtext',
    'rtf' => 'application/rtf',
    'rtx' => 'application/rtf',
    'rv' => 'video/vnd.rn-realvideo',
    's' => 'text/x-asm',
    's3m' => 'audio/s3m',
    'saveme' => 'application/octet-stream',
    'sbk' => 'application/x-tbook',
    'scm' => 'application/x-lotusscreencam',
    'sdml' => 'text/plain',
    'sdp' => 'application/sdp',
    'sdr' => 'application/sounder',
    'sea' => 'application/sea',
    'sea' => 'application/x-sea',
    'set' => 'application/set',
    'sgm' => 'text/sgml',
    'sgml' => 'text/sgml',
    'sh' => 'application/x-sh',
    'shar' => 'application/x-shar',
    'shtml' => 'text/html',
    'sid' => 'audio/x-psid',
    'sit' => 'application/x-stuffit',
    'skd' => 'application/x-koan',
    'skm' => 'application/x-koan',
    'skp' => 'application/x-koan',
    'skt' => 'application/x-koan',
    'sl' => 'application/x-seelogo',
    'smi' => 'application/smil',	
    'smil' => 'application/smil',
    'snd' => 'audio/basic',
    'sol' => 'application/solids',
    'spc' => 'text/x-speech',
    'spl' => 'application/futuresplash',
    'spr' => 'application/x-sprite',
    'sprite' => 'application/x-sprite',
    'src' => 'application/x-wais-source',
    'ssi' => 'text/x-server-parsed-html',
    'ssm' => 'application/streamingmedia',
    'sst' => 'application/vnd.ms-pki.certstore',
    'step' => 'application/step',
    'stl' => 'application/sla',
    'stp' => 'application/step',
    'sv4cpio' => 'application/x-sv4cpio',
    'sv4crc' => 'application/x-sv4crc',
    'svf' => 'image/vnd.dwg',
    'svr' => 'application/x-world',
    'swf' => 'application/x-shockwave-flash',
    't' => 'application/x-troff',
    'talk' => 'text/x-speech',
    'tar' => 'application/x-tar',
    'tbk' => 'application/toolbook',
    'tcl' => 'application/x-tcl',
    'tcsh' => 'text/x-script.tcsh',
    'tex' => 'application/x-tex',
    'texi' => 'application/x-texinfo',
    'texinfo' => 'application/x-texinfo',
    'text' => 'text/plain',
    'tgz' => 'application/x-compressed',
    'tif' => 'image/tiff',	
    'tiff' => 'image/tiff',	
    'tr' => 'application/x-troff',
    'tsi' => 'audio/tsp-audio',
    'tsp' => 'audio/tsplayer',
    'tsv' => 'text/tab-separated-values',
    'turbot' => 'image/florian',
    'txt' => 'text/plain',
    'uil' => 'text/x-uil',
    'uni' => 'text/uri-list',
    'unis' => 'text/uri-list',
    'unv' => 'application/i-deas',
    'uri' => 'text/uri-list',
    'uris' => 'text/uri-list',
    'ustar' => 'application/x-ustar',
    'uu' => 'application/octet-stream',
    'uue' => 'text/x-uuencode',
    'vcd' => 'application/x-cdlink',
    'vcs' => 'text/x-vCalendar',
    'vda' => 'application/vda',
    'vdo' => 'video/vdo',
    'vew ' => 'application/groupwise',
    'viv' => 'video/vivo',
    'vivo' => 'video/vivo',
    'vmd' => 'application/vocaltec-media-desc',
    'vmf' => 'application/vocaltec-media-file',
    'voc' => 'audio/voc',
    'vos' => 'video/vosaic',
    'vox' => 'audio/voxware',
    'vqe' => 'audio/x-twinvq-plugin',
    'vqf' => 'audio/x-twinvq',
    'vql' => 'audio/x-twinvq-plugin',	
    'vrml' => 'application/x-vrml',
    'vrt' => 'x-world/x-vrt',
    'vsd' => 'application/x-visio',
    'vst' => 'application/x-visio',
    'vsw' => 'application/x-visio',
    'w60' => 'application/wordperfect6.0',
    'w61' => 'application/wordperfect6.1',
    'w6w' => 'application/msword',
    'wav' => 'audio/wav',
    'wb1' => 'application/x-qpro',
    'wbmp' => 'image/vnd.wap.wbmp',
    'web' => 'application/vnd.xara',
    'wiz' => 'application/msword',
    'wk1' => 'application/x-123',
    'wmf' => 'windows/metafile',
    'wml' => 'text/vnd.wap.wml',
    'wmlc' => 'application/vnd.wap.wmlc',
    'wmls' => 'text/vnd.wap.wmlscript',	
    'wmlsc' => 'application/vnd.wap.wmlscriptc',
    'word' => 'application/msword',
    'wp' => 'application/wordperfect',
    'wp5' => 'application/wordperfect',
    'wp6' => 'application/wordperfect',
    'wpd' => 'application/wordperfect',
    'wq1' => 'application/x-lotus',	
    'wri' => 'application/mswrite',
    'wrl' => 'model/vrml',
    'wrz' => 'model/vrml',
    'wsc' => 'text/scriplet',
    'wsrc' => 'application/x-wais-source',
    'wtk' => 'application/x-wintalk',
    'xbm' => 'image/xbm',
    'xdr' => 'video/x-amt-demorun',
    'xgz' => 'xgl/drawing',
    'xif' => 'image/vnd.xiff',
    'xl' => 'application/excel',
    'xla' => 'application/excel',
    'xlb' => 'application/excel',
    'xlc' => 'application/excel',
    'xld' => 'application/excel',
    'xlk' => 'application/excel',
    'xll' => 'application/excel',
    'xlm' => 'application/excel',
    'xls' => 'application/excel',
    'xlt' => 'application/excel',
    'xlv' => 'application/excel',
    'xlw' => 'application/excel',
    'xlw' => 'application/x-excel',
    'xm' => 'audio/xm',
    'xml' => 'text/xml',
    'xmz' => 'xgl/movie',
    'xpix' => 'application/x-vnd.ls-xpix',
    'xpm' => 'image/xpm',
    'x-png' => 'image/png',
    'xsr' => 'video/x-amt-showrun',
    'xwd' => 'image/x-xwd',
    'xyz' => 'chemical/x-pdb',
    'z' => 'application/x-compress',	
    'zip' => 'application/zip',
    'zoo' => 'application/octet-stream',
    'zsh' => 'text/x-script.zsh',);
   // require_once('soap/config_upload.php');
    
   // global $MWJma;
   $ext = trim($ext);
    if( !$ext || !$file_ext[$ext] ) {
        return 'application/octet-stream';
     } else {
        return $file_ext[$ext];
     }
 }

?>
