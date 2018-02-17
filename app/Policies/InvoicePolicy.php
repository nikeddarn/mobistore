<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InvoicePolicy
{
    use HandlesAuthorization;

    /**
     * Policy filter.
     *
     * @param $user
     * @param $ability
     * @return bool|null
     */
    public function before($user, $ability)
    {
        if ($user->roles_id !== null) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view the invoice.
     *
     * @param  User  $user
     * @param  Invoice  $invoice
     * @return bool
     */
    public function view(User $user, Invoice $invoice)
    {
        if ($invoice->relationLoaded('userCart')){

            return $invoice->userCart->users_id === $user->id;

        }elseif ($invoice->relationLoaded('userInvoice')){

            return $invoice->userInvoice->users_id === $user->id;

        }elseif ($invoice->relationLoaded('vendorInvoice')){

            return (bool)$invoice->vendorInvoice->vendor()->vendorUser()->where('users_id', $user->id)->first();

        }else{
            return false;
        }
    }

    /**
     * Determine whether the user can create invoices.
     *
     * @param  User  $user
     * @return bool
     */
    public function create(User $user)
    {
        return false;
    }

    /**
     * Determine whether the user can update the invoice.
     *
     * @param  User  $user
     * @param  Invoice  $invoice
     * @return bool
     */
    public function update(User $user, Invoice $invoice)
    {
        return false;
    }

    /**
     * Determine whether the user can delete the invoice.
     *
     * @param  User  $user
     * @param  Invoice  $invoice
     * @return bool
     */
    public function delete(User $user, Invoice $invoice)
    {
        return false;
    }
}
