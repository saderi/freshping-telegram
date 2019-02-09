<?php

// Get data from webhook
$json_data = file_get_contents("php://input");
if (empty($json_data))
    die();

// Log webhook action
$file = 'access.log';
$current = file_get_contents($file);
$current .= date('[j/M/Y H:i:s]'). " $json_data \n";
file_put_contents($file, $current);

// Get config variables
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/vendor/autoload.php';

$telegram = new Longman\TelegramBot\Telegram(BOT_TOKEN, BOT_USERNAME);
use Longman\TelegramBot\Request;

$data = json_decode($json_data);
$check_state = $data->webhook_event_data->check_state_name;
$request_url = $data->webhook_event_data->request_url;
$request_start_time = date(
    'Y-m-d H:i:s T', 
    strtotime( $data->webhook_event_data->request_start_time )
);
$check_name = $data->webhook_event_data->check_name;

$message = "Check state changed to $check_state \n";
$message .= "URL tested: $request_url \n";
$message .= "test time: $request_start_time \n";

$data = [
    'chat_id' => CHAT_ID,
    'text'    => $message,
];

if ( ! in_array( $check_name, $exceptionList ) ) {
    if ( ANY_STATE ) {
        $resutl = Request::sendMessage($data);
    } elseif ( $check_state == 'Not Responding' ) {
        $resutl = Request::sendMessage($data);
    }
}


?>
