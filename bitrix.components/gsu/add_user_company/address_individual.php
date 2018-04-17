<?php
/**
 * Created by PhpStorm.
 * User: GSU
 * Date: 22.11.2016
 * Time: 18:01
 */

include 'dadata_config.php';
//$dadata_address_url="https://dadata.ru/api/v2/clean/address";

$data = json_encode([$_REQUEST["address"]]);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $dadata_address_url);
curl_setopt($ch, CURLOPT_POST, true); // -X
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json","Authorization: Token ".$dadata_address_token."","X-Secret: ".$dadata_address_secret.""]); // -H
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);

if($response==="{\"detail\":\"Zero balance\"}"){
    echo "error";
}else{


    echo $response;
}

?>