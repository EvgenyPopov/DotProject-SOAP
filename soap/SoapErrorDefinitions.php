<?php

$error_defs = array(
'no_error'=>array('number'=>0 , 'name'=>'No Error', 'description'=>'No Error'),
'invalid_login'=>array('number'=>10 , 'name'=>'Invalid Login', 'description'=>'Login attempt failed please check the username and password'),
'invalid_session'=>array('number'=>11 , 'name'=>'Invalid Session ID', 'description'=>'The session ID is invalid'),
'user_not_configure'=>array('number'=>12 , 'name'=>'User Not Configured', 'description'=>'Please log into your instance of SugarCRM to configure your user. '),
'no_portal'=>array('number'=>12 , 'name'=>'Invalid Portal Client', 'description'=>'Portal Client does not have authorized access'),
'no_module'=>array('number'=>20 , 'name'=>'Module Does Not Exist', 'description'=>'This module is not available on this server'),
'no_file'=>array('number'=>21 , 'name'=>'File Does Not Exist', 'description'=>'The desired file does not exist on the server'),
'no_module_support'=>array('number'=>30 , 'name'=>'Module Not Supported', 'description'=>'This module does not support this feature'),
'no_relationship_support'=>array('number'=>31 , 'name'=>'Relationship Not Supported', 'description'=>'This module does not support this relationship'),
'no_access'=>array('number'=>40 , 'name'=>'Access Denied', 'description'=>'You do not have access'),
'duplicates'=>array('number'=>50 , 'name'=>'Duplicate Records', 'description'=>'Duplicate records have been found. Please be more specific.'),
'no_records'=>array('number'=>51 , 'name'=>'No Records', 'description'=>'No records were found.'),
'cannot_add_client'=>array('number'=>52 , 'name'=>'Cannot Add Offline Client', 'description'=>'Unable to add Offline Client.'),
'client_deactivated'=>array('number'=>53 , 'name'=>'Client Deactivated', 'description'=>'Your Offline Client instance has been deactivated.  Please contact your Administrator in order to resolve.'),
'sessions_exceeded'=>array('number'=>60 , 'name'=>'Number of sessions exceeded.'),
'upgrade_client'=>array('number'=>61 , 'name'=>'Upgrade Client', 'description'=>'Please contact your Administrator in order to upgrade your Offline Client'),
'no_admin' => array('number' => 70, 'name' => 'Admin credentials are required', 'description' => 'The logged-in user is not an administrator'),
'custom_field_type_not_supported' => array('number' => 80, 'name' => 'Custom field type not supported', 'description' => 'The custom field type you supplied is not currently supported'),
'custom_field_property_not_supplied' => array('number' => 81, 'name' => 'Custom field property not supplied', 'description' => 'You are missing one or more properties for the supplied custom field type'),
'resource_management_error' => array('number'=>90, 'name'=>'Resource Management Error', 'description'=>'The resource query limit specified in config.php has been exceeded during execution of the SOAP method'),

);
?>
