<?php 

declare(strict_types=1);

namespace BankingAccount\Domain\User\Repository;

use BankingAccount\Domain\Account\Repository\IAccountRepository;
use BankingAccount\Domain\User\Entity\IUser;
use BankingAccount\Domain\User\Exception\UserValidateException;
use BankingAccount\Exception\BankingAccountException;
use BankingAccount\Helper\ErrorHandler;
use BankingAccount\Helper\HttpStatus;
use PDOException;

/**
 * Class responsible for executing queries
 * requested by service.
 */
class UserRepository implements IUserRepository
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
     * Method to get all users from database.
     * 
     * @return array
     * @throws BankingAccountException
     */
    public function getAll(): array
    {
        try {

            $sql = 'SELECT `id`, `name`, `cpf`, `birthdate` FROM `user` ORDER BY `id` ASC';

            $stmt = $this->connection->prepare($sql);

            $stmt->execute();

            return $stmt->fetchAll();

        } catch(PDOException $e) {
            throw new BankingAccountException(ErrorHandler::DEFAULT_MESSAGE, HttpStatus::CODE_503);
        }
    }

    /**
     * Method to get user by id.
     * 
     * @param int $id
     * 
     * @return mixed
     * @throws BankingAccountException|UserValidateException
     */
    public function getById(int $id): mixed
    {
        try {
            
            $sql = 'SELECT `id`, `name`, `cpf`, `birthdate` FROM `user` WHERE `id` = :id';

            $stmt = $this->connection->prepare($sql);
            $stmt->execute([':id' => $id]);

            if (! $stmt->rowCount()) {
                throw new UserValidateException(ErrorHandler::NOT_FOUND_USER_MESSAGE, HttpStatus::CODE_404);
            }
            
            return $stmt->fetch();

        } catch(PDOException $e) {
            throw new BankingAccountException(ErrorHandler::DEFAULT_MESSAGE, HttpStatus::CODE_503);
        }
    }

    /**
     * Method to verify if the CPF already exists.
     * 
     * @param IUser $user
     * 
     * @return bool
     * @throws BankingAccountException
     */
    public function getByCpf(IUser $user): bool
    {
        try {
            
            $sql = 'SELECT 1 FROM `user` WHERE `cpf` = :cpf AND `id` <> :id';
            
            $stmt = $this->connection->prepare($sql);
            $stmt->execute([
                ':cpf' => $user->getCpf(),
                ':id' => (int) $user->getId()
            ]);

            return $stmt->rowCount() ? true : false;

        } catch(PDOException $e) {
            throw new BankingAccountException(ErrorHandler::DEFAULT_MESSAGE, HttpStatus::CODE_503);
        }
    }

    /**
     * Method to search users from database by term.
     * 
     * @param string $term
     * 
     * @return array
     * @throws BankingAccountException
     */
    public function getByTerm(string $term): array
    {
        try {
            
            $sql = 'SELECT `id`, `name`, `cpf`, `birthdate` FROM `user` WHERE `name` LIKE :term OR 
            `cpf` LIKE :term OR `birthdate` LIKE :term ORDER BY `name` ASC';

            $stmt = $this->connection->prepare($sql);

            $stmt->execute([':term' => '%'.$term.'%']);

            return $stmt->fetchAll();

        } catch(PDOException $e) {
            throw new BankingAccountException(ErrorHandler::DEFAULT_MESSAGE, HttpStatus::CODE_503);
        }
    }

    /**
     * Method to insert a new user in database.
     * 
     * @param IUser $user
     * 
     * @return IUser
     * @throws BankingAccountException
     */
    public function create(IUser $user): IUser
    {
        try {
            
            $sql = 'INSERT INTO `user` (`id`, `name`, `cpf`, `birthdate`) VALUES (NULL, :name, :cpf, :birthdate)';

            $stmt = $this->connection->prepare($sql);
            
            $stmt->execute([
                ':name' => $user->getName(),
                ':cpf' => $user->getCpf(),
                ':birthdate' => $user->getBirthdate(),
            ]);

            $lastInsertId = (int) $this->connection->lastInsertId();

            $user->setId($lastInsertId);

            return $user;

        } catch(PDOException $e) {
            throw new BankingAccountException(ErrorHandler::DEFAULT_MESSAGE, HttpStatus::CODE_503);
        }
    }

    /**
     * Method to update an user in database.
     * 
     * @param IUser $user
     * 
     * @return IUser
     * @throws BankingAccountException
     */
    public function update(IUser $user): IUser
    {
        try {
            
            $sql = 'UPDATE `user` SET `name` = :name, `cpf` = :cpf, `birthdate` = :birthdate 
                    WHERE 
                    `id` = :id 
                    LIMIT 1;';

            $stmt = $this->connection->prepare($sql);
            
            $stmt->execute([
                ':name' => $user->getName(),
                ':cpf' => $user->getCpf(),
                ':birthdate' => $user->getBirthdate(),
                ':id' => $user->getId()
            ]);

            return $user;

        } catch(PDOException $e) {
            throw new BankingAccountException(ErrorHandler::DEFAULT_MESSAGE, HttpStatus::CODE_503);
        }
    }

    /**
     * Method to delete an user from database.
     * 
     * @param IUser $user
     * 
     * @return int
     * @throws BankingAccountException
     */
    public function delete(IUser $user): int
    {
        try {

            $accountBalanceNotEmpty = $this->accountRepository->checkUserNotEmptyBalanceAccounts($user);
            
            if ($accountBalanceNotEmpty) {
                $message = ErrorHandler::USER_ACCOUNT_BALANCE_NOT_EMPTY_MESSAGE;
                throw new UserValidateException($message, HttpStatus::CODE_422);
            }
            
            $sql = 'DELETE FROM `user` WHERE  `id` = :id  LIMIT 1;';

            $stmt = $this->connection->prepare($sql);
            
            $stmt->execute([
                ':id' => $user->getId()
            ]);

            unset($user);

            return $stmt->rowCount();

        } catch(PDOException $e) {
            throw new BankingAccountException(ErrorHandler::DEFAULT_MESSAGE, HttpStatus::CODE_503);
        }
    }
}
