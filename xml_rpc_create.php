<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php

// Copyright (C) 2016 - IDACTIVOS.COM. All Rights Reserved
// @author Didier Hernandez <http://idactivos.com>

// XML-RPC AVEC ERP & PHP
  // Connexion à OpenERP
  // Create a res.partner

echo '<h2>XML-RPC ERP & PHP</h2>';

include("xmlrpc_lib/xmlrpc.inc.php");
include("xmlrpc_lib/xmlrpcs.inc.php");
$GLOBALS['xmlrpc_internalencoding']='UTF-8';
include("cnx.inc.php");

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

    //Add required field for customer
    $val = array ( 
        "name"    => new xmlrpcval("Customer 1", "string"),
        "street"  => new xmlrpcval("Street 1", "string"),
        "city"    => new xmlrpcval("San Jose", "string"),
        "zip"     => new xmlrpcval("77000", "string"),
        "website" => new xmlrpcval("http://www.idactivos.com", "string"),
        "lang"    => new xmlrpcval("es_CR", "string"),
        "tz"      => new xmlrpcval("America/Costa_Rica", "string"),
        ); 
    
    $client = new xmlrpc_client($server_url . "/xmlrpc/object");
    $client->setSSLVerifyPeer(0);

    $msg = new xmlrpcmsg('execute'); 
    $msg->addParam(new xmlrpcval($dbname, "string")); 
    $msg->addParam(new xmlrpcval($uid, "int")); 
    $msg->addParam(new xmlrpcval($password, "string")); 
    $msg->addParam(new xmlrpcval("res.partner", "string")); 
    $msg->addParam(new xmlrpcval("create", "string")); 
    $msg->addParam(new xmlrpcval($val, "struct")); 
    $resp = $client->send($msg);

    if ($resp->faultCode()){
        echo $resp->faultString();
    }
    $result = $resp->value()->scalarval();

    //echo 'Partner created - partner_id = ' . $response->value()->scalarval();
    //$result = $response->value();
    //$result->scalarval();
    echo 'Partner created - partner_id = ' . print_r($result);
}
//xml_rpc_create.php
?>