<?php
/**
 * Balance controller.
 */

namespace App\Http\Controllers\User;


use App\Http\Controllers\Controller;
use App\Http\Support\Invoices\Fabrics\User\Product\UserBalanceInvoiceFabric;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class BalanceController extends Controller
{
    /**
     * @var Request
     */
    private $request;
    /**
     * @var UserBalanceInvoiceFabric
     */
    private $userBalanceInvoiceFabric;

    /**
     * BalanceController constructor.
     * @param Request $request
     * @param UserBalanceInvoiceFabric $userBalanceInvoiceFabric
     */
    public function __construct(Request $request, UserBalanceInvoiceFabric $userBalanceInvoiceFabric)
    {

        $this->request = $request;
        $this->userBalanceInvoiceFabric = $userBalanceInvoiceFabric;
    }

    public function show()
    {
        $user = $this->request->user('web');

        $invoices = $this->userBalanceInvoiceFabric->getRepository()->getInvoices($user->id);

        $perPage = config('shop.user_items_per_page_count.all_items');

        $paginator = $this->createPaginator($invoices, $perPage, route('user_balance.show'));

        $invoicesData = $this->userBalanceInvoiceFabric->getViewer()->getInvoicesData($paginator);

        return view('content.user.balance.index')
            ->with([
                'commonMetaData' => $this->getCommonViewData(),
                'userInvoices' => $invoicesData,
                'userBalance' => $this->getUserBalance($user),
            ]);
    }

    /**
     * Get common view data.
     *
     * @return array
     */
    private function getCommonViewData(): array
    {
        return [
            'title' => trans('meta.title.user.balance'),
        ];
    }

    /**
     * Get user balance.
     *
     * @param $user
     * @return mixed
     */
    private function getUserBalance($user)
    {
        return $this->userBalanceInvoiceFabric->getViewer()->formatUsdPrice($user->balance);
    }

    /**
     * Create paginator.
     *
     * @param Collection $notifications
     * @param int $perPage
     * @param string $urlPath
     * @return LengthAwarePaginator
     */
    private function createPaginator(Collection $notifications, int $perPage, string $urlPath): LengthAwarePaginator
    {
        $currentPage = $this->request->has('page') ? (int)$this->request->get('page') : 1;

        return new LengthAwarePaginator($notifications->forPage($currentPage, $perPage), $notifications->count(), $perPage, $currentPage, [
            'path' => $urlPath,
        ]);
    }
}