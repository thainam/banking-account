<?php

namespace BankingAccount\Application\Controller;

use BankingAccount\Domain\User\Exception\UserValidateException;
use BankingAccount\Domain\User\Service\IUserService;
use BankingAccount\Domain\User\Service\UserService;
use BankingAccount\Exception\BankingAccountException;
use BankingAccount\Helper\ErrorHandler;
use BankingAccount\Helper\HttpStatus;
use Exception;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UserController extends Controller
{
    private UserService $userService;

    /**
     * @codeCoverageIgnore
     */
    public function __construct(ContainerInterface  $container)
    {
        $this->userService = $container->get(IUserService::class);
    }
    
    /**
     * Method to map routes by method.
     * 
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * 
     * @return ResponseInterface
     */
    public function mapRoute( 
        ServerRequestInterface $request, 
        ResponseInterface $response
    ): ResponseInterface {

        $method = $request->getMethod();
        
        if ($method == Controller::PUT_METHOD) {
            return $this->update($request, $response);
        }

        return $this->delete($request, $response);
    }

    /**
     * Method to handle user list request.
     * 
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * 
     * @return ResponseInterface
     */
    public function index( 
        ServerRequestInterface $request, 
        ResponseInterface $response
    ): ResponseInterface {
        try {

            $allUsers = $this->userService->list();

            return $this->jsonResponse($response, $allUsers);

        } catch (BankingAccountException $e) {

            $error = ErrorHandler::handle($e->getCode(), $e->getMessage());
            return $this->jsonResponse($response, $error, $e->getCode());

        } catch (Exception $e) {

            $error = ErrorHandler::handle();
            return $this->jsonResponse($response, $error, HttpStatus::CODE_503);
        }
    }

    /**
     * Method to handle user search request.
     * 
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * 
     * @return ResponseInterface
     */
    public function search( 
        ServerRequestInterface $request, 
        ResponseInterface $response
    ): ResponseInterface {
        try {

            $params = $request->getQueryParams();
            $term = $params['term'] ?? '';

            $foundUsers = $this->userService->search($term);

            return $this->jsonResponse($response, $foundUsers);

        } catch (BankingAccountException $e) {
            $error = ErrorHandler::handle($e->getCode(), $e->getMessage());
            return $this->jsonResponse($response, $error, $e->getCode());

        } catch (Exception $e) {
            $error = ErrorHandler::handle();
            return $this->jsonResponse($response, $error, HttpStatus::CODE_503);
        }
    }

    /**
     * Method to handle user create request.
     * 
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * 
     * @return ResponseInterface
     */
    public function create( 
        ServerRequestInterface $request, 
        ResponseInterface $response
    ): ResponseInterface {
        try {

            $fields = $request->getParsedBody();

            $user = $this->userService->create($fields);

            $payload = ['message' => 'Usuário inserido com sucesso!', 'data' => $user];

            return $this->jsonResponse($response, $payload, HttpStatus::CODE_201);

        } catch (UserValidateException $e) {
            $error = ErrorHandler::handle($e->getCode(), $e->getMessage());
            return $this->jsonResponse($response, $error, $e->getCode());

        } catch (Exception $e) {
            $error = ErrorHandler::handle();
            return $this->jsonResponse($response, $error, HttpStatus::CODE_503);
        }
    }

    /**
     * Method to handle user update request.
     * 
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * 
     * @return ResponseInterface
     */
    public function update( 
        ServerRequestInterface $request, 
        ResponseInterface $response,
    ): ResponseInterface {
        try {

            $userId = $this->getPathVariable($request, 'id');
            
            $fields = $request->getParsedBody($request);
            
            $user = $this->userService->update($userId, $fields);

            $payload = ['message' => 'Usuário atualizado com sucesso!', 'data' => $user];

            return $this->jsonResponse($response, $payload);

        } catch (UserValidateException $e) {
            $error = ErrorHandler::handle($e->getCode(), $e->getMessage());
            return $this->jsonResponse($response, $error, $e->getCode());

        } catch (Exception $e) {
            $error = ErrorHandler::handle();
            return $this->jsonResponse($response, $error, HttpStatus::CODE_503);
        }
    }

    /**
     * Method to handle user delete request.
     * 
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * 
     * @return ResponseInterface
     */
    public function delete( 
        ServerRequestInterface $request, 
        ResponseInterface $response,
    ): ResponseInterface {
        try {

            $userId = $this->getPathVariable($request, 'id');
            
            $this->userService->delete($userId);

            $payload = ['message' => 'Usuário excluído com sucesso!'];

            return $this->jsonResponse($response, $payload);

        } catch (UserValidateException $e) {
            $error = ErrorHandler::handle($e->getCode(), $e->getMessage());
            return $this->jsonResponse($response, $error, $e->getCode());

        } catch (Exception $e) {
            $error = ErrorHandler::handle($e->getCode(), $e->getMessage());
            return $this->jsonResponse($response, $error, HttpStatus::CODE_503);
        }
    }
}
