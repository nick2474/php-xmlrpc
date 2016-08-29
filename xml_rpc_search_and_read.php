<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php

// Copyright (C) 2016 - IDACTIVOS.COM. All Rights Reserved
// @author Didier Hernandez <http://idactivos.com>

// XML-RPC ERP & PHP
// Connexion to ERP
// Search all res.partner with domain [('is_company', '!=', 'True')]
// Read all res.partner with field name, email, street, city and zip

echo '<h2>XML-RPC ERP & PHP</h2>';

include("xmlrpc_lib/xmlrpc.inc.php");
include("xmlrpc_lib/xmlrpcs.inc.php");
$GLOBALS['xmlrpc_internalencoding']='UTF-8';

//==>CONECCTION STRING
include("cnx.inc.php");

$connexion = new xmlrpc_client($server_url . "/xmlrpc/common");
$connexion->setSSLVerifyPeer(0);

$c_msg = new xmlrpcmsg('login');
$c_msg->addParam(new xmlrpcval($dbname, "string"));
$c_msg->addParam(new xmlrpcval($user, "string"));
$c_msg->addParam(new xmlrpcval($password, "string"));
$c_response = $connexion->send($c_msg);
//==>CONECCTION STRING

if ($c_response->errno != 0){
    echo  '<p>error : ' . $c_response->faultString() . '</p>';
}
else{
    
    $uid = $c_response->value()->scalarval();

    //Filters
    $domain_filter = array ( 
        new xmlrpcval(
            array(new xmlrpcval('is_company' , "string"), 
                  new xmlrpcval('!=',"string"), 
                  new xmlrpcval('True',"string")
                  ),"array"             
            ),
        ); 
    
    $client = new xmlrpc_client($server_url . "/xmlrpc/object");
    $client->setSSLVerifyPeer(0);

    $msg = new xmlrpcmsg('execute'); 
    $msg->addParam(new xmlrpcval($dbname, "string")); 
    $msg->addParam(new xmlrpcval($uid, "int")); 
    $msg->addParam(new xmlrpcval($password, "string")); 
    $msg->addParam(new xmlrpcval("res.partner", "string")); 
    $msg->addParam(new xmlrpcval("search", "string")); 
    $msg->addParam(new xmlrpcval($domain_filter, "array")); 
    $response = $client->send($msg);    
    
    $result = $response->value();
    $ids = $result->scalarval();
   
    $id_list = array();
    
    for($i = 0; $i < count($ids); $i++){
        $id_list[]= new xmlrpcval($ids[$i]->me['int'], 'int');
    }

    $field_list = array(
        new xmlrpcval("name", "string"),
        new xmlrpcval("email", "string"),
        new xmlrpcval("phone", "string"),
        new xmlrpcval("street", "string"),
        new xmlrpcval("city", "string"),
        new xmlrpcval("zip", "string"),
    ); 
     
    $msg = new xmlrpcmsg('execute');
    $msg->addParam(new xmlrpcval($dbname, "string"));
    $msg->addParam(new xmlrpcval($uid, "int"));
    $msg->addParam(new xmlrpcval($password, "string"));
    $msg->addParam(new xmlrpcval("res.partner", "string"));
    $msg->addParam(new xmlrpcval("read", "string")); 
    $msg->addParam(new xmlrpcval($id_list, "array")); 
    $msg->addParam(new xmlrpcval($field_list, "array")); 

    $resp = $client->send($msg);

    if ($resp->faultCode()){
        echo $resp->faultString();
    }

    $result = $resp->value()->scalarval();    
    
    //echo '<h2>Results print_r($result) :</h2>';
    //echo "<pre>";
    //print_r($result);
    //echo "</pre>";

    echo '<hr />';
    echo '<h2>List of partners which doesnt a Company:</h2>';

    //HTML print
    for($i = 0; $i < count($result); $i++){
        echo '<h1>' . $result[$i]->me['struct']['name']->me['string'] . '</h1>'
           . '<ol>'
           . '<li><strong>Email</strong> : ' . $result[$i]->me['struct']['email']->me['string'] . '</li>'
           . '<li><strong>Phone</strong> : ' . $result[$i]->me['struct']['phone']->me['string'] . '</li>'
           . '<li><strong>Street</strong> : ' . $result[$i]->me['struct']['street']->me['string'] . '</li>'
           . '<li><strong>City</strong> : ' . $result[$i]->me['struct']['city']->me['string'] . '</li>'
           . '<li><strong>Zip code</strong> : ' . $result[$i]->me['struct']['zip']->me['string'] . '</li>'
           . '</ol>'     
           . '<hr />';
    }

}
//xml_rpc_search_and_read.php
?>
