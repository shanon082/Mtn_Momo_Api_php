<?php
include "config.php";
include "createaccesstoken.php";

header('Content-Type: application/json');

$reference_id = $_GET['reference'] ?? '';

if (empty($reference_id)) {
    echo json_encode(['error' => 'No reference ID provided']);
    exit;
}

// Check payment status
$url = "https://sandbox.momodeveloper.mtn.com/collection/v1_0/requesttopay/" . $reference_id;
$headers = array(
    "Authorization: Bearer " . $access_token,
    "X-Target-Environment: sandbox",
    "Ocp-Apim-Subscription-Key: $secodary_key"
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpcode == 200) {
    $data = json_decode($response, true);
    echo json_encode(['status' => $data['status'] ?? 'UNKNOWN']);
} else {
    echo json_encode(['status' => 'PENDING', 'http_code' => $httpcode]);
}
?>