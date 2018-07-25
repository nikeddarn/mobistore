<?php
/**
 * Handle storage balance.
 */

namespace App\Http\Support\Balance;


use App\Models\StorageDepartment;

class StorageBalance
{
    /**
     * Get storage department balance for calculate balance of company.
     *
     * @param StorageDepartment $storage
     * @return float
     */
    public function getBalance(StorageDepartment $storage):float
    {
        return $storage->balance;
    }

    /**
     * Add sum to storage department debet balance (decrease balance)
     *
     * @param StorageDepartment $storage
     * @param float $sum
     * @return bool
     */
    public function addToDebitBalance(StorageDepartment $storage, float $sum):bool
    {
        $storage->balance += $sum;

        return $storage->save();
    }

    /**
     * Add sum to storage department credit balance (increase balance)
     *
     * @param StorageDepartment $storage
     * @param float $sum
     * @return bool
     */
    public function addToCreditBalance(StorageDepartment $storage, float $sum):bool
    {
        $storage->balance -= $sum;

        return $storage->save();
    }
}