<?php 

declare(strict_types=1);

namespace BankingAccount\Domain\Transaction\Service;

use BankingAccount\Domain\Account\Entity\IAccount;
use BankingAccount\Domain\Account\Exception\AccountValidateException;
use BankingAccount\Domain\Account\Repository\AccountRepository;
use BankingAccount\Domain\Transaction\Service\ITransactionService;
use BankingAccount\Domain\Transaction\Entity\ITransaction;
use BankingAccount\Domain\Transaction\Entity\Transaction;
use BankingAccount\Domain\Transaction\Exception\TransactionValidateException;
use BankingAccount\Domain\Transaction\Repository\TransactionRepository;
use BankingAccount\Exception\BankingAccountException;
use BankingAccount\Helper\Atm;
use Predis\ClientInterface;

class TransactionService implements ITransactionService
{
    private AccountRepository $accountRepository;

    private TransactionRepository $transactionRepository;

    private ClientInterface $cache;

    /**
     * @codeCoverageIgnore
     */
    public function __construct(AccountRepository $accountRepository, TransactionRepository $transactionRepository, ClientInterface $cache)
    {
        $this->accountRepository = $accountRepository;
        $this->transactionRepository = $transactionRepository;
        $this->cache = $cache;
    }

    /**
     * Method to get the user's bank account transactions result.
     * 
     * @param int $userId
     * @param int $accountId
     *  
     * @return array
     * @throws BankingAccountException
     */
    public function list(int $userId, int $accountId): array
    {
        $cacheKey = sprintf(Transaction::LIST_CACHE_KEY, $userId, $accountId);
        try {

            $account = $this->accountRepository->getByIdAndUserId($accountId, $userId);
            
            $cachedTransactions = $this->cache->get($cacheKey);
            if ($cachedTransactions) {
                return json_decode($cachedTransactions);
            }
            
            $transactions =  $this->transactionRepository->getByAccountId($account);

            $serializable = $this->formatToSerializable($transactions);

            $this->cache->set($cacheKey, json_encode($serializable));

            return $serializable;

        } catch (AccountValidateException | TransactionValidateException $e) {
            $this->cache->del($cacheKey);
            throw new BankingAccountException($e->getMessage(), $e->getCode());
        } 
        
    }

    /**
     * Class to return the serilizable format put in Transaction class.
     * 
     * @param array $accountTransactions
     * @return array
     */
    public function formatToSerializable(array $accountTransactions): array
    {
        foreach ($accountTransactions as $k => $accountTransaction) {
            $transaction = new Transaction();
            $transaction->fill($accountTransaction);
            $accountTransactions[$k] = $transaction;
        }
        
        return $accountTransactions;
    }

    /**
     * Method to get the user's bank account transaction result.
     * 
     * @param int $userId
     * @param int $accountId
     * @param array $fields
     *  
     * @return ITransaction
     * @throws BankingAccountException
     */
    public function create(int $userId, int $accountId, array $fields = []): ITransaction
    {   
        try {
           
            $account = $this->accountRepository->getByIdAndUserId($accountId, $userId);
            
            $fields['account_id'] = $account->getId();
            $transaction = new Transaction();
            $transaction->fill((object) $fields);
            
            switch ($transaction->getOperation()) {
                case $transaction::DEPOSIT_OPERATION:
                    return $this->deposit($account, $transaction);
                    break;
                case $transaction::WITHDRAW_OPERATION:
                    return $this->withdraw($account, $transaction);
                    break;
            }

        } catch (AccountValidateException | TransactionValidateException $e) {
            throw new BankingAccountException($e->getMessage(), $e->getCode());
        } 
    }

    /**
     * Method to get deposit result from repository.
     * 
     * @param IAccount $account
     * @param ITransaction $transaction
     * 
     * @return ITransaction
     */
    public function deposit(IAccount $account, ITransaction $transaction): ITransaction
    {
        $account->increaseBalance($transaction->getAmount());

        $this->transactionRepository->deposit($account, $transaction);

        $cacheKey = sprintf(Transaction::LIST_CACHE_KEY, $account->getUserId(), $account->getId());
        $this->cache->del($cacheKey);

        return $transaction;
    }

    /**
     * Method to get withdrawal result from repository.
     * 
     * @param IAccount $account
     * @param ITransaction $transaction
     * 
     * @return ITransaction
     */
    public function withdraw(IAccount $account, ITransaction $transaction): ITransaction
    {
        $banknotes = Atm::getBanknotes($transaction->getAmount());

        $transaction->setBanknotes($banknotes);

        $account->decreaseBalance($transaction->getAmount());

        $this->transactionRepository->withdraw($account, $transaction);
        
        $cacheKey = sprintf(Transaction::LIST_CACHE_KEY, $account->getUserId(), $account->getId());
        $this->cache->del($cacheKey);
        
        return $transaction;
    }
}
