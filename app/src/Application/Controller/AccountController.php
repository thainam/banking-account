<?php

namespace BankingAccount\Application\Controller;

use BankingAccount\Domain\Account\Exception\AccountValidateException;
use BankingAccount\Domain\Account\Service\AccountService;
use BankingAccount\Domain\Account\Service\IAccountService;
use BankingAccount\Exception\BankingAccountException;
use BankingAccount\Helper\ErrorHandler;
use BankingAccount\Helper\HttpStatus;
use Exception;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


class AccountController extends Controller
{
    private AccountService $accountService;

    /**
     * @codeCoverageIgnore
     */
    public function __construct(ContainerInterface  $container)
    {
        $this->accountService = $container->get(IAccountService::class);
    }
    
    /**
     * Method to handle user's bank accounts list request.
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
            $userId = $this->getPathVariable($request, 'userId');

            $allUsersAccounts = $this->accountService->list($userId);

            return $this->jsonResponse($response, $allUsersAccounts);

        } catch (BankingAccountException $e) {
            $error = ErrorHandler::handle($e->getCode(), $e->getMessage());
            return $this->jsonResponse($response, $error, $e->getCode());

        } catch (Exception $e) {
            $error = ErrorHandler::handle();
            return $this->jsonResponse($response, $error, HttpStatus::CODE_503);
        }
    }

    /**
     * Method to handle user's bank account create request.
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
            
            $userId = $this->getPathVariable($request, 'userId');

            $account = $this->accountService->create($userId, $fields);

            $payload = ['message' => 'Conta criada com sucesso!', 'data' => $account];

            return $this->jsonResponse($response, $payload, HttpStatus::CODE_201);

        } catch (AccountValidateException $e) {
            $error = ErrorHandler::handle($e->getCode(), $e->getMessage());
            return $this->jsonResponse($response, $error, $e->getCode());
        } catch (Exception $e) {
            $error = ErrorHandler::handle();
            return $this->jsonResponse($response, $error, HttpStatus::CODE_503);
        }
    }
}
