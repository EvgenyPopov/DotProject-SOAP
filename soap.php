<?php
// подключение библиотеки
require_once ('base.php');
require_once ('lib/nusoap/nusoap.php');

require_once('soap/SoapError.php');

$NAMESPACE = 'urn:dotprojectwsdl';

$server = new soap_server;   // Создаем экземпляр сервера

$server->configureWSDL('dotprojectwsdl', $NAMESPACE,'http://dotproject/soap.php');  // Инициализируем поддержку WSDL

$server->wsdl->schemaTargetNamespace = $NAMESPACE;   // Устанавливаем пространство имен с префиксом tns для WSDL-схемы

$server->soap_defencoding = 'UTF-8';
$server->decode_utf8 = FALSE;
$server->debug_flag = true;

require_once('soap/SoapFunctions.php');

$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
$server->service($HTTP_RAW_POST_DATA);

?>
