<?php

use BankingAccount\Helper\ErrorHandler;
use BankingAccount\Helper\HttpStatus;
use Selective\BasePath\BasePathMiddleware;
use Slim\App;
use Slim\Middleware\ErrorMiddleware;

return function (App $app) {
    
    $app->addBodyParsingMiddleware();

    
    $app->addRoutingMiddleware();

    $app->add(BasePathMiddleware::class);

    $NotFoundErrorCustomHandler = function (
        Psr\Http\Message\ServerRequestInterface $request,
        \Throwable $exception,
        bool $displayErrorDetails,
        bool $logErrors,
        bool $logErrorDetails
    ) use ($app) {
        $response = $app->getResponseFactory()->createResponse();

        $error = json_encode(ErrorHandler::handle(HttpStatus::CODE_404, 'Route Not Found.'));
        $response->getBody()->write($error);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(HttpStatus::CODE_404);
    };

    $methodNotAllowedErrorCustomHandler = function (
        Psr\Http\Message\ServerRequestInterface $request,
        \Throwable $exception,
        bool $displayErrorDetails,
        bool $logErrors,
        bool $logErrorDetails
    ) use ($app) {
        $response = $app->getResponseFactory()->createResponse();

        $error = json_encode(ErrorHandler::handle(HttpStatus::CODE_405, 'Method Not Allowed.'));
        $response->getBody()->write($error);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(HttpStatus::CODE_404);
    };

    $errorMiddleware = $app->addErrorMiddleware(true, true, true);

    $errorMiddleware->setErrorHandler(Slim\Exception\HttpNotFoundException::class, $NotFoundErrorCustomHandler);

    $errorMiddleware->setErrorHandler(Slim\Exception\HttpMethodNotAllowedException::class, $methodNotAllowedErrorCustomHandler);

    $app->add(ErrorMiddleware::class);
};