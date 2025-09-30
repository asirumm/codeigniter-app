<?php

use App\Controllers\Api\UserAuthController;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->group('api',['filter'=>'authentication'] ,static function ($routes) {

    $routes->post('login', [UserAuthController::class, 'login']);

});