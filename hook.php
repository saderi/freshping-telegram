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

$telegram = new Longman\TelegramBot\Telegram($bot_token, $bot_username);
use Longman\TelegramBot\Request;

$data = json_decode($json_data);
$webhook_event_data = $data->webhook_event_data;
if ($webhook_event_data->check_state_name == 'Not Responding' && 
    !in_array($webhook_event_data->check_name, $exceptionList)) {

    $message = "Your site has a problem. Site name: $webhook_event_data->request_url.";

    $data = [
        'chat_id' => $chat_id,
        'text'    => $message,
    ];

    $resutl = Request::sendMessage($data);

}

?>
