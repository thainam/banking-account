<?php 

declare(strict_types=1);

namespace Tests\V1\Integration\User;

use Tests\RandomCpf;
use Tests\TestCase;

final class UserIntegrationTest extends TestCase
{
    /**
     * @covers \BankingAccount\Application\Controller\UserController::index
     * @covers \BankingAccount\Domain\User\Repository\UserRepository::getAll
     * @covers \BankingAccount\Domain\User\Service\UserService::list
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
     * @covers \BankingAccount\Domain\User\Service\UserService::formatToSerializable
     * @covers \BankingAccount\Helper\Cpf::doMask
     * @covers \BankingAccount\Helper\Cpf::validate
     */
    public function testShouldGetAllUsers(): void
    {
        $request = $this->createRequest('GET', '/v1/users');
        $response = $this->app->handle($request);
        $responseArray = json_decode((string) $response->getBody(), true);
        
        $this->assertSame(self::HTTP_STATUS_200, $response->getStatusCode());
        $this->assertIsArray($responseArray);
        $this->assertArrayNotHasKey('error', $responseArray);
    }

    /**
     * @covers \BankingAccount\Application\Controller\UserController::search
     * @covers \BankingAccount\Domain\User\Repository\UserRepository::getByTerm
     * @covers \BankingAccount\Domain\User\Service\UserService::search
     * @covers \BankingAccount\Domain\User\Service\UserService::formatToSerializable
     * 
     */
    public function testShouldSearchUsers(): void
    {
        $request = $this->createRequest('GET', '/v1/users/search?term=marcela');
        $response = $this->app->handle($request);
        $responseArray = json_decode((string) $response->getBody(), true);
        $this->assertSame(self::HTTP_STATUS_200, $response->getStatusCode());
        $this->assertIsArray($responseArray);
    }

    /**
     * @covers \BankingAccount\Application\Controller\UserController::search
     * @covers \BankingAccount\Domain\User\Service\UserService::search
     * @covers \BankingAccount\Helper\ErrorHandler::handle
     */
    public function testShouldNotSearchUsersWithoutTheTerm(): void
    {
        $request = $this->createRequest('GET', '/v1/users/search');
        $response = $this->app->handle($request);
        $responseArray = json_decode((string) $response->getBody(), true);
        $this->assertSame(self::HTTP_STATUS_422, $response->getStatusCode());
        $this->assertIsArray($responseArray);
    }

    /**
     * @covers \BankingAccount\Domain\User\Repository\UserRepository::create
     * @covers \BankingAccount\Domain\User\Service\UserService::create
     * @covers \BankingAccount\Application\Controller\UserController::create
     * @covers \BankingAccount\Domain\User\Entity\User::getBirthdate
     * @covers \BankingAccount\Domain\User\Entity\User::getBirthdateBr
     * @covers \BankingAccount\Domain\User\Entity\User::getCpf
     * @covers \BankingAccount\Domain\User\Entity\User::getId
     * @covers \BankingAccount\Domain\User\Entity\User::getName
     * @covers \BankingAccount\Domain\User\Entity\User::jsonSerialize
     * @covers \BankingAccount\Domain\User\Entity\User::fill
     * @covers \BankingAccount\Domain\User\Entity\User::setBirthdate
     * @covers \BankingAccount\Domain\User\Entity\User::setCpf
     * @covers \BankingAccount\Domain\User\Entity\User::setId
     * @covers \BankingAccount\Domain\User\Entity\User::setName
     * @covers \BankingAccount\Domain\User\Repository\UserRepository::getByCpf
     * @covers \BankingAccount\Helper\Cpf::validate
     * @covers \BankingAccount\Helper\Cpf::doMask
     * 
     */
    public function testShouldCreatAnUser(): void
    {
        $request = $this->createJsonRequest('POST', '/v1/users', ['name' => 'João', 'cpf' => RandomCpf::generate(), 'birthdate' => '24/05/1976']);
        $response = $this->app->handle($request);
        $this->assertSame(self::HTTP_STATUS_201, $response->getStatusCode());
    }

    /**
     * @covers \BankingAccount\Domain\User\Service\UserService::create
     * @covers \BankingAccount\Application\Controller\UserController::create
     * @covers \BankingAccount\Domain\User\Entity\User::fill
     * @covers \BankingAccount\Domain\User\Entity\User::setBirthdate
     * @covers \BankingAccount\Domain\User\Entity\User::setCpf
     * @covers \BankingAccount\Domain\User\Entity\User::setId
     * @covers \BankingAccount\Domain\User\Entity\User::setName
     * @covers \BankingAccount\Helper\ErrorHandler::handle
     * @covers \BankingAccount\Helper\Cpf::validate
     */
    public function testShouldNotCreatAnUserWhenParametersAreEmpty(): void
    {
        $request = $this->createJsonRequest('POST', '/v1/users', ['name' => '', 'cpf' => '', 'birthdate' => '']);
        $response = $this->app->handle($request);
        $this->assertSame(self::HTTP_STATUS_422, $response->getStatusCode());
    }

    /**
     * @covers \BankingAccount\Domain\User\Repository\UserRepository::update
     * @covers \BankingAccount\Domain\User\Service\UserService::update
     * @covers \BankingAccount\Application\Controller\UserController::update
     * @covers \BankingAccount\Domain\User\Entity\User::getBirthdate
     * @covers \BankingAccount\Domain\User\Entity\User::getBirthdateBr
     * @covers \BankingAccount\Domain\User\Entity\User::getCpf
     * @covers \BankingAccount\Domain\User\Entity\User::getId
     * @covers \BankingAccount\Domain\User\Entity\User::getName
     * @covers \BankingAccount\Domain\User\Entity\User::jsonSerialize
     * @covers \BankingAccount\Domain\User\Entity\User::fill
     * @covers \BankingAccount\Domain\User\Entity\User::setBirthdate
     * @covers \BankingAccount\Domain\User\Entity\User::setCpf
     * @covers \BankingAccount\Domain\User\Entity\User::setId
     * @covers \BankingAccount\Domain\User\Entity\User::setName
     * @covers \BankingAccount\Application\Controller\UserController::index
     * @covers \BankingAccount\Application\Controller\UserController::mapRoute
     * @covers \BankingAccount\Domain\User\Repository\UserRepository::getAll
     * @covers \BankingAccount\Domain\User\Repository\UserRepository::getById
     * @covers \BankingAccount\Domain\User\Service\UserService::list
     * @covers \BankingAccount\Helper\Cpf::doMask
     * @covers \BankingAccount\Domain\User\Repository\UserRepository::getByCpf
     * @covers \BankingAccount\Helper\Cpf::validate
     * @covers \BankingAccount\Application\Controller\Controller::getPathVariable
     * @covers \BankingAccount\Domain\User\Service\UserService::formatToSerializable
     * 
     */
    public function testShouldUpdateAnUser(): void
    {
        $request = $this->createJsonRequest('GET', "/v1/users");
        $response = $this->app->handle($request);
        $responseArray = json_decode((string) $response->getBody(), true);
        $userId = $responseArray[0]['id'] ?? 0;

        $request = $this->createJsonRequest('PUT', "/v1/users/{$userId}", ['name' => 'Pedro Canário', 'cpf' => RandomCpf::generate(), 'birthdate' => '24/05/2000']);
        $response = $this->app->handle($request);
        $responseArray = json_decode((string) $response->getBody(), true);
        $this->assertSame(self::HTTP_STATUS_200, $response->getStatusCode());
        $this->assertArrayHasKey('message', $responseArray);
    }

    /**
     * @covers \BankingAccount\Application\Controller\UserController::index
     * @covers \BankingAccount\Application\Controller\UserController::mapRoute
     * @covers \BankingAccount\Domain\User\Entity\User::fill
     * @covers \BankingAccount\Domain\User\Entity\User::getId
     * @covers \BankingAccount\Domain\User\Entity\User::setBirthdate
     * @covers \BankingAccount\Domain\User\Entity\User::setCpf
     * @covers \BankingAccount\Domain\User\Entity\User::setId
     * @covers \BankingAccount\Domain\User\Entity\User::setName
     * @covers \BankingAccount\Domain\User\Repository\UserRepository::getAll
     * @covers \BankingAccount\Domain\User\Service\UserService::list
     * @covers \BankingAccount\Helper\Cpf::DoMask
     * @covers \BankingAccount\Helper\Cpf::validate
     * @covers \BankingAccount\Domain\User\Repository\UserRepository::delete
     * @covers \BankingAccount\Domain\User\Service\UserService::delete
     * @covers \BankingAccount\Application\Controller\UserController::delete
     * @covers \BankingAccount\Domain\User\Repository\UserRepository::getById
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
     * @covers \BankingAccount\Domain\Account\Repository\AccountRepository::checkUserNotEmptyBalanceAccounts
     * @covers \BankingAccount\Domain\Account\Repository\AccountRepository::create
     * @covers \BankingAccount\Domain\Account\Service\AccountService::create
     * @covers \BankingAccount\Domain\User\Entity\User::getBirthdate
     * @covers \BankingAccount\Domain\User\Entity\User::getBirthdateBr
     * @covers \BankingAccount\Domain\User\Entity\User::getCpf
     * @covers \BankingAccount\Domain\User\Entity\User::getName
     * @covers \BankingAccount\Domain\User\Entity\User::jsonSerialize
     * @covers \BankingAccount\Domain\User\Repository\UserRepository::create
     * @covers \BankingAccount\Domain\User\Repository\UserRepository::getByCpf
     * @covers \BankingAccount\Domain\User\Service\UserService::create
     * @covers \BankingAccount\Helper\ErrorHandler::handle
     * 
     */
    public function testShouldNotDeleteAnUserWhenBalanceIsPositive(): void
    {
        $randomUser = $this->generateRandomUser();
        $userId = $randomUser->data->id;
        
        $this->generateRandomUserBankAccount($userId);

        $request = $this->createJsonRequest('DELETE', "/v1/users/{$userId}");
        $response = $this->app->handle($request);
        $responseArray = json_decode((string) $response->getBody(), true);
        $this->assertSame(self::HTTP_STATUS_422, $response->getStatusCode());
        $this->assertArrayHasKey('errors', $responseArray);
    }

    /**
     * @covers \BankingAccount\Application\Controller\UserController::index
     * @covers \BankingAccount\Application\Controller\UserController::mapRoute
     * @covers \BankingAccount\Domain\User\Entity\User::fill
     * @covers \BankingAccount\Domain\User\Entity\User::getId
     * @covers \BankingAccount\Domain\User\Entity\User::setBirthdate
     * @covers \BankingAccount\Domain\User\Entity\User::setCpf
     * @covers \BankingAccount\Domain\User\Entity\User::setId
     * @covers \BankingAccount\Domain\User\Entity\User::setName
     * @covers \BankingAccount\Domain\User\Repository\UserRepository::getAll
     * @covers \BankingAccount\Domain\User\Service\UserService::list
     * @covers \BankingAccount\Helper\Cpf::DoMask
     * @covers \BankingAccount\Helper\Cpf::validate
     * @covers \BankingAccount\Domain\User\Repository\UserRepository::delete
     * @covers \BankingAccount\Domain\User\Service\UserService::delete
     * @covers \BankingAccount\Application\Controller\UserController::delete
     * @covers \BankingAccount\Domain\User\Repository\UserRepository::getById
     * @covers \BankingAccount\Application\Controller\Controller::getPathVariable
     * @covers \BankingAccount\Application\Controller\UserController::create
     * @covers \BankingAccount\Domain\Account\Repository\AccountRepository::checkUserNotEmptyBalanceAccounts
     * @covers \BankingAccount\Domain\User\Entity\User::getBirthdate
     * @covers \BankingAccount\Domain\User\Entity\User::getBirthdateBr
     * @covers \BankingAccount\Domain\User\Entity\User::getCpf
     * @covers \BankingAccount\Domain\User\Entity\User::getName
     * @covers \BankingAccount\Domain\User\Entity\User::jsonSerialize
     * @covers \BankingAccount\Domain\User\Repository\UserRepository::create
     * @covers \BankingAccount\Domain\User\Repository\UserRepository::getByCpf
     * @covers \BankingAccount\Domain\User\Service\UserService::create
     * 
     */
    public function testShouldDeleteAnUserWhenDoesntHaveAccount(): void
    {
        $randomUser = $this->generateRandomUser();
        $userId = $randomUser->data->id;
        
        $request = $this->createJsonRequest('DELETE', "/v1/users/{$userId}");
        $response = $this->app->handle($request);
        $responseArray = json_decode((string) $response->getBody(), true);
        $this->assertSame(self::HTTP_STATUS_200, $response->getStatusCode());
        $this->assertArrayHasKey('message', $responseArray);
    }
}
