<?php

/**
 * Add or Remove product in user favourite.
 */

namespace App\Http\Controllers\Product;

use App\Models\FavouriteProduct;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;

class ProductFavouriteController extends Controller
{
    /**
     * @var FavouriteProduct
     */
    private $favouriteProduct;

    /**
     * @var Request
     */
    private $request;
    /**
     * @var Product
     */
    private $product;

    /**
     * ProductFavouriteController constructor.
     *
     * @param Request $request
     * @param FavouriteProduct $favouriteProduct
     * @param Product $product
     */
    public function __construct(Request $request, FavouriteProduct $favouriteProduct, Product $product)
    {

        $this->favouriteProduct = $favouriteProduct;
        $this->request = $request;
        $this->product = $product;
    }

    /**
     * Add product in user favourite.
     *
     * @param int $productId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addToFavourite(int $productId)
    {
        if ($this->productExists($productId)) {

            $this->favouriteProduct->updateOrCreate([
                'products_id' => $productId,
                'users_id' => auth('web')->user()->id,
            ]);

            $successJson = json_encode([
                'message' => trans('shop.favourite.message.add'),
                'hrefReplace' => '/favourite/remove/' . $productId,
                'title' => trans('shop.favourite.title.remove'),
            ]);

            return $this->redirectBack($successJson);

        }else{
            return $this->redirectBack(false);
        }
    }

    /**
     * Remove product in user favourite.
     *
     * @param int $productId
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function removeFromFavourite(int $productId)
    {
        if ($this->productExists($productId)) {

            $this->favouriteProduct->where([
                ['products_id', $productId],
                ['users_id', auth('web')->user()->id],
            ])->delete();

            $successJson = json_encode([
                'message' => trans('shop.favourite.message.remove'),
                'hrefReplace' => '/favourite/add/' . $productId,
                'title' => trans('shop.favourite.title.add'),
            ]);

            return $this->redirectBack($successJson);

        }else{
            return $this->redirectBack(false);
        }
    }

    /**
     * Is product with given number exist ?
     *
     * @param int $productId
     * @return bool
     */
    private function productExists(int $productId)
    {
        return (bool)$this->product->find($productId);
    }

    /**
     * Redirect back.
     *
     * @param $jsonMessage
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    private function redirectBack($jsonMessage)
    {
        if ($this->request->ajax()){
            return response()->json($jsonMessage);
        }else {
            return redirect()->back();
        }
    }
}
