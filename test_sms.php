<?php
// Quick standalone test - visit this page directly in the browser:
// http://localhost/scholar_sms/test_sms.php?phone=0771234567
require_once $_SERVER['DOCUMENT_ROOT'] . "/scholar_sms/config/sms.php";

echo "<h2>SMS Test</h2>";

echo "<p><b>User ID:</b> " . NOTIFY_USER_ID . "</p>";
echo "<p><b>API Key:</b> " . substr(NOTIFY_API_KEY, 0, 4) . "********</p>";
echo "<p><b>Sender ID:</b> " . NOTIFY_SENDER_ID . "</p>";

if (!function_exists('curl_init')) {
    echo "<p style='color:red;'><b>PROBLEM FOUND:</b> cURL is NOT enabled in your PHP. This is why SMS isn't sending.</p>";
    exit;
} else {
    echo "<p style='color:green;'>cURL is enabled. Good.</p>";
}

$phone = isset($_GET['phone']) ? $_GET['phone'] : '0771234567';
$phone = preg_replace('/[^0-9]/', '', $phone);
if (substr($phone, 0, 1) === '0') {
    $phone = '94' . substr($phone, 1);
} elseif (substr($phone, 0, 2) !== '94') {
    $phone = '94' . $phone;
}

echo "<p><b>Sending to (formatted):</b> $phone</p>";

$url = "https://app.notify.lk/api/v1/send";
$params = http_build_query([
    'user_id'   => NOTIFY_USER_ID,
    'api_key'   => NOTIFY_API_KEY,
    'sender_id' => NOTIFY_SENDER_ID,
    'to'        => $phone,
    'message'   => 'Test message from Scholar SMS',
]);

$ch = curl_init($url . "?" . $params);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "<p><b>HTTP Status Code:</b> $httpCode</p>";
echo "<p><b>cURL Error (if any):</b> " . ($error ?: 'none') . "</p>";
echo "<p><b>API Response:</b></p>";
echo "<pre style='background:#f4f4f4; padding:12px; border-radius:8px;'>" . htmlspecialchars($response) . "</pre>";
