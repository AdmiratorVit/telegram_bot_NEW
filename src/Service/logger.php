<?php

namespace Admirator\TelegaLoc\Service;

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

class logger
{
    public function toLog($textTitl = '', $textLog = '')
    {
        $filesystem = new Filesystem();
        try {
            $filesystem->appendToFile('debug.txt', '======================= ' . date('d.m.Y H:i:s') . '======================= ' . PHP_EOL);
            $filesystem->appendToFile('debug.txt', $textTitl . PHP_EOL . print_r($textLog, true) . PHP_EOL);
        } catch (IOExceptionInterface $exception) {
            echo "Ошибка записи в файл " . $exception->getPath();
        }
    }
}

