<?php

namespace App\Http\Controllers\Checkout;

use App\Http\Support\Invoice\Repository\CartRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    /**
     * @var string
     */
    const CART_COOKIE_NAME = 'cart';

    /**
     * @var Request
     */
    private $request;
    /**
     * @var CartRepository
     */
    private $cartRepository;

    /**
     * CheckoutController constructor.
     * @param Request $request
     * @param CartRepository $cartRepository
     */
    public function __construct(Request $request, CartRepository $cartRepository)
    {

        $this->request = $request;
        $this->cartRepository = $cartRepository;
    }

    /**
     * @return View
     */
    public function show()
    {
        $userCart = $this->retrieveUserCart();

        if (!$userCart || $userCart){
            return view('content.checkout.index');
        }
    }

    /**
     * Retrieve user cart.
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    private function retrieveUserCart()
    {
        if (auth('web')->check()) {
            return $this->cartRepository->getByUserId(auth('web')->user()->id);
        } elseif ($this->request->hasCookie(self::CART_COOKIE_NAME)) {
            return $this->cartRepository->getByUserCookie($this->cartCookie);
        } else {
            return null;
        }
    }
}
