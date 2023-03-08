<?php

namespace Admirator\TelegaLoc\Service;


use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;

class TelegramBotClient
{
    private const API = 'https://api.telegram.org/bot';
    private const SEND_MESSAGE = 'sendMessage';
    private const GET_UPDATES = 'getUpdates';
    private const SEND_DOCUMENT = 'sendDocument';


    private string $token;
    private string $botname;
    private HttpClientInterface $httpClient;

    public function __construct(string $token, string $botname)
    {
        $this->token = $token;
        $this->botname = $botname;
        $this->httpClient = HttpClient::create();
    }

    public function getUpdates(): array
    {
        $lastUpd = $this->getLastUpdate();
        $response = $this->httpClient->request("GET", $this->getUri(self::GET_UPDATES), ['query' => ['offset' => $lastUpd + 1]]);
        $temparr = $response->toArray(false);
        foreach ($temparr as $key => $updItem) {
            foreach ($updItem as $keyitem => $updData) {
                if ($updData['update_id'] > $lastUpd) {
                    $lastUpd = $updData['update_id'];
                }
            }
        }

        $this->setLastUpdate($lastUpd);
        return $response->toArray(false);

    }

    private function getLastUpdate()
    {
        if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'Data' . DIRECTORY_SEPARATOR . $this->botname . '.txt')) {
            try {
                return file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'Data' . DIRECTORY_SEPARATOR . $this->botname . '.txt');
            } catch (IOExceptionInterface $exception) {
                echo "Ошибка записи в файл " . $exception->getPath();
            }
        } else {
            return 0;
        }
    }


    public function setLastUpdate($lastupd)
    {
        $filesystem = new Filesystem();
        try {
            $filesystem->dumpFile(__DIR__ . DIRECTORY_SEPARATOR . 'Data' . DIRECTORY_SEPARATOR . $this->botname . '.txt', $lastupd);
        } catch (IOExceptionInterface $exception) {
            echo "Ошибка записи в файл " . $exception->getPath();
        }
    }

    public function sendMessage(int $chatId, string $text): array
    {
        $response = $this->httpClient->request("GET", $this->getUri(self::SEND_MESSAGE) . '?chat_id=' . $chatId . '&text=' . $text);
        return $response->toArray(false);
    }

    public function sendDocument(string $chatId, string $filePath)
    {
        $formFields = [
            'chat_id' => $chatId,
            'document' => DataPart::fromPath($filePath),
        ];
        $formData = new FormDataPart($formFields);

        $response = $this->httpClient->request("POST", $this->getUri(self::SEND_DOCUMENT),
            [
                'headers' => $formData->getPreparedHeaders()->toArray(),
                'body' => $formData->bodyToIterable(),
            ]
        );

        return $response->toArray();
    }

    public function sendPhoto(string $chatId, string $filePath)
    {
        $formFields = [
            'chat_id' => $chatId,
            'document' => DataPart::fromPath($filePath),
        ];
        $formData = new FormDataPart($formFields);

        $response = $this->httpClient->request("POST", $this->getUri(self::SEND_PHOTO),
            [
                'headers' => $formData->getPreparedHeaders()->toArray(),
                'body' => $formData->bodyToIterable(),
            ]
        );

        return $response->toArray();
    }

    private function getUri(string $telegramMethod): string
    {
        return self::API . $this->token . '/' . $telegramMethod;
    }
}

