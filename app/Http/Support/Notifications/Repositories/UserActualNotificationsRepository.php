<?php
/**
 * User actual notifications repository.
 */

namespace App\Http\Support\Notifications\Repositories;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class UserActualNotificationsRepository extends UserNotificationsRepository
{
    /**
     * Make notifications query.
     *
     * @param $user
     * @return Builder
     */
    protected function makeQuery($user):Builder
    {
        return $this->getUserUnreadNotificationsQuery(parent::makeQuery($user))
            ->union($this->getUserInvoiceNotificationsQuery(parent::makeQuery($user)));
    }

    /**
     * Get user actual unread notification query builder.
     *
     * @param Builder $query
     * @return Builder
     */
    private function getUserUnreadNotificationsQuery(Builder $query): Builder
    {
        return $query
            ->where('created_at', '>', Carbon::now()->subDays(config('notifications.show_unread_notification_days')))
            ->whereNull('read_at')
            ->whereNull('invoices_id');
    }

    /**
     * Get user last invoice's unread notification query builder.
     *
     * @param Builder $query
     * @return Builder
     */
    private function getUserInvoiceNotificationsQuery(Builder $query): Builder
    {
        return $query
            ->whereNotNull('invoices_id')
            ->whereIn('created_at', function ($query) {
                $query->selectRaw('MAX(created_at)')
                    ->from('notifications')
                    ->whereNotNull('invoices_id')
                    ->groupBy('invoices_id')
                    ->havingRaw('read_at IS NULL');
            });
    }
}