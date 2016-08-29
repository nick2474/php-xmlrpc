<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php

// Copyright (C) 2016 - IDACTIVOS.COM. All Rights Reserved
// @author Didier Hernandez <http://idactivos.com>

// XML-RPC ERP & PHP
// Connexion to ERP
  // Search sur res.partner avec le domaine [('name', 'ilike', 'godin')]
  // Unlink des partners trouvés

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

if ($c_response->errno != 0){
    echo  '<p>error : ' . $c_response->faultString() . '</p>';
}
else{
    
    $uid = $c_response->value()->scalarval();

    $domain_filter = array ( 
        new xmlrpcval(
            array(new xmlrpcval('name' , "string"), 
                  new xmlrpcval('ilike',"string"), 
                  new xmlrpcval('Customer',"string") /*String to find*/
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
 
    $msg = new xmlrpcmsg('execute');
    $msg->addParam(new xmlrpcval($dbname, "string"));
    $msg->addParam(new xmlrpcval($uid, "int"));
    $msg->addParam(new xmlrpcval($password, "string"));
    $msg->addParam(new xmlrpcval("res.partner", "string"));
    $msg->addParam(new xmlrpcval("unlink", "string")); 
    $msg->addParam(new xmlrpcval($id_list, "array")); 

    $resp = $client->send($msg);

    if ($resp->faultCode()){
        echo $resp->faultString();
    }

    echo '<h2>List of partners deleted:</h2>';
   
    for($i = 0; $i < count($id_list); $i++){
        echo '<li><strong>ID</strong> : ' . $id_list[$i]->me['int'] . '</li>';
    }

}
//xml_rpc_search_and_unlink.php
?>


