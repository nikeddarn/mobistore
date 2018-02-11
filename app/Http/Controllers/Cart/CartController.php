<?php

/**
 * User Cart controller.
 */

namespace App\Http\Controllers\Cart;

use App\Http\Support\Invoice\Repository\CartRepository;
use App\Http\Support\Invoices\Handlers\ProductInvoiceHandler;
use App\Http\Support\Price\ProductPrice;
use App\Models\Invoice;
use App\Models\RecentProduct;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class CartController extends Controller
{
    /**
     * @var string
     */
    const CART_COOKIE_NAME = 'cart';

    /**
     * @var string
     */
    const SESSION_REFERRER_NAME = 'cart_referrer';

    /**
     * @var string
     */
    private $cartCookie;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var CartRepository
     */
    private $cartRepository;

    /**
     * @var ProductInvoiceHandler
     */
    private $invoiceHandler;

    /**
     * @var ProductPrice
     */
    private $productPrice;
    /**
     * @var RecentProduct
     */
    private $recentProduct;

    /**
     * CartController constructor.
     * @param Request $request
     * @param CartRepository $cartRepository
     * @param ProductInvoiceHandler $invoiceHandler
     * @param ProductPrice $productPrice
     * @param RecentProduct $recentProduct
     */
    public function __construct(Request $request, CartRepository $cartRepository, ProductInvoiceHandler $invoiceHandler, ProductPrice $productPrice, RecentProduct $recentProduct)
    {
        $this->request = $request;
        $this->cartRepository = $cartRepository;
        $this->invoiceHandler = $invoiceHandler;
        $this->productPrice = $productPrice;
        $this->recentProduct = $recentProduct;
    }

    /**
     * Show user cart.
     *
     * @return View
     * @throws \Exception
     */
    public function show(): View
    {
        $this->bindCartToHandler();

        return $this->createResponse();
    }

    /**
     * Add product to invoice if it is not present in cart.
     *
     * @param int $productId
     * @return Response
     * @throws \Exception
     */
    public function add(int $productId): Response
    {
        $this->bindCartToHandler();

        if (!$this->invoiceHandler->isProductPresentInCart($productId)) {
            $calculatedProductPrice = $this->productPrice->getPriceByProductId($productId);
            $this->invoiceHandler->addProducts($productId, $calculatedProductPrice);
            $this->storeReferrer();
        }

        $this->updateRecentProducts($productId);

        return $this->createRedirect();
    }

    /**
     * Remove product from invoice.
     *
     * @param int $productId
     * @return Response
     * @throws \Exception
     */
    public function remove(int $productId): Response
    {
        $this->bindCartToHandler();

        $this->invoiceHandler->deleteProducts($productId);

        $this->storeReferrer();

        $this->updateRecentProducts($productId);

        return $this->createRedirect();
    }

    /**
     * Add product to invoice if it is not present in cart. Set given from POST parameter quantity of product.
     *
     * @return Response
     * @throws \Exception
     */
    public function setCount(): Response
    {
        $this->validate($this->request, [
            'id' => 'required|integer',
            'quantity' => 'required|integer',
        ]);

        $this->bindCartToHandler();

        $productId = $this->request->get('id');
        $productQuantity = $this->request->get('quantity');

        $calculatedProductPrice = $this->productPrice->getPriceByProductId($productId);

        $this->invoiceHandler->setProductsCount($productId, $calculatedProductPrice, $productQuantity);

        $this->storeReferrer();

        $this->updateRecentProducts($productId);

        return $this->createRedirect();
    }

    /**
     * Retrieve or make new user cart. Bind it to handler. Update exchange rate if cart is not committed.
     *
     * @return void
     * @throws \Exception
     */
    private function bindCartToHandler()
    {
        $userCart = $this->getUserCart();
        $this->invoiceHandler->bindInvoice($userCart);

        if (!$this->invoiceHandler->isInvoiceCommitted() && $userCart->updated_at->timestamp <= Carbon::now()->subDays(config('shop.invoice_exchange_rate_ttl'))->timestamp) {
            $this->invoiceHandler->updateInvoiceExchangeRate();
        }
    }

    /**
     * Retrieve or create user cart.
     *
     * @return Invoice
     * @throws \Exception
     */
    private function getUserCart(): Invoice
    {
        $userCart = $this->retrieveUserCart();

        if (!$userCart) {
            $userCart = $this->createUserCart();
        }

        if ($userCart->updated_at->timestamp <= Carbon::now()->subDays(config('shop.user_cart_ttl'))->timestamp) {
            $this->cartRepository->deleteInvoice($userCart);
            $userCart = $this->createUserCart();
        }

        return $userCart;
    }

    /**
     * Retrieve user cart.
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    private function retrieveUserCart()
    {
        if (auth('web')->check()) {
            $user = auth('web')->user();

            return $this->cartRepository->getByUserId($user->id);
        } elseif ($this->request->hasCookie(self::CART_COOKIE_NAME)) {
            $this->cartCookie = $this->request->cookie(self::CART_COOKIE_NAME);

            return $this->cartRepository->getByUserCookie($this->cartCookie);
        } else {
            return null;
        }
    }

    /**
     * Create user cart.
     *
     * @return Invoice
     */
    private function createUserCart()
    {
        if (auth('web')->check()) {
            return $this->cartRepository->createByUserId(auth('web')->user()->id);
        } else {
            $this->cartCookie = str_random(32);
            return $this->cartRepository->createByUserCookie($this->cartCookie);
        }
    }

    /**
     * Create view with meta data and product data.
     * @return View
     */
    private function createResponse()
    {
        return view('content.cart.shopping_cart.index')
            ->with($this->commonMetaData())
            ->with($this->getInvoiceProducts());
    }

    /**
     * Create array of cart products data.
     *
     * @return array
     */
    private function getInvoiceProducts()
    {
        $productImagePathPrefix = Storage::disk('public')->url('images/products/small/');

        return [
            'productsData' => [
                'products' => $this->invoiceHandler->getFormattedProducts($productImagePathPrefix),
                'invoice_sum' => number_format($this->invoiceHandler->getInvoiceSum(), 2, '.', ','),
                'invoice_uah_sum' => number_format($this->invoiceHandler->getInvoiceUahSum(), 2, '.', ','),
                'back_shopping' => $this->getReferrer(),
                'checkout_form' => route('checkout.show'),
            ]
        ];
    }

    /**
     * Create meta data for the view.
     *
     * @return array
     */
    private function commonMetaData(): array
    {
        return [
            'commonMetaData' => [
                'title' => trans('meta.title.shopping_cart'),
            ],
        ];
    }

    /**
     * Create redirect with cookie
     *
     * @return RedirectResponse
     */
    private function createRedirect()
    {
        $redirect = redirect(route('cart.show'));

        if ($this->cartCookie) {
            $redirect->withCookie(cookie(self::CART_COOKIE_NAME, $this->cartCookie, config('shop.user_cart_ttl') * 1440, '/'));
        }

        return $redirect;
    }

    /**
     * Store http referrer in flash session.
     *
     * @return void
     */
    private function storeReferrer()
    {
        if ($this->request->session()->has(self::SESSION_REFERRER_NAME)) {
            $this->request->session()->keep(self::SESSION_REFERRER_NAME);
        } else {
            $referrer = $this->request->headers->get('referer');
            if (strpos($referrer, 'cart') === false) {
                $this->request->session()->flash(self::SESSION_REFERRER_NAME, $referrer);
            }
        }
    }

    /**
     * Get http referrer from flash session. Reflash it.
     *
     * @return string|null
     */
    private function getReferrer()
    {
        $this->request->session()->keep(self::SESSION_REFERRER_NAME);

        return $this->request->session()->get(self::SESSION_REFERRER_NAME);
    }

    /**
     * Add to recent products
     *
     * @param int $productId
     */
    private function updateRecentProducts(int $productId)
    {
        if (auth('web')->check()) {
            $this->recentProduct->updateOrCreate([
                'products_id' => $productId,
                'users_id' => auth('web')->user()->id,
            ])->touch();
        }
    }
}
