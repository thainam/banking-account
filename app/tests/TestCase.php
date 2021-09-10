<?php

declare(strict_types=1);

namespace Tests;

use DI\ContainerBuilder;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase as PHPUnit_TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Slim\Psr7\Factory\ServerRequestFactory;
use stdClass;
use UnexpectedValueException;

class TestCase extends PHPUnit_TestCase
{

    const HTTP_STATUS_200 = 200;
    const HTTP_STATUS_201 = 201;
    const HTTP_STATUS_422 = 422;

    const CHECKING_ACCOUNT_TYPE = 'C';
    const CHECKING_ACCOUNT_DESC = 'Conta Corrente';

    const BALANCE_VALID = 10000;
    const BALANCE_INVALID = -31;

    const WITHDRAW_OPERATION = 'W';
    const DEPOSIT_OPERATION = 'D';
    
    const WITHDRAW_OPERATION_DESC = 'Saque';
    const DEPOSIT_OPERATION_DESC = 'Depósito';

    const EMPTY_VALUE = '';
    const NULL_VALUE = null;

     /**
     * @var Container
     */
    protected $container;

    /**
     * @var App
     */
    protected $app;

    /**
     * Bootstrap app.
     *
     * @throws UnexpectedValueException
     *
     * @return void
     */
    protected function setUp(): void
    {

        $containerBuilder = new ContainerBuilder();

        $containerBuilder->addDefinitions(__DIR__ . '/../config/container.php');

        $container = $containerBuilder->build();

        $app = $container->get(App::class);

        (require __DIR__ . '/../config/routes.php')($app);

        (require __DIR__ . '/../config/middleware.php')($app);

        $this->app = $app;

        $container = $this->app->getContainer();
        if ($container === null) {
            throw new UnexpectedValueException('Container must be initialized');
        }

        $this->container = $container;
    }

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
    }

    /**
     * Add mock to container.
     *
     * @param string $class The class or interface
     *
     * @return MockObject The mock
     */
    protected function mock(string $class): MockObject
    {
        if (!class_exists($class)) {
            throw new InvalidArgumentException(sprintf('Class not found: %s', $class));
        }

        $mock = $this->getMockBuilder($class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->container->set($class, $mock);

        return $mock;
    }
    
    /**
     * Create a server request.
     *
     * @param string $method The HTTP method
     * @param string|UriInterface $uri The URI
     * @param array $serverParams The server parameters
     *
     * @return ServerRequestInterface
     */
    protected function createRequest(
        string $method,
        $uri,
        array $serverParams = []
    ): ServerRequestInterface {
        return (new ServerRequestFactory())->createServerRequest($method, $uri, $serverParams);
    }

    /**
     * Create a JSON request.
     *
     * @param string $method The HTTP method
     * @param string|UriInterface $uri The URI
     * @param array|null $data The json data
     *
     * @return ServerRequestInterface
     */
    protected function createJsonRequest(
        string $method,
        $uri,
        array $data = null
    ): ServerRequestInterface {
        $request = $this->createRequest($method, $uri);

        if ($data !== null) {
            $request = $request->withParsedBody($data);
        }

        return $request->withHeader('Content-Type', 'application/json');
    }

    /**
     * Function to generate random user in database
     * to use un subsequent tests.
     * 
     * @return stdClass
     */
    protected function generateRandomUser(): stdClass
    {
        $request = $this->createJsonRequest('POST', '/v1/users', [
            'name' => 'User '.rand(), 
            'cpf' => RandomCpf::generate(), 
            'birthdate' => '10/11/1990'
        ]);
        $response = $this->app->handle($request);
        return json_decode((string) $response->getBody());
    }

    /**
     * Function to generate random user bank account in database
     * to use un subsequent tests.
     * 
     * @return stdClass
     */
    protected function generateRandomUserBankAccount(int $userId): stdClass
    {
        $request = $this->createJsonRequest('POST', "/v1/users/{$userId}/accounts", [
            'type' => self::CHECKING_ACCOUNT_TYPE, 
            'balance' => self::BALANCE_VALID, 
        ]);

        $response = $this->app->handle($request);
        return json_decode((string) $response->getBody());
    }
}