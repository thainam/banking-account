<?php

declare(strict_types=1);

namespace Tests\V1\Integration\Account;

use BankingAccount\Domain\Account\Entity\Account;
use BankingAccount\Domain\Transaction\Entity\Transaction;
use BankingAccount\Domain\User\Entity\User;
use Tests\TestCase;

final class TransactionIntegrationTest extends TestCase
{
    public static User $user;

    public static Transaction $transaction;

    private static Account $account;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$user = new User();
        self::$account = new Account();
        self::$transaction = new Transaction();
    }

    /**
     * @covers \BankingAccount\Application\Controller\AccountController::create
     * @covers \BankingAccount\Application\Controller\Controller::getPathVariable
     * @covers \BankingAccount\Application\Controller\UserController::create
     * @covers \BankingAccount\Domain\Account\Entity\Account::fill
     * @covers \BankingAccount\Domain\Account\Entity\Account::getBalance
     * @covers \BankingAccount\Domain\Account\Entity\Account::getBalanceBr
     * @covers \BankingAccount\Domain\Account\Entity\Account::getId
     * @covers \BankingAccount\Domain\Account\Entity\Account::getType
     * @covers \BankingAccount\Domain\Account\Entity\Account::getTypeDesc
     * @covers \BankingAccount\Domain\Account\Entity\Account::getUserId
     * @covers \BankingAccount\Domain\Account\Entity\Account::jsonSerialize
     * @covers \BankingAccount\Domain\Account\Entity\Account::setBalance
     * @covers \BankingAccount\Domain\Account\Entity\Account::setId
     * @covers \BankingAccount\Domain\Account\Entity\Account::setType
     * @covers \BankingAccount\Domain\Account\Entity\Account::setUserId
     * @covers \BankingAccount\Domain\Account\Repository\AccountRepository::checkExistingAccount
     * @covers \BankingAccount\Domain\Account\Repository\AccountRepository::create
     * @covers \BankingAccount\Domain\Account\Repository\AccountRepository::getByIdAndUserId
     * @covers \BankingAccount\Domain\Account\Service\AccountService::create
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::fill
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::getAccountId
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::getAmount
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::getAmountBr
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::getBanknotes
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::getCreatedAt
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::getCreatedAtBr
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::getFormattedBanknotes
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::getId
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::getOperation
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::getOperationDesc
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::jsonSerialize
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::setAccountId
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::setAmount
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::setCreatedAt
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::setId
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::setOperation
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::validateDeposit
     * @covers \BankingAccount\Domain\Transaction\Repository\TransactionRepository::getByAccountId
     * @covers \BankingAccount\Domain\Transaction\Service\TransactionService::formatToSerializable
     * @covers \BankingAccount\Domain\Transaction\Service\TransactionService::list
     * @covers \BankingAccount\Domain\User\Entity\User::fill
     * @covers \BankingAccount\Domain\User\Entity\User::getBirthdate
     * @covers \BankingAccount\Domain\User\Entity\User::getBirthdateBr
     * @covers \BankingAccount\Domain\User\Entity\User::getCpf
     * @covers \BankingAccount\Domain\User\Entity\User::getId
     * @covers \BankingAccount\Domain\User\Entity\User::getName
     * @covers \BankingAccount\Domain\User\Entity\User::jsonSerialize
     * @covers \BankingAccount\Domain\User\Entity\User::setBirthdate
     * @covers \BankingAccount\Domain\User\Entity\User::setCpf
     * @covers \BankingAccount\Domain\User\Entity\User::setId
     * @covers \BankingAccount\Domain\User\Entity\User::setName
     * @covers \BankingAccount\Domain\User\Repository\UserRepository::create
     * @covers \BankingAccount\Domain\User\Repository\UserRepository::getByCpf
     * @covers \BankingAccount\Domain\User\Repository\UserRepository::getById
     * @covers \BankingAccount\Domain\User\Service\UserService::create
     * @covers \BankingAccount\Helper\Atm::checkAmountCents
     * @covers \BankingAccount\Helper\Cpf::doMask
     * @covers \BankingAccount\Helper\Cpf::validate
     */
    public function testShouldListTransactions(): void
    {
        $randomUser = $this->generateRandomUser();
        self::$user->fill($randomUser->data);
        $userId = self::$user->getId();

        $randomUserBankAccount = $this->generateRandomUserBankAccount($userId);
        self::$account->fill($randomUserBankAccount->data);
        $accountId = self::$account->getId();
       
        $request = $this->createJsonRequest('GET', "/v1/users/{$userId}/accounts/{$accountId}/transactions", );

        $response = $this->app->handle($request);

        // $responseArray = json_decode((string) $response->getBody(), true);
        
        $this->assertSame(self::HTTP_STATUS_200, $response->getStatusCode());
    }

    /**
     * @covers \BankingAccount\Domain\Transaction\Service\TransactionService::create
     * @covers \BankingAccount\Application\Controller\TransactionController::create
     * @covers \BankingAccount\Application\Controller\AccountController::create
     * @covers \BankingAccount\Application\Controller\Controller::getPathVariable
     * @covers \BankingAccount\Application\Controller\UserController::create
     * @covers \BankingAccount\Domain\Account\Entity\Account::fill
     * @covers \BankingAccount\Domain\Account\Entity\Account::getBalance
     * @covers \BankingAccount\Domain\Account\Entity\Account::getBalanceBr
     * @covers \BankingAccount\Domain\Account\Entity\Account::getId
     * @covers \BankingAccount\Domain\Account\Entity\Account::getType
     * @covers \BankingAccount\Domain\Account\Entity\Account::getTypeDesc
     * @covers \BankingAccount\Domain\Account\Entity\Account::getUserId
     * @covers \BankingAccount\Domain\Account\Entity\Account::jsonSerialize
     * @covers \BankingAccount\Domain\Account\Entity\Account::setBalance
     * @covers \BankingAccount\Domain\Account\Entity\Account::setId
     * @covers \BankingAccount\Domain\Account\Entity\Account::setType
     * @covers \BankingAccount\Domain\Account\Entity\Account::setUserId
     * @covers \BankingAccount\Domain\Account\Repository\AccountRepository::checkExistingAccount
     * @covers \BankingAccount\Domain\Account\Repository\AccountRepository::create
     * @covers \BankingAccount\Domain\Account\Repository\AccountRepository::getByIdAndUserId
     * @covers \BankingAccount\Domain\Account\Service\AccountService::create
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::fill
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::getAmount
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::getOperation
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::setAccountId
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::setAmount
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::setOperation
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::validateWithdraw
     * @covers \BankingAccount\Domain\Transaction\Service\TransactionService::withdraw
     * @covers \BankingAccount\Domain\User\Entity\User::fill
     * @covers \BankingAccount\Domain\User\Entity\User::getBirthdate
     * @covers \BankingAccount\Domain\User\Entity\User::getBirthdateBr
     * @covers \BankingAccount\Domain\User\Entity\User::getCpf
     * @covers \BankingAccount\Domain\User\Entity\User::getId
     * @covers \BankingAccount\Domain\User\Entity\User::getName
     * @covers \BankingAccount\Domain\User\Entity\User::jsonSerialize
     * @covers \BankingAccount\Domain\User\Entity\User::setBirthdate
     * @covers \BankingAccount\Domain\User\Entity\User::setCpf
     * @covers \BankingAccount\Domain\User\Entity\User::setId
     * @covers \BankingAccount\Domain\User\Entity\User::setName
     * @covers \BankingAccount\Domain\User\Repository\UserRepository::create
     * @covers \BankingAccount\Domain\User\Repository\UserRepository::getByCpf
     * @covers \BankingAccount\Domain\User\Repository\UserRepository::getById
     * @covers \BankingAccount\Domain\User\Service\UserService::create
     * @covers \BankingAccount\Helper\Atm::checkAmount
     * @covers \BankingAccount\Helper\Atm::checkAmountCents
     * @covers \BankingAccount\Helper\Atm::checkMinimumAmount
     * @covers \BankingAccount\Helper\Atm::getBanknotes
     * @covers \BankingAccount\Helper\Atm::isSubtractable
     * @covers \BankingAccount\Helper\Atm::subtract
     * @covers \BankingAccount\Helper\Cpf::doMask
     * @covers \BankingAccount\Helper\Cpf::validate
     * @covers \BankingAccount\Helper\ErrorHandler::handle
     */
    public function testShouldNotWithdrawMoneyWhenAmountHasNoAvailableBankNotes(): void
    {
        $randomUser = $this->generateRandomUser();
        self::$user->fill($randomUser->data);
        $userId = self::$user->getId();

        $randomUserBankAccount = $this->generateRandomUserBankAccount($userId);
        self::$account->fill($randomUserBankAccount->data);
        $accountId = self::$account->getId();
       
        $request = $this->createJsonRequest('POST', "/v1/users/{$userId}/accounts/{$accountId}/transactions", [
            'operation' => self::WITHDRAW_OPERATION, 
            'amount' => 30, 
        ]);

        $response = $this->app->handle($request);
        $responseArray = json_decode((string) $response->getBody(), true);
        
        $this->assertSame(self::HTTP_STATUS_422, $response->getStatusCode());
        $this->assertArrayHasKey('errors', $responseArray);
    }

    /**
     * @covers \BankingAccount\Domain\Transaction\Repository\TransactionRepository::withdraw
     * @covers \BankingAccount\Domain\Transaction\Service\TransactionService::create
     * @covers \BankingAccount\Application\Controller\TransactionController::create
     * @covers \BankingAccount\Application\Controller\AccountController::create
     * @covers \BankingAccount\Application\Controller\Controller::getPathVariable
     * @covers \BankingAccount\Application\Controller\UserController::create
     * @covers \BankingAccount\Domain\Account\Entity\Account::decreaseBalance
     * @covers \BankingAccount\Domain\Account\Entity\Account::fill
     * @covers \BankingAccount\Domain\Account\Entity\Account::getBalance
     * @covers \BankingAccount\Domain\Account\Entity\Account::getBalanceBr
     * @covers \BankingAccount\Domain\Account\Entity\Account::getId
     * @covers \BankingAccount\Domain\Account\Entity\Account::getType
     * @covers \BankingAccount\Domain\Account\Entity\Account::getTypeDesc
     * @covers \BankingAccount\Domain\Account\Entity\Account::getUserId
     * @covers \BankingAccount\Domain\Account\Entity\Account::jsonSerialize
     * @covers \BankingAccount\Domain\Account\Entity\Account::setBalance
     * @covers \BankingAccount\Domain\Account\Entity\Account::setId
     * @covers \BankingAccount\Domain\Account\Entity\Account::setType
     * @covers \BankingAccount\Domain\Account\Entity\Account::setUserId
     * @covers \BankingAccount\Domain\Account\Repository\AccountRepository::checkExistingAccount
     * @covers \BankingAccount\Domain\Account\Repository\AccountRepository::create
     * @covers \BankingAccount\Domain\Account\Repository\AccountRepository::getByIdAndUserId
     * @covers \BankingAccount\Domain\Account\Repository\AccountRepository::updateBalance
     * @covers \BankingAccount\Domain\Account\Service\AccountService::create
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::fill
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::getAccountId
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::getAmount
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::getAmountBr
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::getBanknotes
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::getCreatedAt
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::getCreatedAtBr
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::getFormattedBanknotes
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::getId
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::getOperation
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::getOperationDesc
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::jsonSerialize
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::setAccountId
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::setAmount
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::setBanknotes
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::setCreatedAt
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::setId
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::setOperation
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::validateWithdraw
     * @covers \BankingAccount\Domain\Transaction\Service\TransactionService::withdraw
     * @covers \BankingAccount\Domain\User\Entity\User::fill
     * @covers \BankingAccount\Domain\User\Entity\User::getBirthdate
     * @covers \BankingAccount\Domain\User\Entity\User::getBirthdateBr
     * @covers \BankingAccount\Domain\User\Entity\User::getCpf
     * @covers \BankingAccount\Domain\User\Entity\User::getId
     * @covers \BankingAccount\Domain\User\Entity\User::getName
     * @covers \BankingAccount\Domain\User\Entity\User::jsonSerialize
     * @covers \BankingAccount\Domain\User\Entity\User::setBirthdate
     * @covers \BankingAccount\Domain\User\Entity\User::setCpf
     * @covers \BankingAccount\Domain\User\Entity\User::setId
     * @covers \BankingAccount\Domain\User\Entity\User::setName
     * @covers \BankingAccount\Domain\User\Repository\UserRepository::create
     * @covers \BankingAccount\Domain\User\Repository\UserRepository::getByCpf
     * @covers \BankingAccount\Domain\User\Repository\UserRepository::getById
     * @covers \BankingAccount\Domain\User\Service\UserService::create
     * @covers \BankingAccount\Helper\Atm::checkAmount
     * @covers \BankingAccount\Helper\Atm::checkAmountCents
     * @covers \BankingAccount\Helper\Atm::checkMinimumAmount
     * @covers \BankingAccount\Helper\Atm::getBanknotes
     * @covers \BankingAccount\Helper\Atm::isSubtractable
     * @covers \BankingAccount\Helper\Atm::subtract
     * @covers \BankingAccount\Helper\Cpf::doMask
     * @covers \BankingAccount\Helper\Cpf::validate
     */
    public function testShouldWithdrawMoney(): void
    {
        $randomUser = $this->generateRandomUser();
        self::$user->fill($randomUser->data);
        $userId = self::$user->getId();

        $randomUserBankAccount = $this->generateRandomUserBankAccount($userId);
        self::$account->fill($randomUserBankAccount->data);
        $accountId = self::$account->getId();
       
        $request = $this->createJsonRequest('POST', "/v1/users/{$userId}/accounts/{$accountId}/transactions", [
            'operation' => self::WITHDRAW_OPERATION, 
            'amount' => 410, 
        ]);

        $response = $this->app->handle($request);
        
        $this->assertSame(self::HTTP_STATUS_201, $response->getStatusCode());
    }

    /**
     * @covers \BankingAccount\Application\Controller\AccountController::create
     * @covers \BankingAccount\Application\Controller\Controller::getPathVariable
     * @covers \BankingAccount\Application\Controller\UserController::create
     * @covers \BankingAccount\Domain\Account\Entity\Account::fill
     * @covers \BankingAccount\Domain\Account\Entity\Account::getBalance
     * @covers \BankingAccount\Domain\Account\Entity\Account::getBalanceBr
     * @covers \BankingAccount\Domain\Account\Entity\Account::getId
     * @covers \BankingAccount\Domain\Account\Entity\Account::getType
     * @covers \BankingAccount\Domain\Account\Entity\Account::getTypeDesc
     * @covers \BankingAccount\Domain\Account\Entity\Account::getUserId
     * @covers \BankingAccount\Domain\Account\Entity\Account::increaseBalance
     * @covers \BankingAccount\Domain\Account\Entity\Account::jsonSerialize
     * @covers \BankingAccount\Domain\Account\Entity\Account::setBalance
     * @covers \BankingAccount\Domain\Account\Entity\Account::setId
     * @covers \BankingAccount\Domain\Account\Entity\Account::setType
     * @covers \BankingAccount\Domain\Account\Entity\Account::setUserId
     * @covers \BankingAccount\Domain\Account\Repository\AccountRepository::checkExistingAccount
     * @covers \BankingAccount\Domain\Account\Repository\AccountRepository::create
     * @covers \BankingAccount\Domain\Account\Repository\AccountRepository::getByIdAndUserId
     * @covers \BankingAccount\Domain\Account\Repository\AccountRepository::updateBalance
     * @covers \BankingAccount\Domain\Account\Service\AccountService::create
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::fill
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::getAccountId
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::getAmount
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::getAmountBr
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::getBanknotes
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::getCreatedAt
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::getCreatedAtBr
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::getFormattedBanknotes
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::getId
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::getOperation
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::getOperationDesc
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::jsonSerialize
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::setAccountId
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::setAmount
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::setCreatedAt
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::setId
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::setOperation
     * @covers \BankingAccount\Domain\Transaction\Entity\Transaction::validateDeposit
     * @covers \BankingAccount\Domain\Transaction\Repository\TransactionRepository::deposit
     * @covers \BankingAccount\Domain\Transaction\Service\TransactionService::create
     * @covers \BankingAccount\Domain\Transaction\Service\TransactionService::deposit
     * @covers \BankingAccount\Domain\User\Entity\User::fill
     * @covers \BankingAccount\Domain\User\Entity\User::getBirthdate
     * @covers \BankingAccount\Domain\User\Entity\User::getBirthdateBr
     * @covers \BankingAccount\Domain\User\Entity\User::getCpf
     * @covers \BankingAccount\Domain\User\Entity\User::getId
     * @covers \BankingAccount\Domain\User\Entity\User::getName
     * @covers \BankingAccount\Domain\User\Entity\User::jsonSerialize
     * @covers \BankingAccount\Domain\User\Entity\User::setBirthdate
     * @covers \BankingAccount\Domain\User\Entity\User::setCpf
     * @covers \BankingAccount\Domain\User\Entity\User::setId
     * @covers \BankingAccount\Domain\User\Entity\User::setName
     * @covers \BankingAccount\Domain\User\Repository\UserRepository::create
     * @covers \BankingAccount\Domain\User\Repository\UserRepository::getByCpf
     * @covers \BankingAccount\Domain\User\Repository\UserRepository::getById
     * @covers \BankingAccount\Domain\User\Service\UserService::create
     * @covers \BankingAccount\Helper\Atm::checkAmountCents
     * @covers \BankingAccount\Helper\Cpf::doMask
     * @covers \BankingAccount\Helper\Cpf::validate
    
     */
    public function testShouldDepositMoney(): void
    {
        $randomUser = $this->generateRandomUser();
        self::$user->fill($randomUser->data);
        $userId = self::$user->getId();

        $randomUserBankAccount = $this->generateRandomUserBankAccount($userId);
        self::$account->fill($randomUserBankAccount->data);
        $accountId = self::$account->getId();
       
        $request = $this->createJsonRequest('POST', "/v1/users/{$userId}/accounts/{$accountId}/transactions", [
            'operation' => self::DEPOSIT_OPERATION, 
            'amount' => 400, 
        ]);

        $response = $this->app->handle($request);
        
        $this->assertSame(self::HTTP_STATUS_201, $response->getStatusCode());
    }
}
