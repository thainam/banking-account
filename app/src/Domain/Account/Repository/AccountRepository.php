<?php 

declare(strict_types=1);

namespace BankingAccount\Domain\Account\Repository;

use BankingAccount\Domain\Account\Entity\Account;
use BankingAccount\Domain\Account\Entity\IAccount;
use BankingAccount\Domain\Account\Exception\AccountValidateException;
use BankingAccount\Domain\User\Entity\IUser;
use BankingAccount\Exception\BankingAccountException;
use BankingAccount\Helper\ErrorHandler;
use BankingAccount\Helper\HttpStatus;
use PDOException;

/**
 * Class responsible for executing queries
 * requested by service.
 */
class AccountRepository implements IAccountRepository
{
    private \PDO $connection;

    /**
     * @codeCoverageIgnore
     */
    public function  __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Method to get all accounts by user from database.
     * 
     * @param IUser $user
     * 
     * @return array
     * @throws BankingAccountException
     */
    public function getAccountByUser(IUser $user): array
    {
        try {

            $sql = 'SELECT `id`, `type`, `balance`, `user_id` FROM `user_account` WHERE `user_id` = :user_id';
            $stmt = $this->connection->prepare($sql);
            $stmt->execute([
                'user_id' => $user->getId()
            ]);

            return $stmt->fetchAll();

        } catch(PDOException $e) {
            throw new BankingAccountException(ErrorHandler::DEFAULT_MESSAGE, HttpStatus::CODE_503);
        }
    }

    /**
     * Method to insert a new account in database.
     * 
     * @param IAccount $account
     * 
     * @return IAccount
     * @throws BankingAccountException
     */
    public function create(IAccount $account): IAccount
    {
        try {

            $this->checkExistingAccount($account);

            $sql = 'INSERT INTO `user_account` (`id`, `user_id`, `type`, `balance`) VALUES (NULL, :user_id, :type, :balance)';

            $stmt = $this->connection->prepare($sql);
            
            $stmt->execute([
                ':user_id' => $account->getUserId(),
                ':type' => $account->getType(),
                ':balance' => $account->getBalance(),
            ]);

            $lastInsertId = (int) $this->connection->lastInsertId();

            $account->setId($lastInsertId);
            return $account;

        } catch(PDOException $e) {
            throw new BankingAccountException(ErrorHandler::DEFAULT_MESSAGE, HttpStatus::CODE_503);
        }
    }

    /**
     * Method to check if the user already has an 
     * account of the type neeeded.
     * 
     * @param IAccount $account
     * @return void
     * @throws AccountValidateException
     */
    public function checkExistingAccount(IAccount $account): void
    {
        $sql = 'SELECT 1 FROM user_account where `user_id` = :user_id AND `type` = :type';

        $stmt = $this->connection->prepare($sql);
        
        $stmt->execute([
            ':user_id' => $account->getUserId(),
            ':type' => $account->getType(),
        ]);

        if ($stmt->rowCount()) {

            $accountType = $account->getType();
            $message = ErrorHandler::ACCOUNT_DUPLICATED_MESSAGE[$accountType];
            
            throw new AccountValidateException($message, HttpStatus::CODE_422);
        }
    }

    /**
     * Method to check account by id and user id.
     * 
     * @param int $id
     * @param int $userId
     * 
     * @return IAccount
     * @throws BankingAccountException|AccountValidateException
     */
    public function getByIdAndUserId(int $id, int $userId): IAccount
    {
        try {
            
            $sql = 'SELECT `id`, `type`, `balance`, `user_id` FROM `user_account` WHERE `id` = :id AND `user_id` = :user_id';

            $stmt = $this->connection->prepare($sql);
            $stmt->execute([
                ':id' => $id,
                ':user_id' => $userId,
            ]);

            if (! $stmt->rowCount()) {
                throw new AccountValidateException(ErrorHandler::ACCOUNT_NOT_FOUND_MESSAGE, HttpStatus::CODE_404);
            }

            $account = new Account();
            $account->fill($stmt->fetch());

            return $account;

        } catch(PDOException $e) {
            throw new BankingAccountException(ErrorHandler::DEFAULT_MESSAGE, HttpStatus::CODE_503);
        }
    }

    /**
     * Method to update users account balance 
     * after some deposit/withdrawal.
     * 
     * @param IAccount $account
     * @return void
     */
    public function updateBalance(IAccount $account): void
    {
        $sql = 'UPDATE user_account SET `balance` = :balance WHERE id = :id AND `user_id` = :user_id LIMIT 1';

        $stmt = $this->connection->prepare($sql);
        
        $stmt->execute([
            ':balance' => $account->getBalance(),
            ':id' => $account->getId(),
            ':user_id' => $account->getUserId(),
        ]);
    }

    /**
     * Method to check in an user has one or more 
     * bank accounts with balance positive.
     * 
     * @param IUser $user
     * @return bool
     */
    public function checkUserNotEmptyBalanceAccounts(IUser $user): bool
    {
        $sql = 'SELECT `id` FROM user_account where `user_id` = :user_id AND `balance` > 0';

        $stmt = $this->connection->prepare($sql);
        
        $stmt->execute([
            ':user_id' => $user->getId(),
        ]);
        // print_r($stmt->rowCount());
        return $stmt->rowCount() ? true : false;
    }
}
