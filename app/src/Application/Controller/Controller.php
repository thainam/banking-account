<?php 

declare(strict_types=1);

namespace BankingAccount\Application\Controller;

use BankingAccount\Helper\HttpStatus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;

abstract class Controller
{
    const PUT_METHOD = 'PUT';
    const DELETE_METHOD = 'DELETE';

    /**
     * Method to get the path variable value
     * Ex: /users/10 will return -> 10.
     * 
     * @param ServerRequestInterface $request
     * @param string $variableName
     * 
     * @return string|null
     */
    public function getPathVariable(ServerRequestInterface $request, string $variableName): ?string
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        return  $route->getArgument($variableName);
    }

    /**
     * Method to return json response format with http status.
     * 
     * @codeCoverageIgnore
     * 
     * @param Psr\Http\Message\ResponseInterface $response
     * @param array $payload
     * @param int $status
     * 
     * @return Psr\Http\Message\ResponseInterface
     */
    public function jsonResponse(ResponseInterface $response, array $payload, int $status = HttpStatus::CODE_200): ResponseInterface
    {
        $jsonEncodedPayload = json_encode($payload);
        
        $response->getBody()->write($jsonEncodedPayload);

        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }
}
