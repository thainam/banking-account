<?php 

declare(strict_types=1);

namespace BankingAccount\Domain\User\Service;

use BankingAccount\Domain\User\Entity\IUser;
use BankingAccount\Domain\User\Entity\User;
use BankingAccount\Domain\User\Exception\UserValidateException;
use BankingAccount\Domain\User\Repository\UserRepository;
use BankingAccount\Exception\BankingAccountException;
use BankingAccount\Helper\ErrorHandler;
use BankingAccount\Helper\HttpStatus;
use InvalidArgumentException;

class UserService implements IUserService
{
    private UserRepository $repository;

    /**
     * @codeCoverageIgnore
     */
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Method to get list of users from repository.
     * 
     * @return array
     */
    public function list(): array
    {
        $users = $this->repository->getAll();

        return $this->formatToSerializable($users);
    }

    /**
     * Method to get search result from repository.
     * 
     * @param string $term
     * 
     * @return array
     * @throws BankingAccountException
     */
    public function search(string $term): array
    {
        $term = filter_var($term, FILTER_SANITIZE_STRING);

        if (empty($term)) {
            throw new BankingAccountException('Você deve informar um termo para ser pesquisado!', HttpStatus::CODE_422);
        }

        $users = $this->repository->getByTerm($term);

        return $this->formatToSerializable($users);
    }

    /**
     * Class to return the serilizable format in User class.
     * 
     * @param array $users
     * @return array
     */
    public function formatToSerializable(array $users): array
    {
        foreach ($users as $k => $user) {
            $userClass = new User();
            $userClass->fill($user);
            $users[$k] = $userClass;
        }
        
        return $users;
    }

    /**
     * Method to get the create result from repository.
     * 
     * @param array $fields
     *  
     * @return IUser
     * @throws UserValidateException
     */
    public function create(array $fields = []): IUser
    {   
        try {

            $user = new User();
            $user->fill((object) $fields);

            if ($this->repository->getByCpf($user)) {
                throw new UserValidateException(ErrorHandler::DUPLICATED_USER_MESSAGE, HttpStatus::CODE_422);
            }
    
            $user = $this->repository->create($user);

            return $user;

        } catch (InvalidArgumentException $e) {
            throw new UserValidateException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Method to get the update result from repository.
     * 
     * @param int $id
     * @param array $fields
     *  
     * @return IUser
     * @throws UserValidateException
     */
    public function update(int $id, array $fields = []): IUser
    {   
        try {
            
            $userById = $this->repository->getById($id);
            
            $user = new User();
            $userInfo = (object) array_merge((array) $userById, $fields);
            $user->fill($userInfo);

            if ($this->repository->getByCpf($user)) {
                throw new UserValidateException(ErrorHandler::DUPLICATED_USER_MESSAGE, HttpStatus::CODE_422);
            }
            $user = $this->repository->update($user);

            return $user;

        } catch (InvalidArgumentException $e) {
            throw new UserValidateException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Method to get the delete result from repository.
     * 
     * @param int $id
     *  
     * @return void
     */
    public function delete(int $id): void
    {   
        $userById = $this->repository->getById($id);
        $user = new User();
        $user->fill($userById);

        $this->repository->delete($user);
    }
}
