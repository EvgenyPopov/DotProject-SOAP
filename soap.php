<?php
// ����������� ����������
require_once ('base.php');
require_once ('lib/nusoap/nusoap.php');

require_once('soap/SoapError.php');

$NAMESPACE = 'urn:dotprojectwsdl';

$server = new soap_server;   // ������� ��������� �������

$server->configureWSDL('dotprojectwsdl', $NAMESPACE,'http://dotproject/soap.php');  // �������������� ��������� WSDL

$server->wsdl->schemaTargetNamespace = $NAMESPACE;   // ������������� ������������ ���� � ��������� tns ��� WSDL-�����

$server->soap_defencoding = 'UTF-8';
$server->decode_utf8 = FALSE;
$server->debug_flag = true;

require_once('soap/SoapFunctions.php');

$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
$server->service($HTTP_RAW_POST_DATA);

?>
