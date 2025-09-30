<?php

namespace App\ApplicationConfiguration;

use DateTime;
use DateTimeZone;
use Monolog\Formatter\JsonFormatter;
use Monolog\LogRecord;

/**
* Memformat otuput logging menjadi lebih baik,
 * dan set datetime sesuai zona waktu
 */
class CustomJsonFormatter extends  JsonFormatter
{
    public function format(LogRecord $record): string
    {
        $datetime = new DateTime('now', new DateTimeZone('Asia/Jakarta'));

        $customRecord = [
            'timestamp' => $datetime->format('Y-m-d H:i'),
            'log_level' => $record->level->getName(),
            'channel'   => $record->channel,
            'message'   => $record->message,
            'context'   => $record->context,
            'extra'     => $record->extra,
        ];

        return json_encode(
            $customRecord,
             JSON_PRETTY_PRINT
        );
    }
}