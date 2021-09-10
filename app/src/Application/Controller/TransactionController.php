<?php

namespace BankingAccount\Application\Controller;

use BankingAccount\Domain\Transaction\Service\ITransactionService;
use BankingAccount\Domain\Transaction\Service\TransactionService;
use BankingAccount\Exception\BankingAccountException;
use BankingAccount\Helper\ErrorHandler;
use BankingAccount\Helper\HttpStatus;
use Exception;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @codeCoverageIgnore
 */
class TransactionController extends Controller
{
    private TransactionService $transactionService;

    
    public function __construct(ContainerInterface  $container)
    {
        $this->transactionService = $container->get(ITransactionService::class);
    }
    
    /**
     * Method to handle user's bank account transactions request.
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
            $accountId = $this->getPathVariable($request, 'accountId');

            $statements = $this->transactionService->list($userId, $accountId);
            
            return $this->jsonResponse($response, $statements);

        } catch (BankingAccountException $e) {
            $error = ErrorHandler::handle($e->getCode(), $e->getMessage());
            return $this->jsonResponse($response, $error, $e->getCode());

        } catch (Exception $e) {
            $error = ErrorHandler::handle();
            return $this->jsonResponse($response, $error, 503);
        }
    }

    /**
     * Method to handle user's bank account create transaction request.
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
            
            $accountId = $this->getPathVariable($request, 'accountId');

            $account = $this->transactionService->create($userId, $accountId, $fields);

            $payload = ['message' => 'Transação realizada com sucesso!', 'data' => $account];

            return $this->jsonResponse($response, $payload, HttpStatus::CODE_201);

        } catch (BankingAccountException $e) {
            $error = ErrorHandler::handle($e->getCode(), $e->getMessage());
            return $this->jsonResponse($response, $error, $e->getCode());

        } catch (Exception $e) {
            $error = ErrorHandler::handle();
            return $this->jsonResponse($response, $error, HttpStatus::CODE_503);
        }
    }
}
