<?php

/**
 * User Cart controller.
 */

namespace App\Http\Controllers\Cart;

use App\Http\Support\Invoice\Repository\CartRepository;
use App\Http\Support\Invoices\Handlers\ProductInvoiceHandler;
use App\Http\Support\Price\ProductPrice;
use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CartController extends Controller
{
    /**
     * @var string
     */
    const CART_COOKIE_NAME = 'cart';

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
     * CartController constructor.
     * @param Request $request
     * @param CartRepository $cartRepository
     * @param ProductInvoiceHandler $invoiceHandler
     * @param ProductPrice $productPrice
     */
    public function __construct(Request $request, CartRepository $cartRepository, ProductInvoiceHandler $invoiceHandler, ProductPrice $productPrice)
    {
        $this->request = $request;
        $this->cartRepository = $cartRepository;
        $this->invoiceHandler = $invoiceHandler;
        $this->productPrice = $productPrice;
    }

    public function add(int $productId)
    {
        $userCart = $this->getUserCart();
        $this->invoiceHandler->bindInvoice($userCart);

        $calculatedProductPrice = $this->productPrice->getPriceByProductId($productId);

        if (!$this->invoiceHandler->isProductPresentInCart($productId)) {
            $this->invoiceHandler->addProducts($productId, $calculatedProductPrice);
        }

        var_dump($userCart->invoiceProduct);
    }

    public function remove(int $productId)
    {
        $userCart = $this->getUserCart();
        $this->invoiceHandler->bindInvoice($userCart);
        $this->invoiceHandler->deleteProducts($productId);
        var_dump($userCart->invoiceProduct);
    }

    /**
     * Increase count of product by one.
     *
     * @param $productId
     */
    public function increase(int $productId)
    {
        $userCart = $this->getUserCart();
        $this->invoiceHandler->bindInvoice($userCart);

        $calculatedProductPrice = $this->productPrice->getPriceByProductId($productId);

        $this->invoiceHandler->addProducts($productId, $calculatedProductPrice);
    }

    /**
     * Decrease count of product by one.
     *
     * @param $productId
     */
    public function decrease(int $productId)
    {
        $userCart = $this->getUserCart();
        $this->invoiceHandler->bindInvoice($userCart);

        $this->invoiceHandler->removeProducts($productId);
    }

    /**
     * Set product invoice count.
     */
    public function addCount()
    {
        $this->validate($this->request, [
            'id' => 'required|integer',
            'quantity' => 'required|integer',
        ]);

        $userCart = $this->getUserCart();
        $this->invoiceHandler->bindInvoice($userCart);

        $productId = $this->request->get('id');
        $productQuantity = $this->request->get('quantity');

        $calculatedProductPrice = $this->productPrice->getPriceByProductId($productId);

        $this->invoiceHandler->setProductsCount($productId, $calculatedProductPrice, $productQuantity);
    }

    /**
     * Retrieve or create user cart.
     *
     * @return Invoice
     */
    private function getUserCart():Invoice
    {
        $userCart = $this->retrieveUserCart();

        if (!$userCart){
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
        if (auth('web')->check()){
            return $this->cartRepository->getByUserId(auth('web')->user()->id);
        }elseif ($this->request->hasCookie(self::CART_COOKIE_NAME)){
            $this->cartCookie = $this->request->cookie(self::CART_COOKIE_NAME);
            return $this->cartRepository->getByUserCookie($this->request->cookie(self::CART_COOKIE_NAME));
        }else{
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
        if (auth('web')->check()){
            return $this->cartRepository->createByUserId(auth('web')->user()->id);
        }else{
            $this->cartCookie = str_random(32);
            return $this->cartRepository->createByUserCookie($this->cartCookie);
        }
    }
}
