<?php
/**
 * User's menu badges.
 */

namespace App\Http\Support\Badges;


use App\Contracts\Shop\Delivery\DeliveryStatusInterface;
use App\Contracts\Shop\Invoices\InvoiceStatusInterface;
use App\Contracts\Shop\Invoices\InvoiceTypes;
use App\Models\Invoice;
use App\Models\User;
use App\Models\UserActiveReclamation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class UserBadges
{
    /**
     * @var Invoice
     */
    private $invoice;
    /**
     * @var UserActiveReclamation
     */
    private $userActiveReclamation;

    /**
     * UserBadges constructor.
     * @param Invoice $invoice
     * @param UserActiveReclamation $userActiveReclamation
     */
    public function __construct(Invoice $invoice, UserActiveReclamation $userActiveReclamation)
    {

        $this->invoice = $invoice;
        $this->userActiveReclamation = $userActiveReclamation;
    }

    /**
     * Get user badges.
     *
     * @param User|\Illuminate\Contracts\Auth\Authenticatable $user
     * @return array
     */
    public function getUserBadges($user): array
    {
        $userId = $user->id;

        return [
            'totalNotifications' => $this->getUserNotificationsQuery($user)->count() + $this->getUserInvoiceNotificationsQuery($user)->count(),
            'shipments' => $this->getUserShipmentInvoicesQuery($userId)->count(),
            'orders' => $this->getUserOrderInvoicesQuery($userId)->count(),
            'reclamations' => $this->getUserActiveReclamationQuery($userId)->count(),
            'payments' => $this->getUserPaymentsInvoicesQuery($userId)->count(),
        ];
    }

    /**
     * Get user shipments query builder.
     *
     * @param $user
     * @return Builder
     */
    private function getUserNotificationsQuery($user): Builder
    {
        return $user->unreadNotifications()->getQuery()
            ->where('created_at', '>', Carbon::now()->subDays(config('notifications.show_unread_notification_days')))
            ->whereNull('invoices_id');
    }

    /**
     * Get user shipments query builder.
     *
     * @param $user
     * @return Builder
     */
    private function getUserInvoiceNotificationsQuery($user): Builder
    {
        return $user->unreadNotifications()->getQuery()
            ->whereNotNull('invoices_id')
            ->whereIn('created_at', function ($query) use ($user) {
                $query->selectRaw('MAX(created_at)')
                    ->from('notifications')
                    ->whereNotNull('invoices_id')
                    ->groupBy('invoices_id')
                    ->havingRaw('read_at IS NULL');
            });
    }

    /**
     * Get user shipments query builder.
     *
     * @param int $userId
     * @return Builder
     */
    private function getUserShipmentInvoicesQuery(int $userId): Builder
    {
        return $this->invoice
            ->where('invoice_status_id', InvoiceStatusInterface::PROCESSING)
            ->whereHas('userInvoices', function ($query) use ($userId) {
                $query->where('users_id', $userId);
            })
            ->whereHas('userInvoices.userDelivery', function ($query) {
                $query->where('delivery_status_id', DeliveryStatusInterface::DELIVERING);
            });
    }

    /**
     * Get user orders query builder.
     *
     * @param int $userId
     * @return Builder
     */
    private function getUserOrderInvoicesQuery(int $userId): Builder
    {
        return $this->invoice
            ->where('invoice_status_id', InvoiceStatusInterface::PROCESSING)
            ->whereHas('userInvoices', function ($query) use ($userId) {
                $query->where('users_id', $userId);
            })
            ->whereIn('invoice_types_id', [
                InvoiceTypes::USER_ORDER,
                InvoiceTypes::USER_PRE_ORDER,
                InvoiceTypes::USER_RETURN_ORDER,
            ]);
    }

    /**
     * Get user orders query builder.
     *
     * @param int $userId
     * @return Builder
     */
    private function getUserPaymentsInvoicesQuery(int $userId): Builder
    {
        return $this->invoice
            ->where('invoice_status_id', InvoiceStatusInterface::PROCESSING)
            ->whereHas('userInvoices', function ($query) use ($userId) {
                $query->where('users_id', $userId);
            })
            ->whereIn('invoice_types_id', [
                InvoiceTypes::USER_PAYMENT,
                InvoiceTypes::USER_RETURN_PAYMENT,
            ]);
    }

    /**
     * Get user orders query builder.
     *
     * @param int $userId
     * @return Builder
     */
    private function getUserActiveReclamationQuery(int $userId): Builder
    {
        return $this->userActiveReclamation
            ->where('users_id', $userId);
    }
}