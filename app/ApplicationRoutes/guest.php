<?php

use App\Controllers\Api\UserAuthController;
use App\Controllers\View\UserAuthControllerView;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// NOTE : jangan dimasukkan ke group, biarkan sendiri disni
$routes->get('api/login/callback', [UserAuthController::class, 'login'],['filter'=>'googleOauthAuthentication']);


$routes->group('api',['filter'=>'authentication'] ,static function ($routes) {

    $routes->post('login', [UserAuthController::class, 'login']);

});

$routes->group('',static function ($routes) {

    $routes->get('login', [UserAuthControllerView::class, 'formLogin'],['as'=>'login_form']);

});