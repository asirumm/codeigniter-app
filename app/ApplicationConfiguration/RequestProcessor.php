<?php

namespace App\ApplicationConfiguration;

use CodeIgniter\HTTP\IncomingRequest;
use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;

/**
 * Class: RequestProcessor
 *
 * Processor custom untuk logging.
 *
 * Fungsi utama:
 * 1. Menambahkan informasi request ke log.
 * 2. Memungkinkan log lebih kontekstual berdasarkan data request.
 *
 * Contoh data yang bisa ditambahkan:
 * - IP address user
 * - URL endpoint yang diakses
 * - Method request (GET, POST, dll)
 */
class RequestProcessor implements ProcessorInterface
{
    private IncomingRequest $request;
    private string $userReqId;

    public function __construct(IncomingRequest $request)
    {
        $this->request = $request;

        // generate request id sekali saat instance dibuat
        $this->userReqId = "req-" . random_int(0, 555);
    }

    public function __invoke(LogRecord $record)
    {


        $ip        = $this->request->getIPAddress();
        $browser   = $this->request->getUserAgent()->getBrowser();
        $os        = $this->request->getUserAgent()->getPlatform();
        $mobile    = $this->request->getUserAgent()->getMobile()?$this->request->getUserAgent()->getMobile():"Desktop";
        $userReq   = $this->userReqId;
        $uri       = (string) $this->request->getUri();
        $method    = $this->request->getMethod();



        // extra memang dari monolog
        $record['extra']['request_id'] = $userReq;
        $record['extra']['ip_address'] = $ip;
        $record['extra']['method']     = $method;
        $record['extra']['path']       = $uri ;
        $record['extra']['browser']    = $browser;
        $record['extra']['mobile']     = $mobile;
        $record['extra']['operating_system'] = $os;


        return $record;
    }
}