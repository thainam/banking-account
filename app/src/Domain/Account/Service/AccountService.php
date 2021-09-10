<?php 

declare(strict_types=1);

namespace BankingAccount\Domain\Account\Service;

use BankingAccount\Domain\Account\Entity\Account;
use BankingAccount\Domain\Account\Entity\IAccount;
use BankingAccount\Domain\Account\Exception\AccountValidateException;
use BankingAccount\Domain\Account\Repository\AccountRepository;
use BankingAccount\Domain\Account\Service\IAccountService;
use BankingAccount\Domain\User\Entity\User;
use BankingAccount\Domain\User\Exception\UserValidateException;
use BankingAccount\Domain\User\Repository\UserRepository;
use BankingAccount\Exception\BankingAccountException;
use InvalidArgumentException;

class AccountService implements IAccountService
{
    private AccountRepository $accountRepository;

    private UserRepository $userRepository;

    /**
     * @codeCoverageIgnore
     */
    public function __construct(AccountRepository $accountRepository, UserRepository $userRepository)
    {
        $this->accountRepository = $accountRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Method to get list of user's accounts from repository.
     * 
     * @param int $userId
     * 
     * @return array
     * @throws BankingAccountException
     */
    public function list(int $userId): array
    {
        try {

            $userById = $this->userRepository->getById($userId);
            $user = new User();
            $user->fill($userById);
            
            $usersAccounts = $this->accountRepository->getAccountByUser($user);

            return $this->formatToSerializable($usersAccounts);

        } catch (UserValidateException $e) {
            throw new BankingAccountException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Class to return the serilizable format put in Account class.
     * 
     * @param array $usersAccounts
     * @return array
     */
    public function formatToSerializable(array $usersAccounts): array
    {
        foreach ($usersAccounts as $k => $userAccount) {
            $account = new Account();
            $account->fill($userAccount);
            $usersAccounts[$k] = $account;
        }
        
        return $usersAccounts;
    }

    /**
     * Method to get the account create result from repository.
     * 
     * @param int $userId
     * @param array $fields
     *  
     * @return IAccount
     * @throws AccountValidateException
     */
    public function create(int $userId, array $fields = []): IAccount
    {   
        try {
            $fields['user_id'] = $userId;

            $this->userRepository->getById($userId);
            
            $account = new Account();
            $account->fill((object) $fields);

            return $this->accountRepository->create($account);

        } catch (InvalidArgumentException | UserValidateException $e) {
            throw new AccountValidateException($e->getMessage(), $e->getCode());
        }
    }
}
