<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;

class DefineUserPriceGroup
{
    /**
     * @var Request
     */
    private $request;

    /**
     * Create the event listener.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Set starting wholesale price group if user registered as wholesale.
     *
     * @param Registered $event
     * @return void
     */
    public function onUserRegister(Registered $event)
    {
        if ($this->request->has('wholesale') && $this->request->get('wholesale') === 'on'){
            $event->user->price_group = config('shop.price.start_wholesale_price_group');
            $event->user->save();
        }
    }
}
