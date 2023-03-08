<?php

use Admirator\TelegaLoc\Service\TelegramBotClient;
use Symfony\Component\Dotenv\Dotenv;

require_once('./vendor/autoload.php');

(new Dotenv())->load(__DIR__ . DIRECTORY_SEPARATOR . '.env');

foreach ($_ENV as $name => $bot_token) {
    if (str_ends_with($name, '_BOT')) {
        $bots[$name] = new TelegramBotClient($bot_token, $name);
    }
}

if ($_REQUEST['vit'] == 1) {
    echo 'МОЙ ДЕБАГ';
}

foreach ($bots as $bot) {
    $updated = $bot->getUpdates();
}

//$response = $telegramBotClient->getUpdates();
//$response = $telegramBotClient->getUpdates()['result'][0]['message']['chat']['id'];
//
//$response = $telegramBotClient->sendMessage('-1001805235187', 'hello world 123 !!!');

//$response = $telegramBotClient->sendMessage((int)$telegramBotClient->getUpdates()['result'][0]['message']['chat']['id'], 'hello world 123 !!!');

//$response = $telegramBotClient->sendDocument('-1001805235187', 'c:\Program Files\OSPanel\domains\telega.loc\README.md');

//print_r($response);

//$telegramBotClient->sendMessage(-1001805235187,'2234234234');

//var_dump($telegramBotClient->getUpdates());


//print_r($response);