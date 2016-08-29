<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php

// Copyright (C) 2016 - IDACTIVOS.COM. All Rights Reserved
// @author Didier Hernandez <http://idactivos.com>

// XML-RPC ERP & PHP
// Connexion to ERP
  // Write / Update a res.partner 

echo '<h2>XML-RPC AVEC OPENERP/ODOO ET PHP</h2>';

include("xmlrpc_lib/xmlrpc.inc.php");
include("xmlrpc_lib/xmlrpcs.inc.php");
$GLOBALS['xmlrpc_internalencoding']='UTF-8';

$user = 'admin';
$password = 'mY5up3rPwd';
$dbname = 'test';

$server_url = 'http://192.168.1.120:8069'; 
$connexion = new xmlrpc_client($server_url . "/xmlrpc/common");
$connexion->setSSLVerifyPeer(0);

$c_msg = new xmlrpcmsg('login');
$c_msg->addParam(new xmlrpcval($dbname, "string"));
$c_msg->addParam(new xmlrpcval($user, "string"));
$c_msg->addParam(new xmlrpcval($password, "string"));
$c_response = $connexion->send($c_msg);

if ($c_response->errno != 0){
    echo  '<p>error : ' . $c_response->faultString() . '</p>';
}
else{
    
    $uid = $c_response->value()->scalarval();
    
    $id_list = array();
    $id_list[]= new xmlrpcval(89, 'int');

    $values = array ( 
        'street'=>new xmlrpcval('Rue de la gare' , "string"), 
        'city'=>new xmlrpcval('Fontainebleau',"string"), 
        'zip'=>new xmlrpcval('77120',"string")            
        ); 
    
    
    $client = new xmlrpc_client($server_url . "/xmlrpc/object");
    $client->setSSLVerifyPeer(0);

    $msg = new xmlrpcmsg('execute'); 
    $msg->addParam(new xmlrpcval($dbname, "string")); 
    $msg->addParam(new xmlrpcval($uid, "int")); 
    $msg->addParam(new xmlrpcval($password, "string")); 
    $msg->addParam(new xmlrpcval("res.partner", "string")); 
    $msg->addParam(new xmlrpcval("write", "string")); 
    $msg->addParam(new xmlrpcval($id_list, "array"));
    $msg->addParam(new xmlrpcval($values, "struct")); 
    $response = $client->send($msg);

    if ($response->faultCode()){
        echo $response->faultString();
    } 
   
    echo '<h2>Mise à jour effectuée</h2>';
}
//xml_rpc_write.php
?>
