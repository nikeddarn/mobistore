<?php

/**
 * User Cart controller.
 */

namespace App\Http\Controllers\Cart;

use App\Contracts\Shop\Invoices\Handlers\ProductInvoiceHandlerInterface;
use App\Http\Support\Invoices\Fabrics\CartInvoiceFabric;
use App\Http\Support\Price\ProductPrice;
use App\Models\RecentProduct;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
    const CART_REFERRER_SESSION_NAME = 'cart_referrer';

    /**
     * @var string
     */
    private $cartCookie;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var ProductPrice
     */
    private $productPrice;

    /**
     * @var RecentProduct
     */
    private $recentProduct;

    /**
     * @var CartInvoiceFabric
     */
    private $cartFabric;

    /**
     * CartController constructor.
     * @param Request $request
     * @param CartInvoiceFabric $invoiceFabric
     * @param ProductPrice $productPrice
     * @param RecentProduct $recentProduct
     */
    public function __construct(
        Request $request,
        CartInvoiceFabric $invoiceFabric,
        ProductPrice $productPrice,
        RecentProduct $recentProduct
    )
    {
        $this->request = $request;
        $this->productPrice = $productPrice;
        $this->recentProduct = $recentProduct;
        $this->cartFabric = $invoiceFabric;
    }

    /**
     * Show user cart.
     *
     * @return View
     * @throws \Exception
     */
    public function show(): View
    {
        $handleableCart = $this->getHandleableCart();

        $response = view('content.cart.shopping_cart.index')
            ->with($this->commonMetaData());

        if ($handleableCart && $handleableCart->getProductsCount()) {
            $response->with($this->getInvoiceProducts($handleableCart));
            $this->request->session()->put('cart_price_warning_shown', true);
        }

        return $response;
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
        $handleableCart = $this->getOrCreateHandleableCart();
        $calculatedProductPrice = $this->productPrice->getUserPriceByProductId($productId);

        if (!$handleableCart->productExists($productId) && $calculatedProductPrice) {
            $handleableCart->appendProducts($productId, $calculatedProductPrice);
        }

        $this->storeReferrer();

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
        $handleableCart = $this->getHandleableCart();

        if ($handleableCart) {
            $handleableCart->deleteProducts($productId);
        }

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

        $handleableCart = $this->getOrCreateHandleableCart();

        $productId = $this->request->get('id');
        $productQuantity = $this->request->get('quantity');

        $calculatedProductPrice = $this->productPrice->getUserPriceByProductId($productId);

        if ($handleableCart && $calculatedProductPrice) {
            $handleableCart->appendProducts($productId, $calculatedProductPrice, $productQuantity);
        }

        $this->storeReferrer();

        $this->updateRecentProducts($productId);

        return $this->createRedirect();
    }

    /**
     * Get cart handler with bound user cart that was retrieved or created anew by user's id or cookie.
     *
     * @return ProductInvoiceHandlerInterface|null
     * @throws \Exception
     */
    private function getOrCreateHandleableCart()
    {
        $cartRepository = $this->cartFabric->getRepository();
        $cartHandler = $this->cartFabric->getHandler();

        if ($cartRepository->cartExists()) {
            if (config('shop.recalculate_cart_prices') && $cartHandler->bindInvoice($cartRepository->getRetrievedInvoice())->getUpdateTime() < Carbon::today()->subDays(1)) {
                $cartHandler->updateProductsPrices();
            }
        } else {
            $cartCreator = $this->cartFabric->getCreator();

            if (auth('web')->check()) {
                $userCart = $cartCreator->createByUserId(auth('web')->user()->id);
            } else {
                $this->cartCookie = str_random(32);
                $userCart = $cartCreator->createByUserCookie($this->cartCookie);
            }

            $cartHandler->bindInvoice($userCart);
        }

        return $cartHandler;
    }

    /**
     * Retrieve user cart. update it if needing.
     *
     * @return ProductInvoiceHandlerInterface|null
     */
    private function getHandleableCart()
    {
        $cartRepository = $this->cartFabric->getRepository();
        $cartHandler = $this->cartFabric->getHandler();

        if ($cartRepository->cartExists()) {
            if (config('shop.recalculate_cart_prices') && $cartHandler->bindInvoice($cartRepository->getRetrievedInvoice())->getUpdateTime() < Carbon::today()->subDays(1)) {
                $cartHandler->updateProductsPrices();
            }
            return $cartHandler;
        } else {
            return null;
        }
    }


    /**
     * Create array of cart products data.
     *
     * @param ProductInvoiceHandlerInterface $handleableCart
     * @return array
     */
    private function getInvoiceProducts(ProductInvoiceHandlerInterface $handleableCart)
    {
        $productImagePathPrefix = Storage::disk('public')->url('images/products/small/');

        return [
            'productsData' => [
                'products' => $handleableCart->getFormattedProducts($handleableCart->getInvoiceProducts(), $productImagePathPrefix),
                'invoice_sum' => number_format($handleableCart->getInvoiceSum(), 2, '.', ','),
                'invoice_uah_sum' => number_format($handleableCart->getInvoiceUahSum(), 2, '.', ','),
                'back_shopping' => $this->getReferrer(),
                'checkout_form' => route('checkout.show'),
                'cart_price_warning' => !$this->request->session()->has('cart_price_warning_shown'),
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
        // redirect to checkout if it's referrer or to show cart otherwise
        if ($this->request->headers->get('referer') === route('checkout.show')) {
            $redirect = redirect(route('checkout.show'));
        } else {
            $redirect = redirect(route('cart.show'));
        }

        // set cart cookie if exists
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
        if ($this->request->session()->has(self::CART_REFERRER_SESSION_NAME)) {
            $this->request->session()->keep(self::CART_REFERRER_SESSION_NAME);
        } else {
            $referrer = $this->request->headers->get('referer');
            if (strpos($referrer, 'cart') === false) {
                $this->request->session()->flash(self::CART_REFERRER_SESSION_NAME, $referrer);
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
        $this->request->session()->keep(self::CART_REFERRER_SESSION_NAME);

        return $this->request->session()->get(self::CART_REFERRER_SESSION_NAME);
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
