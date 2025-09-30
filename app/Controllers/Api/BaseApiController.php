<?php

namespace App\Controllers\Api;


use App\ApplicationConfiguration\MonologConfig;
use CodeIgniter\RESTful\ResourceController;
use Monolog\Logger;

abstract class BaseApiController extends ResourceController
{
    protected Logger $logger;

    public function __construct()
    {
        $this->logger = MonologConfig::getApplicationLogger();
    }

    protected function responseSuccess($data,$message='')
    {
        return $this->respond([
            'status'  => 'success',
            'message' => $message,
            'data'    => $data
        ], 200);
    }

    protected function responseCreated($data = null,  $message = '')
    {
        return $this->respond([
            'status'  => 'success',
            'message' => $message,
            'data'    => $data
        ], $this->codes['created']);
    }

    protected function responseFail( $message)
    {
        return $this->respond([
            'status'  => 'error',
            'message' => $message
        ], 400);
    }

    protected function responseDeleted($data = null, $message="")
    {
        return $this->respond([
            'status'  => 'success',
            'message' => $message
        ], $this->codes['deleted']);
    }

    protected function responseUpdated($data = null,  $message = '')
    {
        return $this->respond([
            'status'  => 'success',
            'message' => $message,
            'data'    => $data
        ], $this->codes['updated']);
    }

    protected function responseFailServerError(string $description = 'Internal Server Error'): ResponseInterface
    {
        return $this->respond([
            'status'  => 'error',
            'message' => $description
        ], $this->codes['server_error']);
    }

    protected function responseFailValidationErrors($errors,  $message = 'Validation failed')
    {
        return $this->respond([
            'status'  => 'error',
            'message' => $message,
            'errors'  => $errors
        ], $this->codes['invalid_data']);
    }

    protected function responseFailNotFound(string $message="Not found")
    {
        return $this->respond([
            'status'  => 'error',
            'message' => $message
        ], $this->codes['resource_not_found']);
    }

    protected function responseFailForbidden(string $message="Forbidden")
    {
        return $this->respond([
            'status'  => 'error',
            'message' => $message
        ], $this->codes['forbidden']);
    }

    protected function responseFailUnauthorized(string $message="Unauthorized")
    {
        return $this->respond([
            'status'  => 'error',
            'message' => $message
        ], $this->codes['unauthorized']);
    }

}