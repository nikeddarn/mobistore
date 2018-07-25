<?php
/**
 * Handle user balance.
 */

namespace App\Http\Support\Balance;


use App\Models\User;

class UserBalance
{
    /**
     * Get user balance for calculate balance of company.
     *
     * @param User $user
     * @return float
     */
    public function getBalanceForCompany(User $user):float
    {
        return $user->balance;
    }

    /**
     * Get user balance for show it to user.
     *
     * @param User $user
     * @return float
     */
    public function getBalanceForUser(User $user):float
    {
        return $user->balance * -1;
    }

    /**
     * Add sum to user debet balance (decrease balance)
     *
     * @param User $user
     * @param float $sum
     * @return bool
     */
    public function addToDebitBalance(User $user, float $sum):bool
    {
        $user->balance += $sum;

        return $user->save();
    }

    /**
     * Add sum to user credit balance (increase balance)
     *
     * @param User $user
     * @param float $sum
     * @return bool
     */
    public function addToCreditBalance(User $user, float $sum):bool
    {
        $user->balance -= $sum;

        return $user->save();
    }
}