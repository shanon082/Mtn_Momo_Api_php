<?php
include "createaccesstoken.php";
$phone = '256771950092';
$amount = '20.0'; 
$currency = 'EUR';
$url = "https://sandbox.momodeveloper.mtn.com/collection/v1_0/requesttopay";
$curl = curl_init($url);

curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

# Request headers
$headers = array(
    'Authorization: Bearer '.$access_token,
    'X-Reference-Id: '. $reference_id,
    'X-Target-Environment: sandbox',
    'Content-Type: application/json',
    'Ocp-Apim-Subscription-Key: '.$primary_key
);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
$external_id = rand(10000000, 99999999);
# Request body
$request_body = array(
    'amount' => $amount,
    'currency' => $currency,
    "externalId" => $external_id,
    "payer"=> array(
        "partyIdType"=> "MSISDN",
        "partyId"=> $phone
),
    'payerMessage' => 'Umeskia Softwares MTN Payment',
    'payeeNote' => 'Thank you for using Umeskia Softwares MTN Payment'
);
$json_body = json_encode($request_body);
// curl_setopt($curl, CURLOPT_POSTFIELDS, $request_body);
curl_setopt_array($curl, array(
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => $headers,
    CURLOPT_POSTFIELDS => $json_body
));

$resp = curl_exec($curl);
curl_close($curl);
var_dump($resp);
?>