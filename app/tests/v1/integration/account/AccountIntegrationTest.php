<?php 

declare(strict_types=1);

namespace Tests\V1\Integration\Account;

use BankingAccount\Domain\Account\Entity\Account;
use BankingAccount\Domain\Transaction\Entity\Transaction;
use BankingAccount\Domain\User\Entity\User;
use Tests\TestCase;

final class AccountIntegrationTest extends TestCase
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
     * @covers \BankingAccount\Application\Controller\AccountController::index
     * @covers \BankingAccount\Domain\Account\Repository\AccountRepository::getAccountByUser
     * @covers \BankingAccount\Domain\Account\Service\AccountService::list
     * @covers \BankingAccount\Application\Controller\Controller::getPathVariable
     * @covers \BankingAccount\Application\Controller\UserController::create
     * @covers \BankingAccount\Domain\Account\Service\AccountService::formatToSerializable
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
     * @covers \BankingAccount\Helper\Cpf::doMask
     * @covers \BankingAccount\Helper\Cpf::validate
     */
    public function testShouldGetAccountByUser(): void
    {
        $randomUser = $this->generateRandomUser();
        
        self::$user->fill($randomUser->data);

        $userId = self::$user->getId();

        $request = $this->createRequest('GET', "/v1/users/{$userId}/accounts");
        $response = $this->app->handle($request);
        $responseArray = json_decode((string) $response->getBody(), true);
        
        $this->assertSame(self::HTTP_STATUS_200, $response->getStatusCode());
        $this->assertIsArray($responseArray);
        $this->assertArrayNotHasKey('error', $responseArray);
    }

    /**
     * @covers \BankingAccount\Domain\Account\Repository\AccountRepository::create
     * @covers \BankingAccount\Domain\Account\Service\AccountService::create
     * @covers \BankingAccount\Application\Controller\AccountController::create
     * @covers \BankingAccount\Domain\Account\Entity\Account::getId
     * @covers \BankingAccount\Domain\Account\Entity\Account::getType
     * @covers \BankingAccount\Domain\Account\Entity\Account::getTypeDesc
     * @covers \BankingAccount\Domain\Account\Entity\Account::getBalance
     * @covers \BankingAccount\Domain\Account\Entity\Account::getBalanceBr
     * @covers \BankingAccount\Domain\Account\Entity\Account::jsonSerialize
     * @covers \BankingAccount\Domain\Account\Entity\Account::fill
     * @covers \BankingAccount\Domain\Account\Entity\Account::setId
     * @covers \BankingAccount\Domain\Account\Entity\Account::setType
     * @covers \BankingAccount\Domain\Account\Entity\Account::setBalance
     * @covers \BankingAccount\Application\Controller\Controller::getPathVariable
     * @covers \BankingAccount\Application\Controller\UserController::create
     * @covers \BankingAccount\Domain\Account\Entity\Account::getUserId
     * @covers \BankingAccount\Domain\Account\Entity\Account::setUserId
     * @covers \BankingAccount\Domain\Account\Repository\AccountRepository::checkExistingAccount
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
     * @covers \BankingAccount\Helper\Cpf::doMask
     * @covers \BankingAccount\Helper\Cpf::validate
     */
    public function testShouldCreateAnAccount(): void
    {
        $randomUser = $this->generateRandomUser();
        
        self::$user->fill($randomUser->data);

        $userId = self::$user->getId();

        $request = $this->createJsonRequest('POST', "/v1/users/{$userId}/accounts", [
            'type' => self::CHECKING_ACCOUNT_TYPE, 
            'balance' => self::BALANCE_VALID, 
        ]);

        $response = $this->app->handle($request);
        $responseObject = json_decode((string) $response->getBody());

        self::$account->fill($responseObject->data);

        $this->assertSame(self::HTTP_STATUS_201, $response->getStatusCode());
    }


    /**
     * @covers \BankingAccount\Domain\Account\Repository\AccountRepository::create
     * @covers \BankingAccount\Domain\Account\Service\AccountService::create
     * @covers \BankingAccount\Application\Controller\AccountController::create
     * @covers \BankingAccount\Domain\Account\Entity\Account::fill
     * @covers \BankingAccount\Domain\Account\Entity\Account::setId
     * @covers \BankingAccount\Domain\Account\Entity\Account::setType
     * @covers \BankingAccount\Domain\Account\Entity\Account::setBalance
     * @covers \BankingAccount\Application\Controller\Controller::getPathVariable
     * @covers \BankingAccount\Application\Controller\UserController::create
     * @covers \BankingAccount\Domain\Account\Entity\Account::getBalance
     * @covers \BankingAccount\Domain\Account\Entity\Account::getBalanceBr
     * @covers \BankingAccount\Domain\Account\Entity\Account::getId
     * @covers \BankingAccount\Domain\Account\Entity\Account::getType
     * @covers \BankingAccount\Domain\Account\Entity\Account::getTypeDesc
     * @covers \BankingAccount\Domain\Account\Entity\Account::getUserId
     * @covers \BankingAccount\Domain\Account\Entity\Account::jsonSerialize
     * @covers \BankingAccount\Domain\Account\Entity\Account::setUserId
     * @covers \BankingAccount\Domain\Account\Repository\AccountRepository::checkExistingAccount
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
     * @covers \BankingAccount\Helper\Cpf::doMask
     * @covers \BankingAccount\Helper\Cpf::validate
     * @covers \BankingAccount\Helper\ErrorHandler::handle
     */
    public function testShouldNotCreateAnAccountWithTheSameType(): void
    {
        $randomUser = $this->generateRandomUser();
        
        self::$user->fill($randomUser->data);

        $userId = self::$user->getId();
        
        $request = $this->createJsonRequest('POST', "/v1/users/{$userId}/accounts", [
            'type' => self::CHECKING_ACCOUNT_TYPE, 
            'balance' => self::BALANCE_VALID, 
        ]);

        $response = $this->app->handle($request);

        $request = $this->createJsonRequest('POST', "/v1/users/{$userId}/accounts", [
            'type' => self::CHECKING_ACCOUNT_TYPE, 
            'balance' => self::BALANCE_VALID, 
        ]);
        
        $response = $this->app->handle($request);
        $responseArray = json_decode((string) $response->getBody(), true);
        

        $this->assertSame(self::HTTP_STATUS_422, $response->getStatusCode());
        $this->assertArrayHasKey('errors', $responseArray);

    }
}
