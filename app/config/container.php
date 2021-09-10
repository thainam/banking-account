<?php

use Predis\Client;
use Predis\ClientInterface;
use BankingAccount\Domain\Account\Repository\AccountRepository;
use BankingAccount\Domain\Account\Repository\IAccountRepository;
use BankingAccount\Domain\Account\Service\IAccountService;
use BankingAccount\Domain\User\Repository\UserRepository;
use BankingAccount\Domain\Account\Service\AccountService;
use BankingAccount\Domain\Transaction\Repository\TransactionRepository;
use BankingAccount\Domain\Transaction\Service\ITransactionService;
use BankingAccount\Domain\Transaction\Service\TransactionService;
use BankingAccount\Domain\User\Service\IUserService;
use BankingAccount\Domain\User\Service\UserService;
use Psr\Container\ContainerInterface;
use Selective\BasePath\BasePathMiddleware;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Middleware\ErrorMiddleware;

return [
    'settings' => function () {
        return require __DIR__ . '/settings.php';
    },

    App::class => function (ContainerInterface $container) {
        AppFactory::setContainer($container);

        return AppFactory::create();
    },

    ErrorMiddleware::class => function (ContainerInterface $container) {
        $app = $container->get(App::class);
        $settings = $container->get('settings')['error'];

        return new ErrorMiddleware(
            $app->getCallableResolver(),
            $app->getResponseFactory(),
            (bool)$settings['display_error_details'],
            (bool)$settings['log_errors'],
            (bool)$settings['log_error_details']
        );
    },

    PDO::class => function (ContainerInterface $container) {
        $settings = $container->get('settings')['db'];

        $host = $settings['host'];
        $dbname = $settings['database'];
        $username = $settings['username'];
        $password = $settings['password'];
        $charset = $settings['charset'];
        $flags = $settings['flags'];
        $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

        return new PDO($dsn, $username, $password, $flags);
    },

    ClientInterface::class => function(ContainerInterface $container) {
        $settings = $container->get('settings')['redis'];
        return new Client($settings['server'], $settings['options']);
    },

    BasePathMiddleware::class => function (ContainerInterface $container) {
        return new BasePathMiddleware($container->get(App::class));
    },

    IUserService::class => function (ContainerInterface $container) {
        $pdo = $container->get(PDO::class);
        $accountRepository = new AccountRepository($pdo);
        $userRepository = new UserRepository($pdo, $accountRepository);
        return new UserService($userRepository);
    },

    IAccountRepository::class => function (ContainerInterface $container) {
        $pdo = $container->get(PDO::class);
        return new AccountRepository($pdo);
    },

    IAccountService::class => function (ContainerInterface $container) {
        $pdo = $container->get(PDO::class);
        $accountRepository = $container->get(IAccountRepository::class);
        $userRepository = new UserRepository($pdo, $accountRepository);
        return new AccountService($accountRepository, $userRepository);
    },
    
    ITransactionService::class => function (ContainerInterface $container) {
        $pdo = $container->get(PDO::class);
        $accountRepository = $container->get(IAccountRepository::class);
        $transactionRepository = new TransactionRepository($pdo, $accountRepository);
        $cache = $container->get(ClientInterface::class);
        return new TransactionService($accountRepository, $transactionRepository, $cache);
    },
    
];
