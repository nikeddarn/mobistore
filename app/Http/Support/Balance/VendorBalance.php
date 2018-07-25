<?php
/**
 * Handle vendor balance.
 */

namespace App\Http\Support\Balance;


use App\Models\Vendor;

class VendorBalance
{
    /**
     * Get user balance for calculate balance of company.
     *
     * @param Vendor $vendor
     * @return float
     */
    public function getBalanceForCompany(Vendor $vendor):float
    {
        return $vendor->balance;
    }

    /**
     * Get user balance for show it to user.
     *
     * @param Vendor $vendor
     * @return float
     */
    public function getBalanceForVendor(Vendor $vendor):float
    {
        return $vendor->balance * -1;
    }

    /**
     * Add sum to user debet balance (decrease balance)
     *
     * @param Vendor $vendor
     * @param float $sum
     * @return bool
     */
    public function addToDebitBalance(Vendor $vendor, float $sum):bool
    {
        $vendor->balance += $sum;

        return $vendor->save();
    }

    /**
     * Add sum to user credit balance (increase balance)
     *
     * @param Vendor $vendor
     * @param float $sum
     * @return bool
     */
    public function addToCreditBalance(Vendor $vendor, float $sum):bool
    {
        $vendor->balance -= $sum;

        return $vendor->save();
    }
}