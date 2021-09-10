<?php

use BankingAccount\Application\Controller\AccountController;
use BankingAccount\Application\Controller\Controller;
use BankingAccount\Application\Controller\TransactionController;
use BankingAccount\Application\Controller\UserController;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;


return function (App $app) {
    $app->redirect('/', 'https://app.swaggerhub.com/apis-docs/thainam/banking-account/1.0.0#/');
    $app->group('/v1', function(RouteCollectorProxy $group) {
        $group->group('/users', function(RouteCollectorProxy $group) {
            $group->get('', [UserController::class, 'index']);
            $group->get('/search', [UserController::class, 'search']);
            $group->post('', [UserController::class, 'create']);
            $group->map([
                Controller::DELETE_METHOD, 
                Controller::PUT_METHOD
            ], '/{id}', [UserController::class, 'mapRoute']);

            $group->group('/{userId}/accounts', function(RouteCollectorProxy $group) {
                $group->get('', [AccountController::class, 'index']);
                $group->post('', [AccountController::class, 'create']);
                $group->group('/{accountId}/transactions', function(RouteCollectorProxy $group) {
                    $group->get('', [TransactionController::class, 'index']);
                    $group->post('', [TransactionController::class, 'create']);
                });
            });
        });
    });
};
