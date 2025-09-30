<?php

namespace App\ApplicationConfiguration;

use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;

/*
 * Konfigurasi untuk monolog config
 * */
class MonologConfig
{
    public static function getApplicationLogger()
    {
        return self::getConfig('application',request(),false);
    }

    public static function getSecurityLogger()
    {
        return self::getConfig('security',request(),false);
    }

    private static function getConfig(string $channel,IncomingRequest $request,bool $productionLog=false): Logger
    {
        $logger = new Logger($channel);

        // apabila di setting log production maka akan diberikan log file
        if ($productionLog){
            // handler untuk file log
            // rotasi 7 file untuk 7 hari
            $handler = new RotatingFileHandler(
                WRITEPATH . "logs/{$channel}.log",
                7,Level::Debug);

            // menggunakan custom json agar format yang tampil bagus
            $formatter = new CustomJsonFormatter();
            $handler->setFormatter($formatter);

            // pasang handler & processor
            $logger->pushHandler($handler);
            $logger->pushProcessor(new RequestProcessor($request));
        }else{
            $streamHandler  = new StreamHandler("php://stderr");
            $logger->pushHandler($streamHandler);
        }

        return $logger;
    }
}