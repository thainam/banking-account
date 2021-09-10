<?php 

declare(strict_types=1);

namespace BankingAccount\Domain\Transaction\Repository;

use BankingAccount\Domain\Account\Entity\IAccount;
use BankingAccount\Domain\Account\Repository\IAccountRepository;
use BankingAccount\Domain\Transaction\Entity\ITransaction;
use BankingAccount\Domain\Transaction\Repository\ITransactionRepository;
use BankingAccount\Exception\BankingAccountException;
use BankingAccount\Helper\ErrorHandler;
use BankingAccount\Helper\HttpStatus;
use DateTime;
use PDOException;

/**
 * Class responsible for executing queries
 * requested by service.
 */
class TransactionRepository implements ITransactionRepository
{
    private \PDO $connection;

    private IAccountRepository $accountRepository;

    /**
     * @codeCoverageIgnore
     */
    public function  __construct(\PDO $connection, IAccountRepository $accountRepository)
    {
        $this->connection = $connection;
        $this->accountRepository = $accountRepository;
    }

    /**
     * Method to get user's bank account transactions by account id.
     * 
     * @param IAccount $account
     * 
     * @return array
     * @throws BankingAccountException
     */
    public function getByAccountId(IAccount $account): array
    {
        try {
            
            $this->connection->beginTransaction();

            $sql = 'SELECT `id`, `account_id`, `operation`, `amount`, `banknotes`, `created_at`
            FROM user_account_transaction WHERE account_id = :account_id ORDER BY created_at ASC';

            $stmt = $this->connection->prepare($sql);
            
            $stmt->execute([
                ':account_id' => $account->getId(),
            ]);

            $this->connection->commit();

            return $stmt->fetchAll();

        } catch(PDOException $e) {
            $this->connection->rollBack();
            throw new BankingAccountException(ErrorHandler::DEFAULT_MESSAGE, HttpStatus::CODE_503);
        }
    }

    /**
     * Method to insert a new deposit transaction in database.
     * 
     * @param IAccount $account
     * @param ITransaction $transaction
     * 
     * @return ITransaction
     * @throws BankingAccountException
     */
    public function deposit(IAccount $account, ITransaction $transaction): ITransaction
    {
        try {

            $this->connection->beginTransaction();

            $this->accountRepository->updateBalance($account);

            $sql = 'INSERT INTO `user_account_transaction` (`id`, `account_id`, `operation`, `amount`, `created_at`) 
            VALUES (NULL, :account_id, :operation, :amount, :created_at)';

            $stmt = $this->connection->prepare($sql);
            
            $createdAt = (new DateTime)->format('Y-m-d H:i:s');

            $stmt->execute([
                ':account_id' => $account->getId(),
                ':operation' => $transaction->getOperation(),
                ':amount' => $transaction->getAmount(),
                ':created_at' => $createdAt
            ]);

            $lastInsertId = (int) $this->connection->lastInsertId();

            $transaction->setId($lastInsertId);
            $transaction->setCreatedAt($createdAt);

            $this->connection->commit();

            return $transaction;

        } catch(PDOException $e) {
            $this->connection->rollBack();
            throw new BankingAccountException(ErrorHandler::DEFAULT_MESSAGE, HttpStatus::CODE_503);
        }
    }

    /**
     * Method to insert a new withdrawal transaction in database.
     * 
     * @param IAccount $account
     * @param ITransaction $transaction
     * 
     * @return ITransaction
     * @throws BankingAccountException
     */
    public function withdraw(IAccount $account, ITransaction $transaction): ITransaction
    {
        try {

            $this->connection->beginTransaction();

            $this->accountRepository->updateBalance($account);

            $sql = 'INSERT INTO `user_account_transaction` (`id`, `account_id`, `operation`, `amount`, `banknotes`, `created_at`) 
            VALUES (NULL, :account_id, :operation, :amount, :banknotes, :created_at)';

            $stmt = $this->connection->prepare($sql);
            
            $createdAt = (new DateTime)->format('Y-m-d H:i:s');

            $stmt->execute([
                ':account_id' => $account->getId(),
                ':operation' => $transaction->getOperation(),
                ':amount' => $transaction->getAmount(),
                ':banknotes' => json_encode($transaction->getBanknotes()),
                ':created_at' => $createdAt,
            ]);

            $lastInsertId = (int) $this->connection->lastInsertId();

            $transaction->setId($lastInsertId);
            $transaction->setCreatedAt($createdAt);
            
            $this->connection->commit();

            return $transaction;

        } catch(\Exception $e) {
            $this->connection->rollBack();
            throw new BankingAccountException(ErrorHandler::DEFAULT_MESSAGE, HttpStatus::CODE_503);
        }
    }
}
