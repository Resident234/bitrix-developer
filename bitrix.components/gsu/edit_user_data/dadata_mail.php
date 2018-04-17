<?php
/**
 * Created by PhpStorm.
 * User: GSU
 * Date: 16.11.2016
 * Time: 17:17
 */
include 'dadata_config.php';

$data = json_encode([$_REQUEST["mail"]]);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $dadata_mail_url);
curl_setopt($ch, CURLOPT_POST, true); // -X
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json","Authorization: Token ".$dadata_mail_token."","X-Secret: ".$dadata_mail_secret.""]); // -H
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);

if($response==="{\"detail\":\"Zero balance\"}"){
    echo "error";
}else{


    echo $response;
}




?>