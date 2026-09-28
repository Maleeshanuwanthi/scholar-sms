<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/scholar_sms/config/sms.php";

// Converts a local Sri Lankan number (0771234567 / 771234567) into
// the 94XXXXXXXXX format Notify.lk requires.
function format_lk_phone($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    if (substr($phone, 0, 1) === '0') {
        $phone = '94' . substr($phone, 1);
    } elseif (substr($phone, 0, 2) !== '94') {
        $phone = '94' . $phone;
    }
    return $phone;
}

// Sends an SMS via Notify.lk. Returns true on success, false on failure.
// Never throws - a failed SMS should never break the page that triggered it.
function send_sms($phone, $message) {
    if (empty($phone)) return false;

    $to = format_lk_phone($phone);
    $url = "https://app.notify.lk/api/v1/send";

    $params = http_build_query([
        'user_id'   => NOTIFY_USER_ID,
        'api_key'   => NOTIFY_API_KEY,
        'sender_id' => NOTIFY_SENDER_ID,
        'to'        => $to,
        'message'   => $message,
    ]);

    $ch = curl_init($url . "?" . $params);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 8);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    error_log("Notify.lk SMS to $to: " . ($response ?: $error));
    return $response !== false;
}
