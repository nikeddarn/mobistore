<?php

/**
 * User Cart controller.
 */

namespace App\Http\Controllers\Cart;

use App\Http\Support\Invoice\Repository\CartRepository;
use App\Http\Support\Invoices\Handlers\ProductInvoiceHandler;
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
     * CartController constructor.
     * @param Request $request
     * @param CartRepository $cartRepository
     * @param ProductInvoiceHandler $invoiceHandler
     */
    public function __construct(Request $request, CartRepository $cartRepository, ProductInvoiceHandler $invoiceHandler)
    {
        $this->request = $request;
        $this->cartRepository = $cartRepository;
        $this->invoiceHandler = $invoiceHandler;
    }

    public function add($productId)
    {
        $userCart = $this->getUserCart();
        $this->invoiceHandler->bindInvoice($userCart);
        $this->invoiceHandler->addProducts($productId, 23, 1);// ToDo: get price from ProductPrice
        var_dump($userCart->invoiceProduct);
    }

    public function delete($productId)
    {
        $userCart = $this->getUserCart();
        $this->invoiceHandler->bindInvoice($userCart);
        $this->invoiceHandler->deleteProducts($productId);
        var_dump($userCart->invoiceProduct);
    }

    private function getUserCart():Invoice
    {
        $userCart = $this->retrieveUserCart();

        if (!$userCart){
            $userCart = $this->createUserCart();
        }

        return $userCart;
    }

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
